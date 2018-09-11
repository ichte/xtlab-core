<?php

namespace XT\Core\Event\ViewPlace;


use XT\Core\Common\Common;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class ViewPlaceManager
{
    /**
     * @var TableGateway
     */
    protected $tablepluginView;
    public function __invoke($sm)
    {
        if ($this->tablepluginView == null)
        {
            $dbAdapter = $sm->get(Adapter::class);
            $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ViewPlace());

            $this->tablepluginView  = new TableGateway('viewplace', $dbAdapter, null, $resultSetPrototype);
        }
        return $this;
    }


    public function allClassActive()
    {
        return $this->tablepluginView->select(function (Select $select) {
            $select->where(['active' => true]);
            $select->order(['Controller' => 'ASC', 'Action' => 'ASC', 'Event' => 'ASC']);
        })->toArray();
    }

    public function allClass()
    {
        return $this->tablepluginView->select(function (Select $select) {
            $select->order(['Controller' => 'ASC', 'Action' => 'ASC', 'Event' => 'ASC']);
        })->toArray();
    }

    public function find($id)
    {
        return $this->tablepluginView->select(['id' =>$id])->current();
    }

    public function delete($id)
    {
        $this->tablepluginView->delete(['id' => $id]);
        $this->exportconfig();

    }

    public function update($data, $id)
    {

        $this->tablepluginView->update($data, ['id' => $id]);
        $this->exportconfig();
    }

    public function insertnew($data)
    {
        $id = $this->tablepluginView->insert($data);
        $this->exportconfig();
        return $this->tablepluginView->getLastInsertValue();
    }

    public function exportconfig()
    {

        $config = new \Zend\Config\Config([],true);
        foreach ($this->allClassActive() as $ob)
        {
            $c1 = $ob['Controller'];
            $c2 = $ob['Action'];
            $c3 = $ob['Event'];
            $c4 = $ob['Class'];

            if (!isset($config[$c1])) $config[$c1] = [];
            $ctrl = &$config[$c1];

            if (!isset($ctrl[$c2])) $ctrl[$c2] = [];
            $ation = &$ctrl[$c2];

            $ation[$c3] = $c4;

        }
        $writer = new \Zend\Config\Writer\PhpArray();
        $writer->toFile('config/insert_viewplace.php', $config);
        if (file_exists('config/listener_merge.cache'))
            unlink('config/listener_merge.cache');

        Common::removeCacheMapConfig();
    }

}