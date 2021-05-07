<?php

namespace Application\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;
use Application\Service;

/* * *******************************************************************************************************************\
 * LAST UPDATE
 * ============
 * 2013-12
 *
 *
 * AUTHOR
 * =============
 * Beskorovayny S.A.
 * bs261257@gmail.com
 * 
 * 
 *
 * PURPOSE
 * =============
 * Получить путь к соответсвующему отчету
 *
 *
 * USAGE
 * =============
 * $this->GetReportURL($typeReport)
 * example:
 * <a href="<?php echo $this->GetReportURL('monthHNO3_M5'); ?>" target="_blank">Производство HNO3</a>
 *
 * 
 * $typeReport - определяет тип отчета, по которому 
 * определяем путь где находяться соответствующие отчеты 
 * и получаем последний сформированный отчет по времени
 *
 * NOTE!
 * ==============
 * 
 * 
  \******************************************************************************************************************** */

class GetReportURL extends AbstractHelper {

    /**
     * Service Locator
     * @var ServiceManager
     */
    protected $serviceLocator;

    /**
     * Str Box
     * - Класс для работы со строками
     * 
     *  
     * @var strBox
     */
    protected $strBox;
    // Пути к отчетам
    protected $_reportPaths = array(
        // Суточные отчеты
        'dailyAvgM5_1-html' => "/www_m5/day_reports/m5-1/HTML",
        'dailySumM5_1-html' => "/www_m5/day_reports/m5-1/HTML",
        'dailyResourcesM5_2-html' => "/www_m5/day_reports/m5-2/Use_Resources",
        'dailyProductionM5_2-html' => "/www_m5/day_reports/m5-2/Production",
        'dailyNGParamsM5_2-html' => "/www_m5/day_reports/m5-2/NG_Params",
        'dailySumM7_1-html' => "/www_m5/day_reports/m7-1/HTML",
        'dailySumM7_2-html' => "/www_m5/day_reports/m7-2/HTML",
        // Месячные отчеты Excel
        'monthHNO3_M5-xls' => "/www_m5/balance/HNO3/Excel",
        'monthAllGAM_M5-xls' => "/www_m5/balance/NH3/Gas/Excel",
        'monthAgrGAM_M5-xls' => "/www_m5/balance/AGR/Excel",
        'monthAllJAM_M5-xls' => "/www_m5/balance/NH3/Liq/Excel",
        'monthRashodK_M5-xls' => "/www_m5/balance/RASHOD_K/Excel",
        // Месячные отчеты HTML
        'monthHNO3_M5-html' => "/www_m5/balance/HNO3/HTML",
        'monthAllGAM_M5-html' => "/www_m5/balance/NH3/Gas/HTML",
        'monthAgrGAM_M5-html' => "/www_m5/balance/AGR/HTML",
        'monthAllJAM_M5-html' => "/www_m5/balance/NH3/Liq/HTML",
        'monthRashodK_M5-html' => "/www_m5/balance/RASHOD_K/HTML"
    );
    // Пути к отчетам
    protected $_urls = array(
    );

    /**
     * __invoke
     *
     * @access public
     * @param  array|xml|object $var
     * @param  string $forceType array|xml|object
     * @param  object $bCollapsed
     * @return String
     */
    public function init() {
//        $this->strBox = Service\StrBox;
    }

