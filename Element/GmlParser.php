<?php

namespace WhereGroup\GmlParserBundle\Element;

use Mapbender\CoreBundle\Component\Element;

/**
 * Class GmlParser
 *
 * @package WhereGroup\GmlParserBundle\Element
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class GmlParser extends Element
{
    /** @var string Element title */
    protected static $title = 'GmlParser';

    /** @var string Element description */
    protected static $description = 'GmlParser element';


    /** @inheritdoc
    Rootpath is /gml-parser/Resources/public
     */
    static public function listAssets()
    {
        return array(
            'js'    =>
                array(),
            'css'   => array(),
            'trans' => array());
    }

    /** @inheritdoc */
    public static function getDefaultConfiguration()
    {
        return array();
    }

    /**
     * @inheritdoc
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function getConfiguration()
    {
        $configuration = parent::getConfiguration();

        return $configuration;
    }


    /**
     * Remove given fields
     *
     * @param $data
     * @param $fields
     * @return mixed
     */
    protected function filterFields($data, $fields)
    {
        foreach ($fields as $deniedFieldName) {
            if (isset($data[ $deniedFieldName ])) {
                unset($data[ $deniedFieldName ]);
            }
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function httpAction($action)
    {
        /** @var $requestService Request */
        $request = $this->getRequestData();

        return parent::httpAction($action);
    }


}