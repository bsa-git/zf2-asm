<?php

/**
 * StrBoxPlugin
 *
 * A class for working with strings
 *
 *
 * @uses
 * @package    Module-Application
 * @subpackage Plugins
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Service;

class StrBoxPlugin extends AbstractPlugin{
    
    // Get Class
    public function getClass(){
        $strBox = new Service\StrBox();
        $class = get_class ( $strBox );
        return $class; 

    }
}
