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

    public function getTagTable($nnDB) {
        $sm = $this->getServiceLocator();
        if ($nnDB == 2) {
            $this->tagTable = $sm->get('Asm\Model\Tag');
        } else {
            $this->tagTable = $sm->get('Asm\Model\Tag2');
        }
        return $this->tagTable;
    }

    public function getDataCurrentTable($nnDB) {
        $sm = $this->getServiceLocator();
        if ($nnDB == 2) {
            $this->dataCurrentTable = $sm->get('Asm\Model\DataCurrent');
        } else {
            $this->dataCurrentTable = $sm->get('Asm\Model\DataCurrent2');
        }
        return $this->dataCurrentTable;
    }

    public function getValuesCurrentTable($nnDB) {
        $sm = $this->getServiceLocator();
        if ($nnDB == 2) {
            $this->valuesCurrentTable = $sm->get('Asm\Model\ValuesCurrent');
        } else {
            $this->valuesCurrentTable = $sm->get('Asm\Model\ValuesCurrent2');
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
                $nnDB = $this->_params['nnDB'] ? (int) $this->_params['nnDB'] : 2;
                $json['nnDB'] = $nnDB;

                //---- Получим исторические данные для тегов ----
                if ($this->_params['series']) {

                    $json['data'] = array();

                    // Получим массив тегов
                    $tagNames = explode(',', $this->_params['tags']);
                    // Получим данные с базы данных
                    $series = $this->getDataCurrentTable($nnDB)->fetchData($tagNames);
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
                    $series = $this->getDataCurrentTable($nnDB)->fetchDataForTime($tagNames, $lastUpdateTime);
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
                        $tags = $this->getTagTable($nnDB)->fetchTags($tagNames);
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
        $maxHistTime2 = 0;
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
                    $nnDB = $this->_params['nnDB'] ? (int) $this->_params['nnDB'] : 0;
                    $json['nnDB'] = $nnDB;
                    $json['data'] = array();

                    // Получим массив тегов
                    $tagNames = $this->_params['tags'];
                    
                    // Получим данные с базы данных
                    if ($nnDB) {
                        $series = $this->getValuesCurrentTable($nnDB)->fetchData($tagNames);
                        // Сформируем массив данных для передачи клиенту
                        foreach ($series as $row) {
                            $name = $row->name;
                            $value = (float) $row->value;
                            $date_hist = $row->date_hist;
                            $time_hist = $row->time_hist;
                            $tsDateTime = $strBox::getTimeStamp("$date_hist $time_hist");
                            $tsDateTimeMs = $tsDateTime * 1000;
                            if (!array_key_exists($name, $json['data'])) {
                                $json['data'][$name] = array();
                            }
                            $json['data'][$name][] = array($tsDateTimeMs, $value);
                        }
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
                    // Получим время последнего обновления 2
                    $lastUpdateTime2 = $this->_params['time2'];
                    
                    // Получим данные с базы данных
                    $series = $this->getValuesCurrentTable(1)->fetchDataForTime($lastUpdateTime);
                    $series2 = $this->getValuesCurrentTable(2)->fetchDataForTime($lastUpdateTime2);
//                    foreach ($series2 as $row) {
//                        array_push($series, $row);
//                    }
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
                    
                    foreach ($series2 as $row) {
                        $name = $row['name'];
                        $value = (float) $row['value'];
                        $date_hist = $row['date_hist'];
                        $time_hist = $row['time_hist'];
                        $tsDateTime = $strBox::getTimeStamp("$date_hist $time_hist");
                        if ($tsDateTime > $maxHistTime2) {
                            $maxHistTime2 = $tsDateTime;
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
                        $tags = $this->getTagTable(1)->fetchTags($tagNames);
                        // Сформируем массив данных для передачи клиенту
                        foreach ($tags as $tag) {
                            $name = $tag->alias;
                            $arrTag = $tag->toArray();
                            $json['config'][$name] = $arrTag;
                        }
                        // Получим данные с базы данных
                        $tags2 = $this->getTagTable(2)->fetchTags($tagNames);
                        // Сформируем массив данных для передачи клиенту
                        foreach ($tags2 as $tag) {
                            $name = $tag->alias;
                            $arrTag = $tag->toArray();
                            $json['config'][$name] = $arrTag;
                        }
                    }
                    $json['time'] = $maxHistTime;
                    $json['time2'] = $maxHistTime2;
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
