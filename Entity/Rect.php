<?php

namespace WhereGroup\GmlParserBundle\Entity;

/**
 * Class Rect
 *
 * @package WhereGroup\GmlParserBundle\Entity
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class Rect
{

    /**
     * Rect constructor.
     *
     * @param string $coordinates
     * @param string $delimiter
     *
     * Real example of how $coordinates could look like :
     * 2538281.5301242,5559380.75408,2598412.4698758,5625932.24592
     */
    public function __construct($coordinates, $delimiter = ",", $srid = "31466")
    {

        if ($coordinates == null) {
            throw new \RuntimeException("Coordinates has to be non-null value!");
        }

        list($this->minX, $this->minY, $this->maxX, $this->maxY) = explode($delimiter, $coordinates);

        $this->check();
        $this->originalCoordinates = $coordinates;
        $this->srid                = $srid;
    }

    /** @var double $minX */
    protected $minX = 0.0;

    /** @var double $maxX */
    protected $maxX = 0.0;

    /** @var double $minY */
    protected $minY = 0.0;

    /** @var double $maxY */
    protected $maxY = 0.0;

    /** @var string $originalCoordinates */
    protected $originalCoordinates;

    /** @var string $srid */
    protected $srid;


    /**
     *
     * Analog to shapely function :
     * def box(minx, miny, maxx, maxy, ccw=True):
     * """Returns a rectangular polygon with configurable normal vector"""
     * coords = [(maxx, miny), (maxx, maxy), (minx, maxy), (minx, miny)]
     * if not ccw:
     * coords = coords[::-1]
     * return Polygon(coords)
     *
     * @return string
     */
    public function getGeometry()
    {
        return "POLYGON(($this->maxX $this->minY,$this->maxX $this->maxY,$this->minX $this->maxY,$this->minX $this->minY,$this->maxX $this->minY))";
    }

    private function check()
    {
        $isIllegalRect = $this->minX > $this->maxX
            || $this->minY > $this->maxY;

        if ($isIllegalRect) {
            throw new \RuntimeException("Illegal coordinates!");
        }
    }


}