<?php

namespace Application\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

/**
 * Returns total value (with tax)
 *
 */
class MyHelperDi extends AbstractHelper {

    /**
     * Service Locator
     * @var ServiceManager
     */
    protected $serviceLocator;

    /**
     * __invoke
     *
     * @access public
     * @param  int $value
     * @return String
     */
    public function __invoke($value) {
        $testService = $this->serviceLocator->get('Application\Service\TestService');
        $taxValue = $testService->getTaxValue();
        return sprintf('Total value: %s', $value * $taxValue);
    }

    /**
     * Setter for $serviceLocator
     * @param ServiceManager $serviceLocator
     */
    public function setServiceLocator(ServiceManager $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
