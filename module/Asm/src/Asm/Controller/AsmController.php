<?php

// module/Asm/src/Asm/Controller/AsmController.php:

namespace Asm\Controller;

//use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Service;

class AsmController extends Service\BaseController {

    protected $tagTable;
    protected $dataCurrentTable;
    protected $valuesCurrentTable;

    public function indexAction() {

        try {
            // Инициализация
            $this->_init();

            $this->_view->class_message = 'alert-error';
            $this->_view->message = array(
                "<strong><em>{$this->_translate('We are sorry')}! {$this->_translate('This page is currently under development')}</em></strong>",
                $this->_translate('Refer to this resource later') . '.'
            );

            return $this->_getViewModel();
        } catch (\Exception $exc) {

            return $this->_showError($exc);
        }
    }

    public function getTagTable() {
        if (!$this->tagTable) {
            $sm = $this->getServiceLocator();
            $this->tagTable = $sm->get('Asm\Model\Tag');
        }
        return $this->tagTable;
    }

    public function getDataCurrentTable() {
        if (!$this->dataCurrentTable) {
            $sm = $this->getServiceLocator();
            $this->dataCurrentTable = $sm->get('Asm\Model\DataCurrent');
        }
        return $this->dataCurrentTable;
    }
    
    public function getValuesCurrentTable() {
        if (!$this->valuesCurrentTable) {
            $sm = $this->getServiceLocator();
            $this->valuesCurrentTable = $sm->get('Asm\Model\ValuesCurrent');
        }
        return $this->valuesCurrentTable;
    }

    public function trendsAction() {
        $maxHistTime = 0;
        $json = array();
        //-------------------------------

        try {
            // Инициализация
            $this->_init();

            if ($this->_isAjaxRequest) {

                // Получим ссылку на класс плагина
                $strBox = $this->_strBox;

                //---- Получим исторические данные для тегов ----
                if ($this->_params['series']) {

                    $json['data'] = array();

                    // Получим массив тегов
                    $tagNames = explode(',', $this->_params['tags']);
                    // Получим данные с базы данных
                    $series = $this->getDataCurrentTable()->fetchData($tagNames);
                    // Сформируем массив данных для передачи клиенту
                    foreach ($series as $row) {
                        $name = $row->name;
                        $value = (float) $row->value;
                        $date_hist = $row->date_hist;
                        $time_hist = $row->time_hist;
                        $tsDateTime = $strBox::getTimeStamp("$date_hist $time_hist");
                        if ($tsDateTime > $maxHistTime) {
                            $maxHistTime = $tsDateTime;
                        }
                        $tsDateTimeMs = $tsDateTime * 1000;
                        if (!array_key_exists($name, $json['data'])) {
                            $json['data'][$name] = array();
                        }
                        $json['data'][$name][] = array($tsDateTimeMs, $value);
                    }
                }

                //----- Обновим данные -----
                if ($this->_params['update']) {

                    $json['data'] = array();
                    $json['config'] = array();

                    // Получим массив тегов
                    $tagNames = explode(',', $this->_params['tags']);
                    // Получим время последнего обновления
                    $lastUpdateTime = $this->_params['time'];

                    // Получим данные с базы данных
                    $series = $this->getDataCurrentTable()->fetchDataForTime($tagNames, $lastUpdateTime);
                    // Сформируем массив данных для передачи клиенту
                    foreach ($series as $row) {
                        $name = $row->name;
                        $value = (float) $row->value;
                        $date_hist = $row->date_hist;
                        $time_hist = $row->time_hist;
                        $tsDateTime = $strBox::getTimeStamp("$date_hist $time_hist");
                        if ($tsDateTime > $maxHistTime) {
                            $maxHistTime = $tsDateTime;
                        }
                        $tsDateTimeMs = $tsDateTime * 1000;
                        if (!array_key_exists($name, $json['data'])) {
                            $json['data'][$name] = array();
                        }
                        $json['data'][$name][] = array($tsDateTimeMs, $value);
                    }

                    // Загрузим данные первый раз
                    if (!$lastUpdateTime) {
                        // Получим данные с базы данных
                        $tags = $this->getTagTable()->fetchTags($tagNames);
                        // Сформируем массив данных для передачи клиенту
                        foreach ($tags as $tag) {

                            $name = $tag->alias;
                            $arrTag = $tag->toArray();
                            $json['config'][$name] = $arrTag;
                        }
                    }

                    $json['time'] = $maxHistTime;
                }

                return $this->_sendJson($json);
            } else {

                $this->_view->tabActive = $this->_params['tab'];
                return $this->_getViewModel();
            }
        } catch (\Exception $exc) {

            return $this->_showError($exc);
        }
    }
    
