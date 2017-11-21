<?php

// module/Application/src/Application/Controller/Plugin/ArrayBox.php:

namespace Application\Service;

//================ КЛАСС РАБОТЫ СО СТРОКАМИ =========================//

/**
 * StrBox
 *
 * Класс для работы со строками
 *
 *
 * @uses       Exception
 * @package    Module-Application
 * @subpackage Service
 */
class StrBox {
    //=========================== HTML ========================================//

    /**
     * Экранирование служебных символов, что бы не было SQL инекций в запросах к базе данных
     * экранирование проходит не зависимо от кодировки
     *
     *
     * @param string $value
     * @return string
     */
    static function quoteSmart($value) {
        // Else magic_quotes_gpc on - use stripslashes
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        // If variable - a number, that shields its not it is necessary
        // if no - that surround her( quote, and shield
        if (!is_numeric($value)) {
            //$value = "'".mysql_escape_string($value)."'";
            $value = mysql_escape_string($value);
        }
        return $value;
    }

    /**
     * Экранирование служебных символов, что бы не было SQL инекций в запросах к базе данных
     * экранирование проходит зависимо от кодировки
     *
     *
     * @param string $value
     * @return string
     */
    static function quoteSmartReal($value) {
        // Else magic_quotes_gpc on - use stripslashes
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        // If variable - a number, that shields its not it is necessary
        // if no - that surround her( quote, and shield
        if (!is_numeric($value)) {
            //$value = "'" . mysql_real_escape_string($value) . "'";
            $value = mysql_real_escape_string($value);
        }
        return $value;
    }

    /**
     * Удаления всего ненужного html
     *
     *
     * @param string $text
     * @return array
     */
    static function phpdigCleanHtml($text) {
        //htmlentities
        global $spec;

        //replace blank characters by spaces
        $text = ereg_replace("[\r\n\t]+", " ", $text);

        //extracts title
        if (preg_match('/< *title *>(.*?)< *\/ *title *>/is', $text, $regs)) {
            $title = trim($regs[1]);
        } else {
            $title = "";
        }

        //delete content of head, script, and style tags
        $text = eregi_replace("<head[^>]*>.*</head>", " ", $text);
        //$text = eregi_replace("<script[^>]*>.*</script>"," ",$text); // more conservative
        $text = preg_replace("/<script[^>]*?>.*?<\/script>/is", " ", $text); // less conservative
        $text = eregi_replace("<style[^>]*>.*</style>", " ", $text);
        // clean tags
        $text = preg_replace("/<[\/\!]*?[^<>]*?>/is", " ", $text);
        //$text = strip_tags($text,"<sub>");
        // first case-sensitive and then case-insensitive
        //tries to replace htmlentities by ascii equivalent
        if ($spec) {
            foreach ($spec as $entity => $char) {
                $text = ereg_replace($entity . "[;]?", $char, $text);
                $title = ereg_replace($entity . "[;]?", $char, $title);
            }
            //tries to replace htmlentities by ascii equivalent
            foreach ($spec as $entity => $char) {
                $text = eregi_replace($entity . "[;]?", $char, $text);
                $title = eregi_replace($entity . "[;]?", $char, $title);
            }
        }

        while (eregi('&#([0-9]{3});', $text, $reg)) {
            $text = str_replace($reg[0], chr($reg[1]), $text);
        }
        while (eregi('&#x([a-f0-9]{2});', $text, $reg)) {
            $text = str_replace($reg[0], chr(base_convert($reg[1], 16, 10)), $text);
        }

        //replace foo characters by space
        $text = eregi_replace("[*{}()\"\r\n\t]+", " ", $text);
        $text = eregi_replace("<[^>]*>", " ", $text);
        $text = ereg_replace("(\r|\n|\r\n)", " ", $text);

        // replace any stranglers by space
        $text = eregi_replace("( -> | <- | > | < )", " ", $text);

        //strip characters used in highlighting with no space
        $text = str_replace("^#_", "", str_replace("_#^", "", $text));
        $text = str_replace("@@@", "", str_replace("@#@", "", $text));

        $text = ereg_replace("[[:space:]]+", " ", $text);

        $retour = array();
        $retour['content'] = $text;
        $retour['title'] = $title;
        return $retour;
    }

