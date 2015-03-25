<?php
namespace Transformation;

class Transformation
{
    private $model;
    private $ruleSet;
    private $attributes = array();
    
    public function __construct(&$model) {
        if (!is_object($model)) {
            throw new TransformationException("Model supplied to load method must be of object type!");
        }
        $this->model = & $model;
        $this->ruleSet = new \RuleSet\RuleSet();
    }
    
    /**
     * Method to load the tranformation data
     * @return array(stdClass)
     */
    public function load($rules, &$model = null) {
        
        // check model
        if (!is_null($model)) {
            if (!is_object($model)) {
                throw new TransformationException("Model supplied to load method must be of object type!");
            }
            $this->model = & $model;
        }
        
        // check rules
        $this->validateRules($rules);
        
        // clear data
        $this->attributes = array();
        $this->ruleSet->clear();
        
        // load data (rules, attributes)
        foreach ($rules as $rule) {
            $this->attributes[] = (object)array("input" => $rule->input_attribute, "output" => $rule->output_attribute);
            $this->ruleSet->load($rule->ruletype, $rule->filterData, $rule->output);
        }
        return $this;
    }
    
    /**
     * [transform description]
     * @return [type] [description]
     */
    public function transform() {
        foreach ($this->attributes as $attribute) {
            if (!isset($this->model->{$attribute->input}) || !isset($this->model->{$attribute->output})) continue;
            $output = $this->ruleSet->filter($this->model->{$attribute->input});
            $this->model->{$attribute->output} = is_null($output) ? $this->model->{$attribute->output} : $output;
        }
        return $this;
    }
    
    /**
     * Method validate rules supplied to transformation object
     * @throws TransformationException
     * @example
     * 	database table with rules
     *  =================================================================================
     *  |input_attribute	|output_attribute	|ruletype	|output 		|filterData	|
     *  |'sku'			 	|'category'			|'in'		|'HelloCategory'|'1,2,4'	|
     *  |'sku'			 	|'category'			|'in'		|'HCategory'	|'5,6,7'	|
     *  =================================================================================
     */
    private function validateRules($rules) {
        if (!is_array($rules)) throw new TransformationException("Rules must be in array format!");
        foreach ($rules as $rule) {
            if ($rule instanceof Ruleset\TransformationModel) {
                throw new TransformationException("Rule object in rules must instance of TransformationModel!");
            }
        }
    }
}

class TransformationException extends \Exception
{
}