    public function chartsAction() {
        $maxHistTime = 0;
        $json = array();
        //-------------------------------

        try {
            // Инициализация
            $this->_init();

            if ($this->_isAjaxRequest) {

                // Получим ссылку на класс плагина
                $strBox = $this->_strBox;

                //---- Получим исторические данные для тегов ----
                if ($this->_params['series']) {

                    $json['data'] = array();

                    // Получим массив тегов
                    $tagNames = $this->_params['tags'];
                    // Получим данные с базы данных
                    $series = $this->getValuesCurrentTable()->fetchData($tagNames);
                    // Сформируем массив данных для передачи клиенту
                    foreach ($series as $row) {
                        $name = $row->name;
                        $value = (float) $row->value;
                        $date_hist = $row->date_hist;
                        $time_hist = $row->time_hist;
                        $tsDateTime = $strBox::getTimeStamp("$date_hist $time_hist");
//                        if ($tsDateTime > $maxHistTime) {
//                            $maxHistTime = $tsDateTime;
//                        }
                        $tsDateTimeMs = $tsDateTime * 1000;
                        if (!array_key_exists($name, $json['data'])) {
                            $json['data'][$name] = array();
                        }
                        $json['data'][$name][] = array($tsDateTimeMs, $value);
                    }
                }

                //----- Обновим данные -----
                if ($this->_params['update']) {

                    $json['data'] = array();
                    $json['config'] = array();

                    // Получим массив тегов
                    $tagNames = explode(',', $this->_params['tags']);
                    // Получим время последнего обновления
                    $lastUpdateTime = $this->_params['time'];

                    // Получим данные с базы данных
                    $series = $this->getValuesCurrentTable()->fetchDataForTime($lastUpdateTime);
                    // Сформируем массив данных для передачи клиенту
                    foreach ($series as $row) {
                        $name = $row['name'];
                        $value = (float) $row['value'];
                        $date_hist = $row['date_hist'];
                        $time_hist = $row['time_hist'];
                        $tsDateTime = $strBox::getTimeStamp("$date_hist $time_hist");
                        if ($tsDateTime > $maxHistTime) {
                            $maxHistTime = $tsDateTime;
                        }
                        $tsDateTimeMs = $tsDateTime * 1000;
                        if (!array_key_exists($name, $json['data'])) {
                            $json['data'][$name] = array();
                        }
                        $json['data'][$name][] = array($tsDateTimeMs, $value);
                    }

                    // Загрузим данные первый раз
                    if (!$lastUpdateTime) {
                        // Получим данные с базы данных
                        $tags = $this->getTagTable()->fetchTags($tagNames);
                        // Сформируем массив данных для передачи клиенту
                        foreach ($tags as $tag) {

                            $name = $tag->alias;
                            $arrTag = $tag->toArray();
                            $json['config'][$name] = $arrTag;
                        }
                    }

                    $json['time'] = $maxHistTime;
                }

                return $this->_sendJson($json);
            } else {

                $this->_view->tabActive = $this->_params['tab'];
//                $this->_view->navTab = 'oc-02';//$this->_params['navtab'];
                return $this->_getViewModel();
            }
        } catch (\Exception $exc) {

            return $this->_showError($exc);
        }
    }

    public function reportsAction() {
        $json = array();
        //-------------------------------
        try {
            // Инициализация
            $this->_init();
            return $this->_getViewModel();
            
        } catch (\Exception $exc) {
            return $this->_showError($exc);
        }
    }

    public function dcsAction() {// Data collection stations
        try {
            // Инициализация
            $this->_init();
            
            $this->_view->tabActive = $this->_params['tab'];
            
            return $this->_getViewModel();
            
        } catch (\Exception $exc) {
            return $this->_showError($exc);
        }
    }

}
