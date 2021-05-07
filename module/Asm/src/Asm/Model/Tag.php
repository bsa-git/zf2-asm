<?php

// module/Asm/src/Asm/Model/Tag.php:

namespace Asm\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Tag implements InputFilterAwareInterface {

    public $id;
    public $ts;
    public $topic;
    public $name_topic;
    public $alias;
    public $name_alias;
    public $tag_param;
    public $name_param;
    public $value_type;
    public $value_unit;
    public $scale_min;
    public $scale_max;
    public $signal_min;
    public $signal_max;
    public $blocking_min;
    public $blocking_max;
    public $comment;
    
//    protected $inputFilter;
    private $_inputFilter;

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->ts = (isset($data['ts'])) ? $data['ts'] : null;
        $this->topic = (isset($data['topic'])) ? $data['topic'] : null;
        $this->name_topic = (isset($data['name_topic'])) ? $data['name_topic'] : null;
        $this->alias = (isset($data['alias'])) ? $data['alias'] : null;
        $this->name_alias = (isset($data['name_alias'])) ? $data['name_alias'] : null;
        $this->tag_param = (isset($data['tag_param'])) ? $data['tag_param'] : null;
        $this->name_param = (isset($data['name_param'])) ? $data['name_param'] : null;
        $this->value_type = (isset($data['value_type'])) ? $data['value_type'] : null;
        $this->value_unit = (isset($data['value_unit'])) ? $data['value_unit'] : null;
        $this->scale_min = (isset($data['scale_min'])) ? $data['scale_min'] : null;
        $this->scale_max = (isset($data['scale_max'])) ? $data['scale_max'] : null;
        $this->signal_min = (isset($data['signal_min'])) ? $data['signal_min'] : null;
        $this->signal_max = (isset($data['signal_max'])) ? $data['signal_max'] : null;
        $this->blocking_min = (isset($data['blocking_min'])) ? $data['blocking_min'] : null;
        $this->blocking_max = (isset($data['blocking_max'])) ? $data['blocking_max'] : null;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }
    
    public function toArray() {
        $arrResult = array();
        //----------------------
        $objectVars = get_object_vars($this);
        foreach ($objectVars as $key => $value) {
            if($key[0] !== '_'){
                $arrResult[$key] = $value;
            }
        }
        return $arrResult;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->_inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'id',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Int',
                                'options' => array(
                                ),
                            ),
                        ),
                    )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'ts',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Date',
                                'options' => array(
                                    'format' => 'Y-m-d H:i:s',
                                ),
                            ),
                        ),
                    )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'topic',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'name_topic',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'alias',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'name_alias',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'tag_param',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'name_param',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'value_type',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'value_unit',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'scale_min',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Float',
                                'options' => array(
                                ),
                            ),
                        ),
                    )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'scale_max',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Float',
                                'options' => array(
                                ),
                            ),
                        ),
                    )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'signal_min',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Float',
                                'options' => array(
                                ),
                            ),
                        ),
                    )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'signal_max',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Float',
                                'options' => array(
                                ),
                            ),
                        ),
                    )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'blocking_min',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Float',
                                'options' => array(
                                ),
                            ),
                        ),
                    )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'blocking_max',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Float',
                                'options' => array(
                                ),
                            ),
                        ),
                    )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'comment',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));

            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }

}
