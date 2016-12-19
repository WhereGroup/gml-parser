<?php

namespace WhereGroup\GmlParserBundle\Controller;

use Mapbender\DigitizerBundle\Entity\FeatureType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use WhereGroup\GmlParserBundle\Entity\Rect;

/**
 * Class WarngebieteController
 *
 * @package WhereGroup\GmlParserBundle\Controller
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 * @Route("/warngebiete/")
 */
class WarngebieteController extends Controller
{


    //TODO: add check that gml is a valid numerical literal to prevent SQL Injections
    const TRLP_WARNGEBIETE         = "trlp_warngebiete";
    const TRLP_GEOMETRY_FIELD_NAME = "geom";
    const TRKP_PRIMARY_KEY         = "id";
    const DEFAULT_SRID             = "31466";
    const DEFAULT_CONNECTION_NAME  = "features";

    /**@var string */
    protected $connectionName;
    /**@var string */
    protected $srid;

    /**@var string */
    protected $geometryField;

    /**
     * WarngebieteController constructor.
     */
    public function __construct(ContainerInterface $containerInterface)
    {

        $this->geometryField  = "wkb_geometry";
        $this->connectionName = self::DEFAULT_CONNECTION_NAME;
        $this->srid           = self::DEFAULT_SRID;

        $this->setContainer($containerInterface);
    }


    /**
     * Examplerequest:
     *
     * http://radwanderland.de/warngebiete/6101100.gml?resolution=61.736077773629106&bbox=2549394.0241234,5556911.310969,2587299.9758766,5628401.689031
     * @param $gml
     * @Route("/{gml}.{_format}",
     *         defaults={ "_format": "gml" },
     *         requirements={ "_format": "gml" }
     * @return array
     */
    public function getWarngebiete($gml, $_format)
    {
        $request    = $this->getRequest();
        $resolution = $request->get("resolution");
        $rectBounds = $request->get("bbox");

        $rect        = new Rect($rectBounds);
        $boundingBox = $rect->getGeometry();
        $tableName   = "trlp_$gml";

        $points = array("features" => array(), "type" => "FeatureCollection");

        $featureTypeArgs            = $this->getFeatureTypeArgs($tableName);
        $warngebieteFeatureTypeArgs = $this->getFeatureTypeArgs(self::TRLP_WARNGEBIETE, self::TRKP_PRIMARY_KEY, self::TRLP_GEOMETRY_FIELD_NAME);
        $featureType                = new FeatureType($this->container, $featureTypeArgs);
        $warngebieteFeatureType     = new FeatureType($this->container, $warngebieteFeatureTypeArgs);

        $connection = $featureType->getDriver()->getConnection();
        $features   = $featureType->search(array("intersectGeometry" => $boundingBox));

        try {
            $route = $this->mergeLines($connection, $features, $points);
        } catch (Exception $exception) {
            return $points;
        }
        $routeEntity = \geoPHP::load($route);

        $warngebiete = $warngebieteFeatureType->search(array("intersectGeometry" => $boundingBox));

        foreach ($warngebiete as $key => $warngebiet) {

            $repeat                   = $warngebiet->getAttribute("repeat") * $resolution;
            $warngebietGeometry       = $warngebiet->getGeom();
            $warngebietGeometryEntity = \geoPHP::load($warngebietGeometry);
            $line                     = $routeEntity->intersection($warngebietGeometryEntity);

            $isParsingSuccessful = $line instanceof \GeometryCollection;

            if (!$isParsingSuccessful || !($length = $line->length())) {
                continue;
            }

            $halfLength         = $length / 2;
            $linePoints         = $line->asText();
            $interpolatedPoints = $this->interpolate($connection, $linePoints, $this->getDistances($repeat, $halfLength));
            $this->createFeatures($points, $interpolatedPoints, $warngebiet);
        }

        return $points;

    }


