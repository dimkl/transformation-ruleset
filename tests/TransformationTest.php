<?php
class TransformationTest extends PHPUnit_Framework_TestCase
{
    public function testInRule() {
        $rules = array(new \Transformation\TransformationModel('sku', 'category', 'in', 'HelloCategory', '1,2,4'), new \Transformation\TransformationModel('sku', 'category', 'in', 'HCategory', '5,6,7'));
        $model = (object)array('sku' => '4', 'category' => 'H');
        $transformation = new \Transformation\Transformation($model);
        $transformation->load($rules)->transform();
        
        $this->assertEquals($model, (object)array('sku' => '4', 'category' => 'HelloCategory'));
    }
    public function testInRuleWithAlteredModelValue() {
        $rules = array(new \Transformation\TransformationModel('sku', 'category', 'in', 'HelloCategory', '1,2,4'), new \Transformation\TransformationModel('sku', 'category', 'in', 'HCategory', '5,6,7'));
        $model = (object)array('sku' => '4', 'category' => 'H');
        $transformation = new \Transformation\Transformation($model);
        $transformation->transform();
        $transformation->load($rules, $model)->transform();
        $model->sku = 7;
        $transformation->load($rules, $model)->transform();
        
        $this->assertEquals($model, (object)array('sku' => 7, 'category' => 'HCategory'));
    }
    public function testCustomRuleWithFirstRuleSuccess() {
        $model = (object)array('sku' => 'sk100', 'category' => 'H');
        $rules = array(new \Transformation\TransformationModel('sku', 'category', 'Custom', 'sk1', 'sk0-100'), new \Transformation\TransformationModel('sku', 'category', 'Custom', 'sk2', 'sk101-200'));
        $transformation = new \Transformation\Transformation($model);
        $transformation->load($rules, $model)->transform();
        
        $this->assertEquals($model, (object)array('sku' => 'sk100', 'category' => 'sk1'));
    }
    public function testCustomRuleWithSecondRuleSuccess() {
        $model = (object)array('sku' => 'sk200', 'category' => 'H');
        $rules = array(new \Transformation\TransformationModel('sku', 'category', 'Custom', 'sk1', 'sk0-100'), new \Transformation\TransformationModel('sku', 'category', 'Custom', 'sk2', 'sk101-200'));
        
        $transformation = new \Transformation\Transformation(new \stdClass());
        $transformation->load($rules, $model)->transform();
        
        $this->assertEquals($model, (object)array('sku' => 'sk200', 'category' => 'sk2'));
    }
    public function testCustomRuleWithAlteredModelValue() {
        $model = (object)array('sku' => 'sk100', 'category' => 'H');
        $transformation = new \Transformation\Transformation($model);
        
        $model->sku = 'sk200';
        $rules = array(new \Transformation\TransformationModel('sku', 'category', 'Custom', 'sk', 'sk0-200'), new \Transformation\TransformationModel('sku', 'category', 'Custom', 'sk--', 'sk'));
        $transformation->load($rules, $model)->transform();
        
        $this->assertEquals($model, (object)array('sku' => 'sk200', 'category' => 'sk'));
    }
    public function testFilterIn() {
        $ruleSetLoader = new \RuleSet\RuleSet();
        $ruleSetLoader->load(\RuleSet\RuleTypes::IN, '1,5,6,7,9,0,8,9,05,f4,22', 'rule type in')->load(\RuleSet\RuleTypes::BETWEEN, '15,30', 'rule type between');
        
        $this->assertEquals($ruleSetLoader->filter(5), 'rule type in');
    }
    public function testFilterPriority() {
        $ruleSetLoader = new \RuleSet\RuleSet();
        $ruleSetLoader->load(\RuleSet\RuleTypes::IN, '1,5,6,7,9,0,8,9,05,f4,22', 'rule type in')->load(\RuleSet\RuleTypes::BETWEEN, '15,30', 'rule type between');
        
        $this->assertEquals($ruleSetLoader->filter(22), 'rule type in');
    }
    public function testFilterNotMatched() {
        $ruleSetLoader = new \RuleSet\RuleSet();
        $ruleSetLoader->load(\RuleSet\RuleTypes::IN, '1,5,6,7,9,0,8,9,05,f4,22', 'rule type in')->load(\RuleSet\RuleTypes::BETWEEN, '15,30', 'rule type between');
        
        $this->assertEquals($ruleSetLoader->filter('2'),null);
    }
    public function testFilterBetween() {
        $ruleSetLoader = new \RuleSet\RuleSet();
        $ruleSetLoader->load(\RuleSet\RuleTypes::IN, '1,5,6,7,9,0,8,9,05,f4,22', 'rule type in')->load(\RuleSet\RuleTypes::BETWEEN, '15,30', 'rule type between');
        $this->assertEquals($ruleSetLoader->filter(25), 'rule type between');
    }
}
