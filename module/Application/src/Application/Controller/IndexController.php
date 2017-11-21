<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Service;

class IndexController extends Service\BaseController {

    public function indexAction() {
        $this->_init();
        $view = new ViewModel();
        return new $view;
    }

    public function homeAction() {

        $this->_init();

        return $this->_getViewModel();
    }

    public function aboutAction() {
        try {
            $this->_init();
            $view = new ViewModel();
            return new $view;
        } catch (\Exception $exc) {
            return $this->_showError($exc);
        }
    }

    public function pluginsAction() {
        $request = $this->getRequest(); // Zend\Http\Request
        $isXmlHttpRequest = $request->isXmlHttpRequest();
        //---------------------------------
        // Пример использование плагина
        $result = $this->ArrayBoxPlugin()->create(array("1", "2"))->max();

        $StrBox = $this->StrBoxPlugin()->getClass();
        $result = $StrBox::Translit("Привет");

        // Пример использования AJAX запроса
        // и формирование JSON ответа клиенту
        if ($isXmlHttpRequest) {
            $partial = $this->getServiceLocator()->get('viewhelpermanager')->get('partial');
            return new JsonModel(array(
                        'html' => $partial('MyModule/MyPartView.phtml', array("key" => "value")),
                        'jsonVar1' => 'jsonVal2',
                        'jsonArray' => array(1, 2, 3, 4, 5, 6)
                    ));
        }



        return new ViewModel();
    }

    public function testAction() {
//        $file = 'http://nnm-club.me/forum/new_year/ny2012.swf';
//        $source_file = file_get_contents($file);
//        $file = 'c:\\XamppServers3\\htdocs\\zf2-mon\\public\\js\\new_year\\ny2012.swf';
//        return file_put_contents($file, $source_file);
    }

    public function printAction() {

        // Инициализация
        $this->_init();

        if ($this->_params['url']) {
            // Получим содержимое ресурса
            $url = $this->_params['url'];
            $html = file_get_contents($url);

            if (!$html === FALSE) {
                $this->_view->html = $html;
            } else {
                $this->_view->html = '';
            }
        }

        // Disable layouts; use this view model in the MVC event instead
        $this->_view->setTerminal(true);
        return $this->_getViewModel();
    }

    /**
     * Загрузка ресурсов 
     * пр. изображений или других файлов
     * 
     */
    public function load() {
        $data = array();
        $paths = array();
        //--------------
        try {

            // Инициализация
            $this->_init();

            if ($this->_isAjaxRequest) {

                // Получим пути из параметров запроса
                $path_large = $this->_params['path_large'];
                if ($this->_params['path_thumbs']) {
                    $path_thumbs = $this->_params['path_thumbs'];
                } else {
                    $path_thumbs = $path_large;
                }
                // Получим массив путей
                $paths_large = explode(',', $path_large);
                $paths_thumbs = explode(',', $path_thumbs);

                // Получим файлы по шаблонам путей
                foreach ($paths_large as $index => $path_large) {
                    $path_large = trim($path_large);
                    if ($paths_thumbs[$index]) {
                        $path_thumbs = $paths_thumbs[$index];
                        $path_thumbs = trim($path_thumbs);
                    } else {
                        $path_thumbs = $path_large;
                    }

                    // Scan all the photos in the folder
                    $files_large = glob($path_large);
                    $files_thumbs = glob($path_thumbs);

                    foreach ($files_large as $index => $file_large) {
                        if ($files_thumbs[$index]) {
                            $file_thumbs = $files_thumbs[$index];
                        } else {
                            $file_thumbs = $file_large;
                        }

                        $data[] = array(
                            'thumb' => $file_thumbs,
                            'large' => $file_large
                        );
                    }
                }

                // Disable layouts; use this view model in the MVC event instead
                $this->_view->data = $data;
                $this->_view->setTerminal(true);
                return $this->_getViewModel();
            }
        } catch (\Exception $exc) {
            return $this->_showError($exc);
        }
    }

}