    /**
     * @param $connection
     * @param $features
     * @return mixed
     */
    private function mergeLines($connection, $features, &$points)
    {
        $queryParts = array();

        while (!empty($features)) {
            $feature        = array_shift($features);
            $quotedGeometry = $connection->quote($feature->getGeom());
            $geometry       = "ST_GeomFromText( " . $quotedGeometry . ")";
            $queryParts[]   = $geometry;
        }

        $explodedQueryParts = implode(",", $queryParts);

        $query = "Select ST_ASTEXT(ST_UNION( ARRAY[ $explodedQueryParts] ) ) AS route";

        $mergedLines = $connection->fetchAll($query);

        if (!isset($mergedLines[0])) {
            throw new Exception("Merge exception !");
        }

        return $mergedLines[0]["route"];
    }

    /**
     * @param $repeat
     * @param $halfLength
     */
    private function getDistances($repeat, $halfLength)
    {
        $distances = array();

        for ($i = $repeat; $i < $halfLength; $i += $repeat) {
            for ($distance = $halfLength + $i; $distance > $halfLength - $i; $distance--) {
                $distances[] = $distance;

            }
        }
        $distances[] = $halfLength;
        return $distances;
    }

    /**
     * @param Connection $connection
     * @param            $lineString
     * @param            $distance
     * @return mixed
     */
    private function interpolate($connection, $lineString, $distances)
    {

        $lineString = $connection->quote($lineString);

        $query = "SELECT ";
        foreach ($distances as $key => $distance) {
            $distanceKey  = '"$key"';
            $queryParts[] = "ST_AsEWKT(ST_Line_Interpolate_Point($lineString,$distance/  ST_length(ST_AsEWKT($lineString)))) AS $distanceKey";

        }

        return $connection->fetchAll($query . implode(",", $queryParts));

    }


    /* points.append({
      * 'type': 'Feature',
      * 'geometry': {
     * 'type': 'Point',
      * 'coordinates': [point.x, point.y]
     * }, 'properties': {
     * 'tooltip': warngebiet.tooltip,
      * 'category': warngebiet.category
     * }})*/

    /**
     * @param array $points
     * @param array $interpolatedPoints
     */
    private function createFeatures(&$points, $interpolatedPoints, $warngebiet)
    {

        $category = $warngebiet->getAttribute("category");
        $tooltip  = $warngebiet->getAttribute("tooltip");

        $properties = array(
            "category" => $category,
            "tooltip"  => $tooltip,
        );

        foreach ($interpolatedPoints as $key => $interpolatedPoint) {

            $coordinate                                = $this->getCoordinatesFromPoint(array_shift($interpolatedPoint));
            $points[ self::DEFAULT_CONNECTION_NAME ][] = array(

                "properties"  => $properties,
                "type"        => "Feature",
                "coordinates" => $coordinate

            );
        }
    }

    /**
     * Example : POINT(-71.1607113337757 42.2590262089386)
     *
     * @param $interpolatedPoint
     */
    private function getCoordinatesFromPoint($interpolatedPoint)
    {

        $coordinates  = array();
        $index        = strpos($interpolatedPoint, "POINT");
        $isValidPoint = is_string($interpolatedPoint) && $index !== false;

        if ($isValidPoint) {
            $beginIndex             = strpos($interpolatedPoint, "(") + 1;
            $endIndex               = strpos($interpolatedPoint, ")") - 1;
            $unprocessedCoordinates = substr($interpolatedPoint, $beginIndex, $endIndex);
            $coordinates            = explode(" ", $unprocessedCoordinates);
        }

        return $coordinates;
    }

    /**
     * @param $geometryField
     * @param $connectionName
     * @param $tableName
     * @param $srid
     * @return array
     */
    private function getFeatureTypeArgs($tableName, $uniqueId = "ogc_fid", $geometryField = null, $connectionName = null, $srid = null)
    {
        $featureTypeArgs = array(
            "table"      => $tableName,
            "uniqueId"   => $uniqueId,
            "geomField"  => !$geometryField ? $this->geometryField : $geometryField,
            "connection" => !$connectionName ? $this->connectionName : $connectionName,
            "srid"       => !$srid ? $this->srid : $srid

        );
        return $featureTypeArgs;
    }

}