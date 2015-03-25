<?php
namespace Transformation;

class TransformationModel
{
    public $input_attribute;
    public $output_attribute;
    public $ruletype;
    public $output;
    public $filterData;
    
    public function __construct($input_attribute, $output_attribute, $ruletype, $output, $filterData) {
        $this->input_attribute = $input_attribute;
        $this->output_attribute = $output_attribute;
        $this->ruletype = $ruletype;
        $this->output = $output;
        $this->filterData = $filterData;
    }
}