    /**
     * Save HTML File
     *
     *
     * @param string $file
     * @return int // кол. байтов вставленных в файл или FALSE если ошибка
     */
    static function saveHTMLFile($file) {
        $str_file = file_get_contents($file);
        $search = array("&lt;", "&gt;");
        $replace = array("<", ">");
        $str_file = str_replace($search, $replace, $str_file);
        return file_put_contents($file, $str_file);
    }

    /**
     * Удаляются пробелы и
     * Преобразуем спец. символы в HTML сущности
     *
     *
     * @param string $value
     * @param string $charset   // 'UTF-8', 'cp1251'
     * @return string
     */
    static function escapeSmart($value, $charset = '') {

        //Очистим пробелы спереди и сзади строки
        $value = trim($value);

        //Преобразуем спец. символы в HTML сущности
        //Производятся следующие преобразования:
        //'&' (амперсанд) преобразуется в '&amp;'
        //'"' (двойная кавычка) преобразуется в '&quot;' when ENT_NOQUOTES is not set.
        //''' (одиночная кавычка) преобразуется в '&#039;' только в режиме ENT_QUOTES.
        //'<' (знак "меньше чем") преобразуется в '&lt;'
        //'>' (знак "больше чем") преобразуется в '&gt;'

        $value = htmlspecialchars($value, ENT_QUOTES, $charset);
        return $value;
    }

