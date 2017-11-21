<?php

// module/Asm/src/Asm/Model/TagTable.php:

namespace Asm\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class TagTable extends AbstractTableGateway {

    protected $table = 'tags';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Tag());

        $this->initialize();
    }

    public function fetchAll() {
        $resultSet = $this->select();
        return $resultSet;
    }
    
    public function fetchTags($tags) {
        $resultSet = $this->select(array('alias' => $tags));
        return $resultSet;
    }

    public function getTag($id) {
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

    public function saveTag(Tag $tag) {
//        $data = array(
//            'ts' => $tag->ts,
//            'topic' => $tag->topic,
//            'name_topic' => $tag->name_topic,
//            'alias' => $tag->alias,
//            'name_alias' => $tag->name_alias,
//            'tag_param' => $tag->tag_param,
//            'name_param' => $tag->name_param,
//            'value_type' => $tag->value_type,
//            'value_unit' => $tag->value_unit,
//            'scale_min' => $tag->scale_min,
//            'scale_max' => $tag->scale_max,
//            'signal_min' => $tag->signal_min,
//            'signal_max' => $tag->signal_max,
//            'blocking_min' => $tag->blocking_min,
//            'blocking_max' => $tag->blocking_max,
//            'comment' => $tag->comment,
//        );
        
        $data = $tag->toArray();

        $id = (int) $tag->id;

        if ($id == 0) {
            $this->insert($data);
        } elseif ($this->getTag($id)) {
            $this->update(
                    $data, array(
                'id' => $id,
                    )
            );
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function deleteTag($id) {
        $this->delete(array(
            'id' => $id,
        ));
    }

}
