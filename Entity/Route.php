<?php

namespace WhereGroup\GmlParserBundle\Entity;

use Wheregroup\XML\Entity\BaseEntity;

/**
 * Class Route
 *
 * @package WhereGroup\GmlParserBundle\Entity
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class Route extends BaseEntity
{

    protected $fid;

    protected $code;

    protected $exception;

    /** @var WhereGroup\GmlParserBundle\Entity\BBox */
    protected $boundedBy;

    protected $ortslage;

    protected $ortsLageLabel;

    protected $rlp;

    protected $verkehr;


    /**
     * @param $data
     * @return $this
     */
    protected function setROUTE($data)
    {
        if ($data) {
            if (isset($data['@attributes'])) {
                $this->fill($data['@attributes']);
            }
            $this->fill($data);
        }
        return $this;
    }

    /**
     * @param mixed $verkehr
     * @return Route
     */
    public function setVerkehr($verkehr)
    {
        $this->verkehr = $verkehr;
        return $this;
    }

    /**
     * @param mixed $ortslageLabel
     * @return Route
     */
    public function setOrtslage_label($ortsLageLabel)
    {
        $this->ortsLageLabel = $ortsLageLabel;
        return $this;
    }


}