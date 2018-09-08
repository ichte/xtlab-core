<?php

namespace XT\Core\Event\BlockLayout;


use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class BlockLayoutManager
{
    /**
     * @var TableGateway
     */
    protected $tableBlocklayout;
    public function __invoke($sm)
    {
        if ($this->tableBlocklayout == null)
        {
            $dbAdapter = $sm->get(Adapter::class);
            $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new BlockLayout());

            $this->tableBlocklayout  = new TableGateway('template_blockhtml', $dbAdapter, null, $resultSetPrototype);
        }
        return $this;
    }


    public function allBlocks()
    {
        return $this->tableBlocklayout->select(function (Select $select) {
            $select->order(['Controller' => 'ASC', 'Action' => 'ASC']);
        })->toArray();

    }

    public function find($id)
    {
        return $this->tableBlocklayout->select(['id' =>$id])->current();
    }

    public function delete($id)
    {
        $this->tableBlocklayout->delete(['id' => $id]);
        $this->exportconfig();

    }

    public function update($data, $id)
    {
        $this->tableBlocklayout->update($data, ['id' => $id]);
        $this->exportconfig();
    }

    public function insertnew($data)
    {
        $id = $this->tableBlocklayout->insert($data);
        $this->exportconfig();
        return $this->tableBlocklayout->getLastInsertValue();
    }

    public function exportconfig()
    {

        $obs = $this->tableBlocklayout->select(function (Select $select) {
            $select->where(['active' => 1])->order(['Controller' => 'ASC', 'Action' => 'ASC']);
        })->toArray();

        $listsave = new \ArrayObject();

        foreach ($obs as $ob)
        {
            $listsave[] = $ob;
        }
        $config = new \Zend\Config\Config([],true);
        $ar =  $listsave->getArrayCopy();
        foreach ($ar as &$ob)
        {
            $c1 = $ob['Controller'];
            $c2 = $ob['Action'];
            $c4 = $ob['Block'];
            $c3 = $ob['placeholder'];

            if (!isset($config[$c1])) $config[$c1] = [];
            $ctrl = &$config[$c1];

            if (!isset($ctrl[$c2])) $ctrl[$c2] = [];
            $ation = &$ctrl[$c2];

            $ation[$c3] = $c4;

        }
        $writer = new \Zend\Config\Writer\PhpArray();
        $writer->toFile('config/listener_insert_layout.php', $config);
        if (file_exists('config/listener_merge.cache'))
            unlink('config/listener_merge.cache');

    }

}