<?php

namespace XT\Core\System\Placeholder;
  
 use XT\Core\System\KeyView;
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

     public function allHolderSelectElements()
     {

         $holders = [];
         /***
          * @var $holder Placeholder
          */
         foreach ($this->allHolder() as $holder) {


             $holders[$holder['name']]  = $holder['name'];
         }

         return $holders;
     }


     public function allEvent()
     {
         $events = [];
         /***
          * @var $holder Placeholder
          */
         foreach ($this->allHolder() as $holder) {

             $options = [];
             $event_start = KeyView::prefix_html_start.$holder['name'];
             $event_end = KeyView::prefix_html_end.$holder['name'];
             $options[$event_start] = $event_start;
             $options[$event_end] = $event_end;

             $events[] = [
                'label' => $holder['name'],
                'options' => $options
             ];
         }
         return $events;
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