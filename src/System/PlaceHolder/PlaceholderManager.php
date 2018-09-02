<?php

namespace XT\Core\System\Placeholder;
  
 use Zend\Db\Adapter\Adapter;
 use Zend\Db\Sql\Select;
 use Zend\Db\TableGateway\TableGateway;

 class PlaceholderManager
 {
     /**
      * @var TableGateway
      */
     protected $tablePlaceholder;

     public function __invoke($sm)
     {
         if ($this->tablePlaceholder == null)
         {
             $dbAdapter = $sm->get(Adapter::class);
             $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
             $resultSetPrototype->setArrayObjectPrototype(new Placeholder());

             $this->tablePlaceholder  = new TableGateway('placeholder', $dbAdapter, null, $resultSetPrototype);
         }
         return $this;
     }


     public function allHolder()
     {
         return $this->tablePlaceholder->select(function(Select $select) {
             $select->order(['name' => 'ASC']);
         })->toArray();
     }

     /***
      * @param $id
      * @return array|\ArrayObject|null
      */
     public function find($id)
     {
         return $this->tablePlaceholder->select(['placeholder_id' =>$id])->current();
     }

     public function delete($id)
     {
         $this->tablePlaceholder->delete(['placeholder_id' => $id]);
     }

     public function update($data, $id)
     {
         $this->tablePlaceholder->update($data, ['placeholder_id' => $id]);
     }

     public function createnewholder()
     {
         $this->tablePlaceholder->insert(['name'=>'key'.time(), 'description' => '']);
         return $this->tablePlaceholder->getLastInsertValue();
     }
 }