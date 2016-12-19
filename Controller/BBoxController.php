<?php

namespace WhereGroup\GmlParserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use WhereGroup\GmlParserBundle\Entity\Rect;

/**
 * Class BBoxController
 *
 * @package WhereGroup\GmlParserBundle\Controller
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class BBoxController extends Controller
{


    public function getFeaturesByBBox($bbox, $featureType,$delimiter =",",$srid="34166")
    {

        $rect        = new Rect($bbox,$delimiter,$srid);
        $boundingBox = $rect->getGeometry();

        return $featureType->search(array("intersectGeometry" => $boundingBox));
    }
}