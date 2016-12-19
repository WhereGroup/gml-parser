<?php

namespace WhereGroup\GmlParserBundle\Entity;

use Wheregroup\XML\Entity\BaseEntity;

/**
 * Class GmlEntity
 *
 * @package WhereGroup\GmlParserBundle\Entity
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class GmlEntity extends BaseEntity
{

    protected $schemaLocation;

    /** @var WhereGroup\GmlParserBundle\Entity\BBox */
    protected $boundedBy;

    /** @var WhereGroup\GmlParserBundle\Entity\Route[] */
    protected $featureMembers;


    /**
     * @param $data
     */
    protected function setFeatureMembers($data)
    {
        $this->featureMembers = array();
        $isArray              = is_array($data);

        if (!$isArray) {
            return;
        }

        $arrayLength           = count($data);
        $isSingleFeatureMember = $arrayLength == 1;

        if ($isSingleFeatureMember) {
            $this->featureMembers[] = new Route($data);
            return;
        }

        foreach ($data as $k => $route) {
            $this->featureMembers[] = new Route($route);

        }
    }


    /**
     * @param $boundingBox
     */
    protected function filter($boundingBox)
    {

    }


}