    /**
     * __invoke
     * 
     * Получим путь к последнему отчету
     * 
     * @access public
     * @param  string $typeReport
     * @return String
     */
    public function __invoke($typeReport) {
        $strBox = new Service\StrBox();
        $strBox = get_class($strBox);
        //----------------------
        try {
            // Проверим если уже этот URL
            if (isset($this->_urls[$typeReport])) {
                return $this->_urls[$typeReport];
            }

            // Get host
            $arrHost = explode(':', $_SERVER['HTTP_HOST']);
            $host = $arrHost[0];

            $config = $this->serviceLocator->get('Config');
            $disks = $config['DisksForHost'];
            $env = $config['application_env'];

            if ($env == 'production') {
                $pathDir = "{$disks[$host]}{$this->_reportPaths[$typeReport]}";
                if ($typeReport == 'dailyAvgM5_1-html') {
                    $listFiles = $strBox::getNameFilesSortDesc($pathDir, 'avg_');
                } else if ($typeReport == 'dailySumM5_1-html') {
                    $listFiles = $strBox::getNameFilesSortDesc($pathDir, 'sum_');
                } else {
                    $listFiles = $strBox::getTimeFilesSortDesc($pathDir);
                }

                // Изменим кодировку файла на UTF8
                $file = $pathDir . '/' . $listFiles[0];
                $str_file = $this->getFileEncodingUTF8($file);
                if ($str_file) {
                    file_put_contents($file, $str_file);
                }
            }

            if ($env == 'development' || $env == 'testing') {
                $pathDir = $this->_getDirPath($typeReport);
                if ($typeReport == 'dailyAvgM5_1-html') {
                    $listFiles = $strBox::getNameFilesSortDesc($pathDir, 'avg_');
                } else if ($typeReport == 'dailySumM5_1-html') {
                    $listFiles = $strBox::getNameFilesSortDesc($pathDir, 'sum_');
                } else {
                    $listFiles = $strBox::getTimeFilesSortDesc($pathDir);
                }
                // Изменим кодировку файла на UTF8
                $file = $pathDir . '/' . $listFiles[0];
                $str_file = $this->getFileEncodingUTF8($file);
                if ($str_file) {
                    file_put_contents($file, $str_file);
                }
            }

            // Получим URL отчета
            if (count($listFiles)) {
                $fileName = $listFiles[0];
                $pathUrl = $this->_getUrlPath($typeReport);
                $pathUrl .= '/' . $fileName;
            } else {
                $pathUrl = '#';
            }

            // Запомним URL
            $this->_urls[$typeReport] = $pathUrl;

            return $pathUrl;
        } catch (\Exception $exc) {
            return "ERROR: " . $exc->getMessage();
        }
    }

    /**
     * Setter for $serviceLocator
     * @param ServiceManager $serviceLocator
     */
    public function setServiceLocator(ServiceManager $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * _getUrlPath
     * Получить путь к папке отчетов
     * @param string $typeReport
     */
    private function _getUrlPath($typeReport) {
        $config = $this->serviceLocator->get('Config');
        // Ips for host
        $ips = $config['IpsForHost'];
        $env = $config['application_env'];
        $path = $this->_reportPaths[$typeReport];
        if ($env == 'development' || $env == 'testing') {
            $path = str_replace('/www_m5', '/upload/reports', $path);
        } else {
            // Get host
            $arrHost = explode(':', $_SERVER['HTTP_HOST']);
            $host = $arrHost[0];
            $path = "http://{$ips[$host]}{$path}";
        }
        
        return $path;
    }

    /**
     * _getDirPath
     * Получить путь к папке отчетов
     * @param string $typeReport
     */
    private function _getDirPath($typeReport) {

        $path = $this->_reportPaths[$typeReport];
        $path_replace = realpath('./') . '/public/upload/reports/';
        $path = str_replace('/www_m5/', $path_replace, $path);
        return $path;
    }

    /**
     * Преобразовать текст из файла в кодировку UTF-8
     *
     * @param  string $aFilename    //Путь к текстовому файлу в неизвестной кодировке (windows-1251 или UTF-8)
     * @return string               //Текст преобразованный в кодировку UTF-8
     *
     */
    private function getFileEncodingUTF8($aFilename) {

        //----------------------
        $content = file_get_contents($aFilename);
        $isWinCoding = substr_count($content, "charset=windows-1251");
        if ($isWinCoding) {
            $content = str_replace("charset=windows-1251", "charset=utf-8", $content);
            return iconv("Windows-1251", "UTF-8", $content);
        } else {
            return "";
        }
    }

}
