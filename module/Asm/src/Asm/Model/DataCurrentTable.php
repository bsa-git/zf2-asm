<?php

// module/Asm/src/Asm/Model/DataCurrentTable.php:

namespace Asm\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

//use Zend\Db\SqlSelect;

class DataCurrentTable extends AbstractTableGateway {

    protected $table = 'data_current';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new DataCurrent());

        $this->initialize();
    }

    public function fetchAll() {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function fetchData($tags) {

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

        $resultSet = $this->select(array('name' => $tags));
        return $resultSet;
    }

    public function fetchDataForTime($tags, $time) {
        
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
                'name' => $tags
            );

            
            $select->where($whereSelect);
            $selectString = $this->sql->getSqlStringForSqlObject($select);
            $resultSet = $this->selectWith($select);
            
        } else {
            // Составим условия запроса
            $whereSelect = array(
                'name' => $tags
            );
            $select->where($whereSelect);
            $select->order('ts DESC');
            $select->limit(count($tags));
            $selectString = $this->sql->getSqlStringForSqlObject($select);
            $resultSet = $this->selectWith($select);
            
        }



//        foreach ($resultSet as $row) {
//            $alias = $row->name;
//        }        
        // Выполним запрос
//        $resultSet = $this->select($arrSelect);
        return $resultSet;
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

    public function saveData(DataCurrent $data_current) {
//        $data = array(
//            'ts' => $data_current->ts,
//            'tag_id' => $data_current->tag_id,
//            'name' => $data_current->name,
//            'date_hist' => $data_current->date_hist,
//            'time_hist' => $data_current->time_hist,
//            'value' => $data_current->value,
//        );

        $data = $data_current->toArray();

        $id = (int) $data_current->id;

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
