<?php

namespace XT\Core\Event\InsertHtml;


use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class InsertHtmlManager
{
    /**
     * @var TableGateway
     */
    protected $tableEventHtml;
    public function __invoke($sm)
    {
        if ($this->tableEventHtml == null)
        {
            $dbAdapter = $sm->get(Adapter::class);
            $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new InsertHTML());

            $this->tableEventHtml  = new TableGateway('template_inserthtml', $dbAdapter, null, $resultSetPrototype);
        }
        return $this;
    }





    public function allEventHTMLInsert()
    {
        return $this->tableEventHtml->select(function (Select $select) {
            $select->order(['Controller' => 'ASC', 'Action' => 'ASC', 'Event' => 'ASC']);
        })->toArray();
    }

    public function find($id)
    {
        return $this->tableEventHtml->select(['id' =>$id])->current();
    }

    public function delete($id)
    {
        $this->tableEventHtml->delete(['id' => $id]);
        $this->exportconfig();

    }

    public function update($data, $id)
    {
        $this->tableEventHtml->update($data, ['id' => $id]);
        $this->exportconfig();
    }

    public function insertnew($data)
    {
        $id = $this->tableEventHtml->insert($data);
        $this->exportconfig();
        return $this->tableEventHtml->getLastInsertValue();
    }

    public function exportconfig()
    {
        $obs = $this->tableEventHtml->select(function (Select $select) {
            $select->where(['active' => true]);
            $select->order(['Controller' => 'ASC', 'Action' => 'ASC', 'Event' => 'ASC']);
            })->toArray();

        $listsave = new \ArrayObject();
        foreach ($obs as $ob)
        {
            $listsave[] = $ob;
        }
        $config = new \Zend\Config\Config([],true);
        $ar = $listsave->getArrayCopy();
        foreach ($ar as &$ob)
        {
            $c1 = $ob['Controller'];
            $c2 = $ob['Action'];
            $c3 = $ob['Event'];
            $c4 = $ob['Block'];

            if (!isset($config[$c1])) $config[$c1] = [];
            $ctrl = &$config[$c1];

            if (!isset($ctrl[$c2])) $ctrl[$c2] = [];
            $ation = &$ctrl[$c2];

            $ation[$c3] = $c4;

        }
        $writer = new \Zend\Config\Writer\PhpArray();
        $writer->toFile('config/listener_insert_html.php', $config);

        if (file_exists('config/listener_merge.cache'))
            unlink('config/listener_merge.cache');
    }

}