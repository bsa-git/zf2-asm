<?php

// module/Application/src/Application/Controller/Plugin/ArrayBox.php:

namespace Application\Service;


/**
 * ArrayBox
 *
 * Класс для работы с массивами
 *
 *
 * @uses
 * @package    Module-Application
 * @subpackage Service
 */
CLASS ArrayBox {
    
    /**
     * @var array 
     */
    private $_array = array();
    
    
    /**
     * Конструктор
     * 
     * 
     * @param array|string|int $a
     * @param string|int $delimiter 
     */
    public function __construct($a = array(), $delimiter = '&') {
        if(is_array($a)){// $a -> array
            $this->_array = $a;
        } elseif (is_string($a)) {
            if($delimiter === NULL){// $a -> string (unserialize)
                $this->_array = unserialize(base64_decode($a));
            }elseif($delimiter == '&'){// $a -> string (query)
                parse_str($a, $this->_array);
            }else{// $a -> string (delimiter)
                $this->_array = explode($delimiter, $a);
            }
        } elseif (is_integer($a) && is_integer($delimiter)) {// range ($low, $high)
            $low = $a; // Низнее значение диапазона
            $high = $delimiter; // Верхнее значение диапазона
            $this->_array = range($low, $high);
        }
    }
    
    
    /**
     * Destructor to prevent memory leaks.
     */
    public function __destruct() {
        unset($this);
    }
    
    /**
     * Returns string representation of the object
     * @return string string representation of the object
     */
    public function __toString() {
        return var_export($this->_array, true);
    }
    
    /**
     * Get the max. value of an array
     * 
     * @return array 
     */
    function max() {
        $array = $this->_array;
        $max = $array[0];
        $index = 0;
        foreach ($array as $key => $val)
            if ($val > $max) {
                $max = $val;
                $index = $key;
            }
        return array("index" => $index, "value" => $max);
    }

    /**
     * Get the min. value of an array
     * 
     * @return array 
     */
    function min() { 
        $array = $this->_array;
        $min = $array[0];
        $index = 0;
        foreach ($array as $key => $val)
            if ($val < $min) {
                $min = $val;
                $index = $key;
            }
        return array("index" => $index, "value" => $min);
    }
    
    
    /**
     * Get the sum. of an array
     * 
     * @return float
     */
    function sum() { 
        $array = $this->_array;
        $sum = 0;
        foreach ($array as $val)
            $sum += $val;
        return $sum;
    }

    /**
     * Get the avg. value of an array
     * 
     * @return float
     */
    function avg() { //
        $array = $this->_array;
        if($this->sum($array) > 0){
            return $this->sum($array) / count($array);
        }else{
            throw new Exception('Division by zero error');
        }
        
    }

    /**
     * Get the mode value of an array
     * 
     * @return float
     */
    function mode() { //
        $array = $this->_array;
        foreach ($array as $val){
            $frequency[$val]++;
        }
        $r = $this->max($frequency);
        return $r["index"];
    }

    /**
     * Get the median value of an array
     * 
     * @return float
     */
    function median() { //
        $array = $this->_array;
        $count = count($array);
        $array = $this->order($array);
        if ($count % 2 == 0)
            return ($array[$count / 2 - 1] + $array[$count / 2]) / 2;
        else
            return $array[$count / 2 - 0.5];
    }

    /**
     * Get array in order
     * 
     * @param bool $ascending
     * @return ArrayBox 
     */
    function order($ascending = true) { //
        $array = $this->_array;
        if ($ascending)
            usort($array, cmp_a);
        else
            usort($array, cmp_d);
        return new self($array);
    }

    /**
     * Change a array to a string
     * 
     * @return boolean|string 
     */
    function array2str() { //
        $array = $this->_array;
        if (is_array($array))
            return base64_encode(serialize($array));
        else
            return false;
    }

    /**
     * Recover a array to a string
     * 
     * @param string $value
     * @return ArrayBox 
     */
    function str2array($value) { //
        if (is_string($value))
            $array = unserialize(base64_decode($value));
        else
            $array = $this->_array;
        return new self($array);
    }
    
    /**
     * The union of the two arrays
     * 
     * @param array $aArray
     * @return ArrayBox 
     */
    function merge($aArray) { // $array + $aArray
        $array = $this->_array;
        return new self($array + $aArray);
    }
    
    /**
     * Select all the keys of an array
     * 
     * @return ArrayBox 
     */
    function keys() { //
        $array = $this->_array;
        return new self(array_keys($array));
    }
    
    /**
     * Select all the values of an array
     * 
     * @return ArrayBox 
     */
    function values() { //
        $array = $this->_array;
        return new self(array_values($array));
    }
    
    /**
     * Проверить, присутствует ли в массиве указанный ключ или индекс
     * 
     * @param mixed $key
     * @return bool
     */
    function isKey($key) {
        $array = $this->_array;
        return array_key_exists ( $key, $array);
    }
    
    /**
     * Проверить, присутствует ли в массиве значение
     * 
     * 
     * @param mixed $value
     * @return bool
     */
    function isValue($value) {
        $array = $this->_array;
        return in_array ($value, $array);
    }
    
    /**
     * Size of array
     * 
     * @return int 
     */
    function count() { // 
        $array = $this->_array;
        return count($array);
    }
    
    /**
     * 
     * Get the value of the array by index or the entire array
     * 
     * @param int|string|null $index
     * @return mixed 
     */
    function get($index = null) { // 
        $array = $this->_array;
        if($index){
            return $array[$index];
        }else{
            return $array;
        }
    }
    
    /**
     * Set or add the value of the array index
     * 
     * 
     * @param mixed $value
     * @param int $index
     * @return ArrayBox
     */
    function set($value, $index=null) { //
        
        if(is_array($value)){
            return new self($value);
        }
        
        $array = $this->_array;
        if($index){
            $array[$index] = $value;
        }else{
            // Добавим значение или значения в конец массива
            // $value может быть массивом
            $array = array_push($array, $value);
        }
        
        return new self($array);
    }
    
    /**
     * Delete the last value in the array or set index
     * 
     * @param int|string $index
     * @return ArrayBox
     */
    function pop($index = null) { //
        $array = $this->_array;
        if($index){
            unset($array[$index]); // Удалить элемент массива по индексу
        }else{
            array_pop($array);// Удалить последний элемент массива
        }
        return new self($array);
    }
    
    /**
     * Add values ​​to the end of the array
     * 
     * @param mixed $values
     * @return ArrayBox 
     */
    function push($values) { // 
        $array = $this->_array;
        array_push($array, $values);
        return new self($array);
    }
    
    /**
     * Join array elements into a string
     *  
     * @param string $delimiter
     * @return string 
     */
    function join($delimiter = ' ') { //
        $array = $this->_array;
        return implode($delimiter, $array);
    }
    
    /**
     * Splits a string into a array
     * 
     * @param string $value
     * @param string $delimiter
     * @return ArrayBox 
     */
    function split( $value, $delimiter = ' ') { //
        $array = $this->_array;
        if(is_string($value)){
            $array = explode($delimiter, $value);
        }
        return new self($array);
    }
    
    /**
     * Converts an array to a query string, 
     * etc.  - "first=value&arr[]=foo+bar&arr[]=baz"
     * 
     * @return string 
     */
    function array2query() { //
        $array = $this->_array;
        return http_build_query($array);
    }
    
    /**
     * Converts the query string etc.  - "first=value&arr[]=foo+bar&arr[]=baz"
     * to a array
     * 
     * @param string $query
     * @return ArrayBox 
     */
    function query2array($query) { 
        $array = array();
        parse_str($query, $array);
        return new self($array);
    }
    
    /**
     * Получить срез массива значений (массивов/обьектов) по ключу
     * и удалить повторяющиеся значения из среза, если нужно
     *
     * @param  string $key     //Название поля
     * @param  bool $unique    //Признак уникальности выходного массива
     *
     * @return ArrayBox 
     */
    function slice($key, $unique=false) {
        $array = array();
        $items = $this->_array;
        //--------------------------
        foreach ($items as $item) {
            if(is_array($item)){
                $array[] = $item[$key];
            }  elseif (is_object($item)) {
                $array[] = $item->$key;
            }
        }
        if($unique){
            $array = array_unique($array);
        }
        
        return new self($array);
    }
    
    /**
     * Print a array
     * 
     * @param bool $bCollapsed 
     */
//    function print_array($bCollapsed=false) { //
//        $array = $this->_array;
//        new DBug($array, "array", $bCollapsed);
//    }

}

function cmp_a($a, $b) {
    if ($a == $b)
        return 0;
    return ($a < $b) ? -1 : 1;
}

function cmp_d($a, $b) {
    if ($a == $b)
        return 0;
    return ($a > $b) ? -1 : 1;
}

?>