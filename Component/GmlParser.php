<?php

namespace WhereGroup\GmlParserBundle\Component;

/**
 * Class GmlParser
 *
 * @package WhereGroup\GmlParserBundle\Component
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class GmlParser
{
    /**
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
     */

    protected function loadGml($gml, $bbox)
    {

    }

}