<?php

namespace WhereGroup\GmlParserBundle\Entity;

use Wheregroup\XML\Entity\BaseEntity;

/**
 * Class Box
 *
 * @package WhereGroup\GmlParserBundle\Entity
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class BBox extends BaseEntity
{

    protected $srsName;
    protected $coordinates;


    /**
     * @return string
     */
    public function getSRID()
    {
        $parts = explode(':', $this->srsName);
        $srid  = end($parts);
        return $srid;
    }

    /**
     * @param $box
     */
    protected function setBox($box)
    {
        if (isset($box['@attributes'])) {
            $this->fill($box['@attributes']);
        }
        $this->fill($box);
    }


}