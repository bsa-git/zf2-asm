<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\ModuleManager\Feature;
use Zend\EventManager\EventInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

//use Zend\I18n\Translator\Translator;
//use Zend\Validator\AbstractValidator;

class Module implements
Feature\AutoloaderProviderInterface, Feature\ServiceProviderInterface, Feature\ConfigProviderInterface, Feature\BootstrapListenerInterface {

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig() {
        return include __DIR__ . '/config/services.config.php';
    }

    public function getViewHelperConfig() {
        return array(
            'invokables' => array(
                'DBugHelper'=> 'Application\ViewHelper\DBugHelper',
            ),
            'factories' => array(
                'MyHelperDi' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new ViewHelper\MyHelperDi();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'GetReportURL'=> function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new ViewHelper\GetReportURL();
                    $viewHelper->init();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
            ),
        );
    }

//    public function onBootstrap(MvcEvent $e) {
    public function onBootstrap(EventInterface $e) {
//        $translator = $e->getApplication()->getServiceManager()->get('translator');
//        $translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']));
//        $translator->setLocale('en_US');
//        $translator->setFallbackLocale('en_US');//ru_RU
        // Установим переводчик для всех валидаторов
//        $translate = new Translator();
//        $translate->setLocale("ru_RU");
//        AbstractValidator::setDefaultTranslator($translator);

        // Настройки локали
        date_default_timezone_set("Europe/Kiev");
        
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        //Вешаем (то есть нужно будет вызвать) на событие MvcEvent::EVENT_ROUTE в вашем модуле слушателя/метод Module::initLocale()
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'initLocale'), -100);
    }

    public function initLocale(MvcEvent $e) {
        
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        //Получаем конфигурацию нашего приложения
        $config = $e->getApplication()->getServiceManager()->get('Config');
        //Вытягиваем короткое имя языка из роута (если он определился)
        $shotLang = $e->getRouteMatch()->getParam('lang'); //или $e->getApplication()->getMvcEvent()->getRouteMatch();
        if (isset($config['languages'][$shotLang])) {
            //устанавливаем локаль на определенный язык
            $translator->setLocale($config['languages'][$shotLang]['locale']);
        } else {
            //устанавливаем локаль на неизвестный/не существующий язык - по дефолту
            $lang = array_shift($config['languages']);
            $translator->setLocale($lang['locale']);
        }
    }

}
