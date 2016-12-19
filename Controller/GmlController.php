<?php

namespace WhereGroup\GmlParserBundle\Controller;

use WhereGroup\GmlParserBundle\Entity\Rect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GmlController
 *
 * @package WhereGroup\GmlParserBundle\Controller
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 * @Route("/warngebiete/")
 */
class GmlController extends Controller
{


    /**
     *
     * def load_gml(gml, bbox):
     * """
     * Load route GML into single geometry object
     * """
     * route_features = fiona.open(gml).filter(bbox=bbox)
     *
     * lines = []
     * for feature in route_features:
     * feature_geom = shapely.geometry.shape(feature['geometry'])
     * if isinstance(feature_geom, shapely.geometry.MultiLineString):
     * lines.extend(feature_geom.geoms)
     * else:
     * lines.append(feature_geom)
     *
     * if len(lines) > 0:
     * return shapely.ops.linemerge(lines)
     * else:
     * return shapely.geometry.MultiLineString()
     *
     * @param $gml
     * @Route("/{gml}.{_format}",
     *         defaults={ "_format": "gml" },
     *         requirements={ "_format": "gfs|gml|kml|gpx|json|xml" }
     */
    public function loadGml($gml, $_format)
    {
        $features = array();
        $type     = "FeatureCollection";
        $result   = array("features" => $features,
                          "type"     => $type);

        $request    = $this->getRequest();
        $resolution = $request->query->get('resolution');
        $bbox       = new Rect($request->query->get('bbox'));

        if ($resolution == null) {
            return $result;
        }



        $this->where($bbox->generateQueryCondition());

        return $result;
    }


    private function intersect($a, $b)
    {
        return "st_setsrid( $a, $b)";

    }

    private function setSrid($a, $srid = "31466")
    {
        return "st_setsrid( $a, $srid)";
    }

    private function select($fields, $from)
    {
        $fieldsString = $fields == null ? "*" : join(" , ", $fields);
        return "select " . $fieldsString . " from " . $from;
    }


    private function where($fields, $modus = " AND ")
    {

        return $fields == null ? "" : " where " . join($modus, $fields);
    }

}