    /**
     * Truncate modifier
     *
     * Name:     truncate<br>
     * Purpose:  Truncate a string to a certain length if necessary,
     *           optionally splitting in the middle of a word, and
     *           appending the $etc string or inserting $etc into the middle.
     * @param string
     * @param integer
     * @param string
     * @param string
     * @param boolean
     * @param boolean
     * 
     * @return string
     */
    static function Truncate($string, $length = 80, $encoding = 'UTF-8', $etc = '...', $break_words = false, $middle = false) {
        if ($length == 0)
            return '';

        if (strlen($string) > $length) {
            $length -= min($length, strlen($etc));
            if (!$break_words && !$middle) {
                $string = mb_ereg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1, $encoding));
            }
            if (!$middle) {
                return mb_substr($string, 0, $length, $encoding) . $etc;
            } else {
                return mb_substr($string, 0, $length / 2, $encoding) . $etc . mb_substr($string, -$length / 2, $encoding);
            }
        } else {
            return $string;
        }
    }

    /**
     * Strip modifier
     *
     * Name:     strip<br>
     * Purpose:  Replace all repeated spaces, newlines, tabs
     *           with a single space or supplied replacement string.<br>
     * @param string
     * @param string
     * @param string
     * 
     * @return string
     */
    static function Strip($text, $replace = ' ', $encoding = 'UTF-8') {
        mb_regex_encoding($encoding);
        $s = mb_ereg_replace('\s+', $replace, $text);
        return $s;
    }

    /**
     * Функция транслита
     *
     * Name:     generate_chpu
     * Purpose:  На входе кириллица, а на выходе предложение на латинской раскладке
     * 
     * 
     * @param string $str
     * 
     * @return string
     */
    static function generate_chpu($str) {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        $str = strtr($str, $converter);
        $str = strtolower($str);
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        $str = trim($str, "-");
        return $str;
    }

    /**
     * Преобразуем строку в транслит
     * 
     * @return String the resulting String object
     */
    static function translit($str) {
        $value = $str;
        //----------------
        // Массив символов
        $letters = array(
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e",
            "ё" => "e", "ж" => "zh", "з" => "z", "и" => "i", "й" => "j", "к" => "k",
            "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "c",
            "ч" => "ch", "ш" => "sh", "щ" => "sh", "ы" => "i", "ь" => "", "ъ" => "",
            "э" => "e", "ю" => "yu", "я" => "ya",
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E",
            "Ё" => "E", "Ж" => "ZH", "З" => "Z", "И" => "I", "Й" => "J", "К" => "K",
            "Л" => "L", "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
            "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "C",
            "Ч" => "CH", "Ш" => "SH", "Щ" => "SH", "Ы" => "I", "Ь" => "", "Ъ" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA",
        );

        // Проходим по массиву и заменяем каждый символ фильтруемого значения
        foreach ($letters as $letterVal => $letterKey) {
            $value = str_replace($letterVal, $letterKey, $value);
        }

        return $value;
    }

    /**
     * Функция tolink() принимает в качестве аргумента ваш текст и возвращает 
     * текст с уже замененными URL на активные ссылки. 
     * 
     * @param string $buf
     * @return String the resulting String object
     */
    static function tolink($buf) {
        $x = explode(" ", $buf);
        $newbuf = '';
        for ($j = 0; $j < count($x); $j++) {
            if (preg_match
                            ("/(http:\\/\\/)?([a-z_0-9-.]+\\.[a-z]{2,3}(([ \"'>\r\n\t])|(\\/([^ \"'>\r\n\t]*)?)))/", $x[$j], $ok))
                $newbuf.=str_replace($ok[2], "<a href='http://$ok[2]'>$ok[2]</a>", str_replace("http://", "", $x[$j])) . " ";
            else
                $newbuf.=$x[$j] . " ";
        }
        return $newbuf;
    }

    //================== РАБОТА С ФАЙЛАМИ ================//

    /**
     * Получить массив имен файлов отсортированному по убыванию
     * из обычной директории
     *
     * 
     * @param  string $dir //директория, где находится файл
     * @param  string $prefix //префикс (начало файла - "my_")
     *
     * @return array
     *
     * пр. my_20100201.xml;my_20100202.xml;my_20100203.xml -> выберется файл my_20100203.xml
     * или my1.xml;my3.xml;my5.xml -> выберется файл my_5.xml
     */
    static function getNameFilesSortDesc($dir, $prefix = "") {
        $isFile = false;
        $isPrefix = true;
        $arrNameFiles = array();
        $name = "";
        //----------------------
        $dirdata = scandir($dir, 1);
        foreach ($dirdata as $key => $element) {
            $isFile = is_file($dir . '\\' . $element);
            if ($prefix !== "") {
                $isPrefix = substr_count($element, $prefix);
            }
            if ($isFile AND $isPrefix) {
                $arrNameFiles[] = $element;
            }
        }
        return $arrNameFiles;
    }

    /**
     * Получить массив имен файлов отсортированному по убыванию
     * из WEB по протоколу HTTP
     * 
     * @param  string $dir //URL, где находятся файлы
     * @param  string $prefix //префикс (начало файла - "my_")
     *
     * @return array
     *
     * пр. my_20100201.xml;my_20100202.xml;my_20100203.xml -> выберется файл my_20100203.xml
     * или my1.xml;my3.xml;my5.xml -> выберется файл my_5.xml
     */
    static function getNameFilesSortDesc_ForWEB($url, $prefix = "") {
        $isFile = false;
        $isPrefix = true;
        $arrNameFiles = array();
        $name = "";
        //----------------------
        // Получим список файлов из полученного HTML
        $dirdata = file_get_contents($url);

        // Если обращение по адресу не удалось, то выйдем
        if ($dirdata === FALSE) {
            return $arrNameFiles;
        }

        $dirdata = explode('<A HREF="', $dirdata);

        foreach ($dirdata as $element) {
            $elements = explode('">', $element);
            $element = $elements[0];
            $strlen = strlen($element);

            // Отфильтруем строки, которые заканчиваются на '/' или '>'
            $lastSymbol = $element[$strlen - 1];
            $isFile = ($lastSymbol == '/' || $lastSymbol == '>') ? false : true;

            if (!$isFile) {
                continue;
            }

            // Выберем только название файла и уберем лишний путь
            $elements = explode('/', $element);
            $lastIndex = count($elements) - 1;
            $element = $elements[$lastIndex];

            // Отфильруем по префиксу
            if ($prefix !== "") {
                $isPrefix = substr_count($element, $prefix);
            }
            if ($isPrefix) {
                $arrNameFiles[] = $element;
            }
        }
        // Сделаем обратную сортировку
        rsort($arrNameFiles);
        return $arrNameFiles;
    }

    /**
     * Получить массив имен файлов отсортированному по убыванию 
     * последнего времени изменения из обычной директории
     *
     * 
     * @param  string $dir //директория, где находится файл
     * @param  string $prefix //префикс (начало файла - "my_")
     *
     * @return array
     */
    static function getTimeFilesSortDesc($dir, $prefix = "") {
        $isFile = false;
        $isPrefix = true;
        $arrNameFiles = array();
        $arrNew = array();
        $name = "";
        //----------------------
        $dirdata = scandir($dir, 1);
        foreach ($dirdata as $key => $element) {
            $isFile = is_file($dir . '\\' . $element);
            if ($prefix !== "") {
                $isPrefix = substr_count($element, $prefix);
            }
            if ($isFile AND $isPrefix) {
                $arrNameFiles[] = $element;
            }
        }
        // Отсортируем массив по времени, 
        // которое находиться имени файла
        foreach ($arrNameFiles as $filename) {
            $path_parts = pathinfo($filename);
            $basename = $path_parts['filename'];
            $arrName = explode("_", $basename);
            $count = count($arrName);
            $key = $arrName[$count-2] . "_" . $arrName[$count-1];
            $arrNew[$key] = $filename;
        }
        
        krsort($arrNew);
        reset($arrNew);
        
        $arrSortNameFiles = array_values($arrNew);
        return $arrSortNameFiles;
    }

    /**
     * ф-ия сортировки массива по дате
     * дата находиться в имени файла
     *
     * 
     * @param  string $arr  // сам массив
     * @param  string $exc  // расширение файла пр. ".html"
     *
     * @return array // отсортированный массив
     */
