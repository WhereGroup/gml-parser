<?php

namespace WhereGroup\GmlParserBundle\Entity;

use Seld\JsonLint\JsonParser;

/**
 * Class Cluster
 *
 * @package WhereGroup\GmlParserBundle\Entity
 * @author  Mohamed Tahrioui <mohamed.tahrioui@wheregroup.com>
 */
class Cluster
{

    /** @var \GeometryCollection[] */
    protected $features;

    /** @var integer */
    protected $threshold;

    /** @var float */
    protected $resolution;

    /** @var bool */
    protected $centroid;

    /** @var bool */
    protected $aggregation;

    /** @var bool */
    protected $aggregationSplitted;

    /** @var bool */
    protected $includeFeatures;

    public function __construct($feature, $threshold = 1, $resolution = 1, $centroid = false,
        $aggregation = false, $aggregationSplitted = false, $aggregationBackref = false, $includeFeatures = false)
    {

        $this->features            = array($feature);
        $this->threshold           = $threshold;
        $this->resolution          = $resolution;
        $this->centroid            = $centroid;
        $this->aggregation         = $aggregation;
        $this->aggregationSplitted = $aggregationSplitted;
        $this->aggregationBackref  = $aggregationBackref;
        $this->includeFeatures     = $includeFeatures;

    }

    /**
     * Implementation of "add(self, feature)"
     *
     * @param $feature
     * @return bool
     */

    public function addFeature($feature)
    {
        if (!isset($this->features[0])) {
            return false;
        }
        $firstFeature = $this->features[0];
        $distance     = $firstFeature->distance($feature);

        if ($distance / $this->resolution <= $this->threshold) {
            $this->features[] = $feature;
            return true;
        }
        return false;
    }

    /**
     * def _get_backref(self, feature):
     * if self.include_features:
     * backref = feature.geoJSON()
     * elif isinstance(self.aggregation_backref, basestring):
     * backref = feature.properties.get(self.aggregation_backref)
     * elif isinstance(self.aggregation_backref, list):
     * backref = {}
     * for attribute in self.aggregation_backref:
     * backref[attribute] = feature.properties.get(attribute)
     * else:
     * backref = None
     *
     * return backref
     *
     * @param \GeometryCollection $feature
     */

    private function getBackref($feature)
    {
        if ($this->includeFeatures) {
            //TODO: convert feature to GeoJson
             $backref = $feature->asArray();
        }

    }
}