<?php
namespace WhereGroup\GmlParserBundle;

/**
 * Class WhereGroupGmlParserBundle
 *
 * @package Mapbender\WhereGroupGmlParserBundle
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class WhereGroupGmlParserBundle extends MapbenderBundle
{
    /**
     * @inheritdoc
     */
    public function getElements()
    {
        return array(
            'WhereGroup\GmlParserBundle\Element\GmlParser'
        );
    }
}