//    static function fnSortTimeFilesDesc($arr) {
//        $arrNew = array();
//        //------------------------------
//        foreach ($arr as $filename) {
//            $path_parts = pathinfo($filename);
//            $basename = $path_parts['filename'];
//            $arrName = explode("_", $basename);
//            $count = count($arrName);
//            $key = $arrName[$count-2] . "_" . $arrName[$count-1];
//            $arrNew[$key] = $filename;
//        }
//        
//        krsort($arrNew);
//        reset($arrNew);
//        
//        $arrSortNameFiles = array_values($arrNew);
//        return $arrSortNameFiles;
//    }

    //=========== РАБОТА С МАССИВАМИ ==================//

    /**
     * Вывести ответ из массива данных
     *
     *
     * @param string $prefix
     * @param array $arrData
     * 
     * @return void
     */
    static function outResponseFromArray($prefix, array $arrData) {
        foreach ($arrData as $key => $value) {
            echo $prefix . "$key:=" . $value . "<br>\n";
        }
    }

    /**
     * Получим строку параметров для запроса SQL в операторе IN('val_1','val_2')
     *
     *
     * @param array $arrParam
     * 
     * @return string
     */
    static function getParamFor_IN(array $arrParam) {

        $strParam = "";
        //----------------
        foreach ($arrParam as $param) {
            if ($strParam == "")
                $strParam = "'" . $param . "'";
            else
                $strParam = $strParam . ",'" . $param . "'";
        }

        return $strParam;
    }

    //=========== РАБОТА С ДАТОЙ ВРЕМЕНЕМ ==================//

    /**
     * Получить текущую дату в формате - Y-m-d H:i:s
     * 
     * @param int $TimeStamp
     * @return string 
     */
    static function getCurrentDateTime($TimeStamp = 0) {

        $format = "Y-m-d H:i:s";
        //----------------
        if ($TimeStamp == 0)
            return date($format);
        else
            return date($format, $TimeStamp);
    }

    /**
     * Преобразование значений даты к временной метке
     * 
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int 
     */
    static function makeTimeStamp($year = '', $month = '', $day = '') {
        if (empty($year)) {
            $year = strftime('%Y');
        }
        if (empty($month)) {
            $month = strftime('%m');
        }
        if (empty($day)) {
            $day = strftime('%d');
        }

        return mktime(0, 0, 0, $month, $day, $year);
    }

    /**
     * Преобразует текстовое представление даты 
     * на английском языке в метку времени Unix
     * 
     * @param string $date_time
     * @return int 
     */
    static function getTimeStamp($date_time = '') {
        if (empty($date_time)) {
            return time();
        }
        if (($timestamp = strtotime($date_time)) === -1) {
            return -1;
        } else {
            return strtotime($date_time);
        }
    }

    /**
     * функция, которая заменяет стандартную функцию date()
     * для вывода форматированной даты и будет выводить русский месяц.
     * 
     * @param string $param
     * @param int $time
     * @return string
     */
    static function rdate($param, $time = 0) {
        if (intval($time) == 0)
            $time = time();
        $MonthNames = array("Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
        if (strpos($param, 'M') === false)
            return date($param, $time);
        else
            return date(str_replace('M', $MonthNames[date('n', $time) - 1], $param), $time);
    }

    //============= РАБОТА С ОТЧЕТАМИ  ==============//

    /**
     * Получить оглавление для отчета
     * 
     * @param string $title
     * @param string $url_pic
     * 
     * 
     * @return string 
     */
    static function getTitle4Report($title, $url_pic) {

        // style='text-align: right;'

        $html = "
        <table border='0' cellpadding='3' width='100%'>
        <tbody>
            <tr class='h'>
                <td class='t' ><img src='$url_pic' alt=''><span class='t'> $title</span></td>
            </tr>
        </tbody>
        </table>
        ";

        return $html;
    }

    /**
     * Получить заголовок отчета
     * 
     * @param array $arrHeaders
     * 
     * 
     * @return atring 
     */
    static function getHead4Report(array $arrHeaders) {
        $th = '';
        foreach ($arrHeaders as $value) {
            $th .= '<th>' . $value . '</th>';
        }

        $html = "
        <thead>
            <tr class='h'>
                $th
            </tr>
        </thead>
        ";

        return $html;
    }

    /**
     * Получить нижний колонтитул отчета
     * 
     * @param array $arrHeaders // Массив строк т.е. может быть несколько строк результатов пр. сумма, среднее, макс, мин.
     * 
     * 
     * @return string 
     */
    static function getFooter4Report(array $arrFooters) {
        $foot = '';
        $tr = '';
        $th = '';
        //---------------------------------
        foreach ($arrFooters as $arrFooter) {
            $tr = "<tr>";
            foreach ($arrFooter as $value) {
                if ($th) {
                    $th .= '<td class="fv">' . $value . '</td>';
                } else {
                    $th .= '<td class="fh">' . $value . '</td>';
                }
            }
            $tr .= $th . '</tr>';
            $foot .= $tr;
            $tr = '';
            $th = '';
        }

        $html = "
        <tfoot>
            $foot
        </tfoot>
        ";

        return $html;
    }

    /**
     * Получить данные для отчета
     * 
     * @param array $params // Массив параметров
     * @param array $arrRows // Массив строк данных 
     * 
     * 
     * @return atring 
     */
    static function getBody4Report(array $arrRows, array $params = null) {
        $isRowHeader = false;
        $body = '';
        $tr = '';
        $td = '';
        $index = 1;
        //---------------------------------
        if ($params['isRowHeader']) {
            $isRowHeader = $params['isRowHeader'];
        }

        foreach ($arrRows as $arrRow) {
            $is_even = $index % 2;
            if ($is_even) {
                $tr = "<tr class='row-even'>";
            } else {
                $tr = "<tr class='row-odd'>";
            }

            foreach ($arrRow as $value) {
                if ($td) {

                    if (is_bool($value)) {
                        $td .= '<td class="v">' . $value . '</td>';
                        continue;
                    }

                    if (is_string($value)) {
                        $td .= '<td class="vl">' . $value . '</td>';
                        continue;
                    }

                    if (is_float($value) || is_int($value)) {
                        $td .= '<td class="vr" >' . $value . '</td>';
                        continue;
                    }

                    $td .= '<td class="vl">' . $value . '</td>';
                } else {
                    if ($isRowHeader) {                        //is_string($arrRow)  //is_float($arrRow) //is_int($arrRow) //is_bool($arrRow)
                        $td .= '<td class="e">' . $value . '</td>';
                    } else {

                        if (is_bool($value)) {
                            $td .= '<td class="v">' . $value . '</td>';
                            continue;
                        }

                        if (is_string($value)) {
                            $td .= '<td class="vl">' . $value . '</td>';
                            continue;
                        }

                        if (is_float($value) || is_int($value)) {
                            $td .= '<td class="vr">' . $value . '</td>';
                            continue;
                        }

                        $td .= '<td class="vl">' . $value . '</td>';
                    }
                }
            }
            $index++;
            $tr .= $td . '</tr>';
            $body .= $tr;
            $tr = '';
            $td = '';
        }

        $html = "
        <tbody>
            $body
        </tbody>
        ";

        return $html;
    }

    //==================== ВЫЗОВ ПОЛЬЗОВАТЕЛЬСКОЙ ОШИБКИ  ==================//

    /**
     * Получим сообщение об ошибке в виде:
     *  - сообщения об ошибке;
     *  - трассы появления ошибки
     *
     *
     * @return string
     *
     */
    static function getMessageError($exc) {
        $message = '<em>Message:</em><br>';
        $message .= $exc->getMessage() . '<br>';
        $message .= '<em>Trace Error:</em><br>';
        $errTraceErr = explode('#', $exc->getTraceAsString());
        foreach ($errTraceErr as $value) {
            $message .= $value . '<br>';
        }
        return $message;
    }

}
