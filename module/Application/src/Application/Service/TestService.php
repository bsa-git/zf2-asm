<?php

/**
 * BaseController
 *
 * Контроллер - Base
 * реализует базовые действия котроллеров
 *
 * @uses       AbstractActionController
 * @package    Controller
 * @subpackage Service
 */

namespace Application\Service;


class TestService{

    /**
     * Конструктор
     * 
     * 
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Инициализация контроллера
     */
    private function init() {

    }

    /**
     * getTaxValue
     * 
     *
     * 
     * @return mixed
     * @throws Exception\DomainException
     */
    public function getTaxValue()
    {
        return 10;
    }

}

