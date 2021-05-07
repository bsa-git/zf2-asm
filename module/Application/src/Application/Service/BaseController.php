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

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Service;

class BaseController extends AbstractActionController {

    /**
     * isAjaxRequest - признак наличия Ajax запроса
     *
     * @var bool
     */
    protected $_isAjaxRequest = false;

    /**
     * isPost - признак наличия POST запроса
     *
     * @var bool
     */
    protected $_isPost = false;

    /**
     * request - обьект запроса
     *
     * @var Zend\Http\Request
     */
    protected $_request = null;
    
    /**
     * response - обьект ответа
     *
     * @var Zend\Http\Response
     */
    protected $_response = null;

    /**
     * view - обьект View\Model
     *
     * @var View\Model
     */
    protected $_view = null; //layout

    /**
     * layout - макет шаблонов
     *
     * @var layout
     */
    protected $_layout = null;

    /**
     * params - параметры запроса
     *
     * @var array
     */
    protected $_params = null;

    /**
     * zend_version - используемая версия Zend Framework
     *
     * @var string
     */
    protected $_zend_version = '';

    /**
     * config - конфигуратор приложения Zend_Cofig
     *
     * @var string
     */
    protected $_config = null;

    /**
     * strBox - класс для работы со строками
     *
     * @var string
     */
    protected $_strBox = null;
    
    /**
     * Инициализация контроллера
     */
    protected function _init() {
        
        $this->_response = $this->getResponse();

        $request = $this->getRequest();
        $this->_request = $request; // Zend\Http\Request
        $this->_isAjaxRequest = $request->isXmlHttpRequest();
        $this->_isPost = $request->isPost();
        $this->_params = $this->_getParams();


        // Получим конфигурацию
        $sm = $this->getServiceLocator();
        $this->_config = $sm->get('Config');

        // Получим класс для работы со строками
        $this->_strBox = $this->StrBoxPlugin()->getClass();
        
        // Получить View\Model и Layout
        $this->_view = new ViewModel();
        $this->_layout = $this->layout();
        
        //Установить View параметры
        $this->_setViewParams();
    }
    
    /**
     * Установить View параметры
     *
     * 
     * @return void
     */
    protected function _setViewParams() {
        
        // Зададим тип схемы расцветки сайта
        $this->_layout->scheme = "yellow-dark-blue";//  red-dark-blue, yellow-dark-blue
        $this->_layout->is_icon_white = FALSE;
        
        // Установим праздник
        // public/css/mystyle.css -> #logo2{top: 90px;}
        // public/schemes/yellow-dark-blue/css/style.css -> .body4 {background:url(../img/bg_top.png) 0px 90px repeat-x}
        $this->_layout->is_new_year = FALSE;
        $this->_view->is_new_year = FALSE;
        $this->_view->is_8_march = FALSE;
        
        // Установим панель
        $this->_layout->is_panel = FALSE;
        
        // Установим тип меню
        $this->_layout->is_superfish = FALSE;
    }

    /**
     * Получить ViewModel
     *
     * @param array $params
     * 
     * @return View\Model
     */
    protected function _getViewModel($params = array()) {

        // Установим свойства обьекта ViewModel
        foreach ($params as $key => $value) {
            $this->_view->$key = $value;
        }

        // Установим сообщение, если оно есть
        if ($this->_view->class_message) {
            $this->_layout->class_message = $this->_view->class_message;
            $this->_layout->message = $this->_view->message;
        }
        return $this->_view;
    }

    /**
     * Получить параметры в запросе
     *
     * @param array $data
     */
    private function _getParams() {
        $result = array();
        //---------------
        $params = $this->params();

        $files = $params->fromFiles();
        if (is_array($files)) {
            $result = $result + $files;
        }

        $post = $params->fromPost();
        if (is_array($post)) {
            $result = $result + $post;
        }

        $query = $params->fromQuery();
        if (is_array($query)) {
            $result = $result + $query;
        }

        $route = $params->fromRoute();
        if (is_array($route)) {
            $result = $result + $route;
        }

        return $result;
    }

    /**
     * Передать данные в формате Json
     *
     * @param array $data
     */
    protected function _sendJson($data) {
        // Очистим буфер, если там были данные
        if (ob_get_length()) {
            $body = ob_get_contents();

            // Определим это как неизвестные данные
            // которых не должно было быть!
            $unexpected_message = array(
                'class_message' => 'alert-info',
                'messages' => array(
                    '<strong><em>' . $this->Translate('Unknown message') . '!</em></strong>',
                    $body
                )
            );

            // Очистим данные
            ob_end_clean();

            // Запишем это неизвестное сообщение в данные для передачи
            if (is_array($unexpected_message)) {
                $data['unexpected_message'] = $unexpected_message;
            } else {
                $data = array(
                    "data" => $data,
                    "unexpected_message" => $unexpected_message
                );
            }
        }

        return new JsonModel($data);
    }
    

    /**
     * Сделать перевод текста
     *
     * @param string $aText
     * @return string
     */
    protected function _translate($aText, $param1 = NULL, $param2 = NULL, $param3 = NULL) {

        $sm = $this->getServiceLocator();
        $translator = $sm->get('translator');
        $text = $translator->translate($aText);
        return sprintf($text, $param1, $param2, $param3);
    }

    /**
     * Вывести (показать) информацию об ошибке
     *
     * @param \Exception $exc
     * @return JsonModel|ViewModel
     */
    protected function _showError(\Exception $exc) {
        
        $StrBox = $this->StrBoxPlugin()->getClass();
        $msgError = $StrBox::getMessageError($exc);
        if ($this->_isAjaxRequest) {
            $json = array(
                'class_message' => 'alert-error',
                'messages' => array(
                    "<strong><em>{$this->_translate('System error')}!</em></strong>",
                    $msgError
                    ));
            return $this->_sendJson($json);
        } else {
            $this->_view->class_message = 'alert-success';// alert-info alert-error alert-success
            $this->_view->message = array(
                "<strong><em>{$this->_translate('System error')}!</em></strong>",
                $msgError
            );
            return $this->_getViewModel();
        }
    }

}

