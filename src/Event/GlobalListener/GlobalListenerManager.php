<?php

namespace XT\Core\Event\GlobalListener;


use XT\Core\Common\Common;
use XT\Core\Event\GlobalListener\AbstractGlobalListener;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class GlobalListenerManager
{
    /**
     * @var TableGateway
     */
    protected $tablelistener;
    public function __invoke($sm)
    {

        if ($this->tablelistener == null)
        {
            $dbAdapter = $sm->get(Adapter::class);
            $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new GlobalListener());

            $this->tablelistener  = new TableGateway('global_listener', $dbAdapter, null, $resultSetPrototype);
        }
        return $this;
    }





    public function allListener()
    {
        return $this->tablelistener->select(function (Select $select) {
            $select->order(['classname' => 'ASC']);
        });
    }
    public function findbyClass($class)
    {
        return $this->tablelistener->select(['classname' =>$class])->current();
    }
    public function find($id)
    {
        return $this->tablelistener->select(['id' =>$id])->current();
    }

    public function delete($id)
    {
        $b = $this->find($id);
        if ($b == null) return;




        $this->tablelistener->delete(['id' => $id]);

        $dirapp = Common::$sm->get('config')['globallistenerapp'].'/'.$b->getClassname().'.php';
        if (file_exists($dirapp)) unlink($dirapp);
        $this->exportconfig();

    }

    public function update($data, $id)
    {
        $b = $this->find($id);
        if ($b == null) return;


        $dirapp = Common::$sm->get('config')['globallistenerapp'].'/'.$b->getClassname().'.php';
        if (file_exists($dirapp))
            unlink($dirapp);



        $this->savetoDiskListener($data['classname'], $data['description'], $data['code']);


        $this->tablelistener->update($data, ['id' => $id]);



        $this->exportconfig();
    }

    public function insertnew($data)
    {

        if (isset($data['id']))
            unset($data['id']);
        $data['priority'] = 1;
        $id = $this->tablelistener->insert($data);
        $this->savetoDiskListener($data['classname'], $data['description'], $data['code']);


        $this->exportconfig();
        return $this->tablelistener->getLastInsertValue();
    }

    public function exportconfig()
    {
        /***
         * @var $ob GlobalListener
         */

        $obs =  $this->tablelistener->select(function (Select $select) {
                                            $select->where(['active' => true])
                                                ->order(['classname' => 'ASC']);
                    });

        $config = new \Zend\Config\Config([],true);

        $cf = [];
        foreach ($obs as $ob)
        {
            if ($ob->getEvents() === false) continue;
            $this->savetoDiskListener($ob->getClassname(), $ob->getDescription(), $ob->getCode());
            $events = $ob->getEvents();
            $cf[$ob->getClassname()] =  $events;

        }


        $config = new \Zend\Config\Config(['xtlabgloballistener'=>$cf], true);
        $writer = new \Zend\Config\Writer\PhpArray();
        $writer->toFile('config/autoload/xtlablistener.global.php', $config);

        Common::removeCacheMapConfig();

    }

    public function savetoDiskListener($classname, $description, $code)
    {
        $dirapp = Common::$sm->get('config')['globallistenerapp'];
        $dirsys = Common::$sm->get('config')['globallistenerapp'];

        $namespace = 'Application\GlobalListener';


        $file = new \Zend\Code\Generator\FileGenerator();
        $file->setNamespace($namespace);
        $file->setFilename($dirapp.'/'.$classname.'.php');

        $class = new ClassGenerator();
        $class->setName($classname);
        $file->setClass($class);



        $class->setDocBlock(new DocBlockGenerator('Class '.$classname, $description ));
        $class->setExtendedClass(AbstractGlobalListener::class);

        $method = new MethodGenerator(
            'execute',
            ['event'],
            MethodGenerator::FLAG_PUBLIC,
            $code,
            new DocBlockGenerator('Execute when receive events','
@param \Zend\EventManager\Event $event
@return array|\ArrayAccess|mixed|object
' )
        );


        $class->addMethodFromGenerator($method);

        $method = new MethodGenerator(
            '__invoke',
            ['serviceManager']
        );
        $method->setBody(
            <<<'BODY'
    $this->serviceManager = $serviceManager;
    return $this;
BODY
        );
        $class->addMethodFromGenerator($method);

        $file->write();
    }

}