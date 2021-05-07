<?php

/**
 * ArrayBox
 *
 * A class for working with arrays
 *
 *
 * @uses
 * @package    Module-Application
 * @subpackage Plugins
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Service;

class ArrayBoxPlugin extends AbstractPlugin{
    
    
    public function create($a = array(), $delimiter = '&'){
        return new Service\ArrayBox($a, $delimiter);
    }
}
