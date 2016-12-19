<?php

namespace WhereGroup\GmlParserBundle\Tests;

use Doctrine\DBAL\Driver\Connection;
use Mapbender\DataSourceBundle\Tests\SymfonyTest;
use Mapbender\DigitizerBundle\Entity\FeatureType;
use Symfony\Component\HttpKernel\KernelInterface;
use WhereGroup\GmlParserBundle\Controller\WarngebieteController;
use WhereGroup\GmlParserBundle\Entity\Rect;

/**
 * Class GmlFeatureMembersControllerTest
 *
 * @package WhereGroup\GmlParserBundle\Tests
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class GmlFeatureMembersControllerTest extends SymfonyTest
{


    /*
     *   'host': '93.89.10.144',
     * 'database': 'mapbender',
     * 'user': 'mapbender',
     * 'password': '&see5Toxu?',
     * 'table': 'trlp_warngebiete'
     */

    /**
     * @return KernelInterface
     */
    public static function getKernel(array $options = array())
    {

        return new \AppKernel(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }


    public function testLineStringInterpolation()
    {
        $distance = 1.0;

    }

    /**
     * Example:
     * SELECT
     * ST_AsText (
     * ST_Union (
     * ARRAY [
     * ST_GeomFromText ('LINESTRING(1 2, 3 4)'),
     * ST_GeomFromText ('LINESTRING(3 4, 4 5)'),
     * ST_GeomFromText ('LINESTRING(3 4, 4 6)')
     * ]
     * )
     * ) AS WKT;
     */
    public function testFetchFeatureMembers()
    {

        $warngebieteController = new WarngebieteController(static::$kernel->getContainer());
        $this->createClient(array( ));
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

            $coordinate           = $this->getCoordinatesFromPoint(array_shift($interpolatedPoint));
            $points["features"][] = array(

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


}