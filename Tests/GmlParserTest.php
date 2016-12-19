<?php

namespace WhereGroup\GmlParserBundle\Tests;

use PHPUnit_Framework_TestCase;
use WhereGroup\GmlParserBundle\Entity\FeatureCollection;
use WhereGroup\GmlParserBundle\Entity\GmlEntity;

use Wheregroup\XML\Util\Parser;

/**
 * Class GmlParserTest
 *
 * @package WhereGroup\GmlParserBundle\Tests
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class GmlParserTest extends PHPUnit_Framework_TestCase
{


    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function endswith($haystack, $needle)
    {
        $haystackLength = strlen($haystack);
        $needleLength   = strlen($needle);
        if ($needleLength > $haystackLength) {
            return false;
        }
        return substr_compare($haystack, $needle, $haystackLength - $needleLength, $needleLength) === 0;
    }

    /**
     * @return array
     */
    public function testParseAndConvertToObject()
    {
        $routesPath      = "/Users/ransomware/work/symfony/gml-parser-mapbender/application/vendor/wheregroup/gml-parser/Tests/routen/";
        $routesDirectory = scandir($routesPath);
        foreach ($routesDirectory as $key => $route) {
            if ($this->endsWith($route, ".gml")) {
                $xml      = file_get_contents($routesPath . "/" . $route);
                $rawArray = Parser::castXml($xml);
                $this->assertTrue(is_array($rawArray), "Could not decode the gml file : " . $route . "\n\n\n" . json_encode($rawArray));
                $gmlEntity = new GmlEntity($rawArray, true);
            }
        }

    }
    
}