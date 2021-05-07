<?php

// module/Asm/src/Asm/Model/ValuesCurrent.php:

namespace Asm\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ValuesCurrent implements InputFilterAwareInterface {

    public $id;
    public $ts;
    public $name;
    public $date_hist;
    public $time_hist;
    public $value;
    
    private $_inputFilter;

    public function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->ts = (isset($data['ts'])) ? $data['ts'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->date_hist = (isset($data['date_hist'])) ? $data['date_hist'] : null;
        $this->time_hist = (isset($data['time_hist'])) ? $data['time_hist'] : null;
        $this->value = (isset($data['value'])) ? $data['value'] : null;
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
                        'name' => 'name',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'date_hist',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Date',
                                'options' => array(
                                    'format' => 'Y-m-d',
                                ),
                            ),
                        ),
                    )));

            
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'time_hist',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Date',
                                'options' => array(
                                    'format' => 'H:i:s',
                                ),
                            ),
                        ),
                    )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'value',
                        'required' => true,
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
