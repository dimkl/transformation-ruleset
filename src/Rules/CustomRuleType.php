<?php
namespace RuleSet\Rules;

class CustomRuleType extends ARuleType
{
    
    /**
     * data should be a string value
     * @example  
     * case 3: filteData=SK, $input=SK100, $output='CategoryOfSKU100'
     * case 1: filteData=SK100, $input=SK100, $output='CategoryOfSKU100'
     * case 2: filteData=SK0-100, $input=SK100, $output='CategoryOfSKU100'
     * @param  string $input
     * @return null or output
     */
    public function filter($input) {
        if (!is_string($this->data)) {
            throw new RuleTypeException('Custom rule type expects filter data to be in string format!');
        }
        $matches = array();
        //cases
        if (!preg_match('/^(?<category>[^0-9]+)(?<productNumber>.*)$/', $this->data, $matches) || count($matches) < 2) {
            return null;
        }

        // case of simple category based on sku initial letters,`Category` format
        if (empty($matches['productNumber'])) {
            return strpos($input, $matches['category']) !== FALSE ? $this->output : null;
        }
        // case of range
        if (strpos($matches['productNumber'], '-') !== FALSE) {
            
            // get range of calues ,'valuemin-valuemax' format
            $values = explode('-', $matches['productNumber']);
            
            // check and get product number from sku
            $matches = array();
            if (!preg_match('/^(?<category>[^0-9]+)(?<productNumber>[0-9]+)$/', $input, $matches) || count($matches) < 2) {
                
                return null;
            }

            // compare
            return $matches['productNumber'] >= $values[0] && $matches['productNumber'] <= $values[1] ? $this->output : null;
        } 
        else {
            return $input == $productNumber ? $this->output : null;
        }
    }
}
