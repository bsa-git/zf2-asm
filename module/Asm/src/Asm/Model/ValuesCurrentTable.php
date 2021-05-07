<?php

// module/Asm/src/Asm/Model/ValuesCurrentTable.php:

namespace Asm\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

//use Zend\Db\SqlSelect;

class ValuesCurrentTable extends AbstractTableGateway {

    protected $table = 'values_current';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new ValuesCurrent());

        $this->initialize();
    }

    public function fetchAll() {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function fetchData($tag) {
        $rows = array();
        $indexValue = -1;
        //----------------------
//        $select = $this->sql->select();
//        $select->columns(array('name', 'ts', 'value'));
//        $select->join('tags', 'tag_id = tags.id');
//        $select->where(array('name' => $tags));
//
//        $selectString = $this->sql->getSqlStringForSqlObject($select);
//        $adapter = $this->adapter;
//        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//
//        foreach ($resultSet as $row) {
//            $alias = $row->name;
//        }
//
//
//        $statement = $this->sql->prepareStatementForSqlObject($select);
//        $resultSet = $statement->execute();
//
//        foreach ($resultSet as $row) {
//            $alias = $row->name;
//        }
//        $select = $this->sql->select();
//        $select->where(array('name' => $tags));
//        $resultSet = $this->selectWith($select);
//        
//        foreach ($resultSet as $row) {
//            $alias = $row->name;
//        }
        // Выберем все записи с данными
        $resultSet = $this->select();

        // Преобразуем каждую запись так,
        // чтобы имя = $tag, а значение = $arrValues[$indexTag]
        foreach ($resultSet as $row) {
            $arrNames = explode(';', $row->name);
            $arrValues = explode(';', $row->value);
            
            // Получим массив значений для соответствующего тега
            if(count($arrNames) == count($arrValues)){
                $arrCombine = array_combine ( $arrNames, $arrValues );
                if($arrCombine[$tag]){
                    $row->name = $tag;
                    $row->value = $arrCombine[$tag];
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    public function fetchDataForTime($time) {
        $rows = array();
        //----------------------
        // Получим обьект Select
        $select = $this->sql->select();
        // Запрос к БД
        if ($time) {
            // Преобразуем временную метку в дату и время
            $tsDateTime = date("Y-m-d H:i:s", $time);
            $arrDateTime = explode(' ', $tsDateTime);

            // Составим условия запроса
            $whereSelect = array(
                '"date_hist"' . " >= '{$arrDateTime[0]}'",
                '"time_hist"' . " > '{$arrDateTime[1]}'",
            );


            $select->where($whereSelect);
            $selectString = $this->sql->getSqlStringForSqlObject($select);
            $resultSet = $this->selectWith($select);
            $count = $resultSet->count();
            $arrResultSet = $resultSet->toArray();
            foreach ($arrResultSet as $row) {

                $rows_ = $this->rowToArrRows($row);
                foreach ($rows_ as $row_) {
                    $rows[] = $row_;
                }
            }
        } else {// Загрузим данные первый раз
            // Составим условия запроса
            // выберем все записи, отсортируем по убыванию и
            // возмем самую последнюю запись
            $select->order('ts DESC');
            $select->limit(1);
            $selectString = $this->sql->getSqlStringForSqlObject($select);
            $resultSet = $this->selectWith($select);
            $count = $resultSet->count();
            // Получим запись
            $arrResultSet = $resultSet->toArray();
            if (count($arrResultSet)) {
                $row = $arrResultSet[0];
                $rows = $this->rowToArrRows($row);
            }
        }



//        foreach ($resultSet as $row) {
//            $alias = $row->name;
//        }        
        // Выполним запрос
//        $resultSet = $this->select($arrSelect);
        return $rows;
    }

    /**
     * Преобразуем запись в массив записей
     * 
     * @param object $row
     * 
     * @return array
     */
    private function rowToArrRows($row) {
        $rows = array();
        //----------------------
        // Получим массивы тегов и значений
        $arrNames = explode(';', $row['name']);
        $arrValues = explode(';', $row['value']);

        foreach ($arrNames as $index => $name) {

            // Создадим клон записи
            $row_cloned = array() + $row;

            // Изменим поля записи
            $row_cloned['name'] = $name;
            $row_cloned['value'] = $arrValues[$index];
            // Добавим новую запись в массив
            $rows[] = $row_cloned;
        }
        return $rows;
    }

    public function getData($id) {
        $id = (int) $id;

        $rowset = $this->select(array(
            'id' => $id,
                ));

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function saveData(ValuesCurrent $values_current) {

        $data = $values_current->toArray();

        $id = (int) $values_current->id;

        if ($id == 0) {
            $this->insert($data);
        } elseif ($this->getData($id)) {
            $this->update(
                    $data, array(
                'id' => $id,
                    )
            );
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function deleteData($id) {
        $this->delete(array(
            'id' => $id,
        ));
    }

}
