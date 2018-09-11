<?php

namespace XT\Core\Admin\Layout;



use XT\Admin\Controller\AbstractPlugin;
use XT\Core\Admin\Layout\Form\FormGlobalListener;
use XT\Core\Admin\Layout\Form\FormHolder;
use XT\Core\Admin\Layout\Form\FormInsertBlock;
use XT\Core\Admin\Layout\Form\FormViewPlace;
use XT\Core\Admin\Layout\Form\FromInsertHtml;
use XT\Core\Common\Common;
use XT\Core\Controller\Controller;
use XT\Core\Event\BlockLayout\BlockLayout;
use XT\Core\Event\BlockLayout\BlockLayoutManager;
use XT\Core\Event\GlobalListener\AbstractGlobalListener;
use XT\Core\Event\GlobalListener\GlobalListener;
use XT\Core\Event\GlobalListener\GlobalListenerManager;
use XT\Core\Event\InsertHtml\InsertHTML;
use XT\Core\Event\InsertHtml\InsertHtmlManager;
use XT\Core\Event\ViewPlace\ViewPlace;
use XT\Core\Event\ViewPlace\ViewPlaceManager;
use XT\Core\Filter\NameClass;
use XT\Core\Helper\ControllerHelper;
use XT\Core\Helper\TemplateFiles;
use XT\Core\System\Placeholder\Placeholder;
use XT\Core\System\Placeholder\PlaceholderManager;
use XT\Core\ToolBox\MessageBox;
use XT\Core\System\RBAC_PERMISSION;
use XT\Core\Validator\ExtensionAllow;
use XT\Db\Adapter;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Config\Config;
use Zend\Filter\FilterChain;
use Zend\Filter\PregReplace;
use Zend\Filter\StaticFilter;
use Zend\Filter\StringToLower;
use Zend\Filter\StringTrim;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\I18n\Filter\Alpha;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\File\Extension;
use Zend\Validator\File\NotExists;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;
use Zend\View\Model\ViewModel;

class Layout extends AbstractPlugin
{



    /**
     * @var ValidatorChain
     */
    protected $validator;

    protected $filter;

    protected $nameplugin = 'Layout';

    protected $description = 'Manager layout (.phtml)';

    public function intPlugin()
    {
        $ar = [];

        $ar['placeholder'] = [
            'name' => 'PlaceHolder',
            'description' => 'PlaceHolder for View, mocking events, insert block HTML ...',
            'index' => true
        ];


        $ar['layoutedit'] = [
            'name' => 'PHTML',
            'description' => 'Edit .phtml (template) files',
            'index' => true
        ];



        $ar['applyblocklayout'] = [
            'name' => 'Block layout',
            'description' => 'Config block layout',
            'index' => true

        ];

        $ar['applyblocklayoutedit'] = [
            'name' => 'EditBlockLayout',
            'description' => 'EditBlockLayout',
            'index' => false

        ];
        $ar['AddBlockHTML'] = [
            'name' => 'AddBlockHTML',
            'description' => 'AddBlockHTML',
            'index' => false

        ];



        $ar['eventinserthtml'] = [
            'name' => 'Insert PHTML',
            'description' => 'Insert file.html to placeholder by trigger event',
            'index' => true

        ];

        $ar['viewplace'] = [
            'name' => 'ViewPlace',
            'description' => 'Event, Listener class render at placeholder of template .phtml',
            'index' => true

        ];
        $ar['viewplaceedit'] = [
            'name' => 'Edit ViewPlace',
            'description' => 'ViewPlace',
            'index' => false

        ];
        $ar['viewplaceadd'] = [
            'name' => 'Add ViewPlace',
            'description' => 'Add ViewPlace',
            'index' => false

        ];







        $ar['listenerglobal'] = [
            'name' => 'ListenerGlobal',
            'description' => 'Create Listener/Event Global',
            'index' => true

        ];


        $ar['editgloballistener'] = [
            'name' => 'Edit Listener',
            'description' => 'Create Listener/Event Global',
            'index' => false

        ];
        $ar['createglobalistener'] = [
            'name' => 'Create',
            'description' => 'Create Listener/Event Global',
            'index' => false

        ];



        $this->setListaction($ar);
    }


    function index($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted'). ' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);

        $view->setVariables(['list' => $this->getListactionIndex()]);
        return $view;
    }


    //HOLDER
    function placeholder($id)
    {

        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $placeholderManager PlaceholderManager
         */
        $placeholderManager = $this->serviceManager->get(PlaceholderManager::class);



        $id = $this->ctrl->params()->fromRoute('id');
        $obs = $placeholderManager->allHolder();
        $item = null;

        $listevent = [];
        foreach ($obs as $ob) { $listevent[] = $ob; }

        $request = $this->ctrl->getRequest();
        $form = new FormHolder('edit');



        if ($id > 0)
        {

            $item = $placeholderManager->find($id);

            if ($item == null)
            {
                return MessageBox::htmlMessage(Common::translate('Not found').' : '.$id);
            }

            if ($request->isPost())
            {
                $post = $request->getPost();

                if ($post->action == 'Delete') {
                    return $this->ctrl->askBeforeDone(
                        'Delete keyholder: '.$item->getName(),
                        $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'holderdelete', 'id' =>$id]),
                        ['id' => $id, 'confirmdel' => 'confirmdel']

                    );
                }

                $form->addNoRecordExistsName(['field'=>'placeholder_id', 'value' => $item->getPlaceholderId()]);
                $form->setData($post);

               if ($form->isValid())
               {
                   $data = $form->getData();
                   $item->exchangeArray($data);
                   $placeholderManager->update($item->getArrayCopy(), $id);
                   return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder']);
               }
            }
            else
                $form->bind($item);


            $view = $this->createView(__DIR__, __CLASS__, 'holderedit');
            $view->setVariables(['item' => $item,  'form' => $form]);
            return $view;

        }

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['listevent'=>$listevent,'item' => $item]);
        return $view;
    }

    function holderdelete($id)
    {
        if ($this->ctrl->isConfirm(['id' => $id, 'confirmdel' => 'confirmdel']))
        {
            /**
             * @var $holderManager HolderManager
             */
            $holderManager = $this->serviceManager->get(PlaceholderManager::class);
            $holderManager->delete($id);


            return MessageBox::redirectMgs('Đã xóa', 
                $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder']),
                'Thông báo',
                2

            );
        }
        return MessageBox::redirectMgs('Cancel Delete', 
            $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder', 'id' =>$id]),
            'Notification',
            2

        );
    }

    function newholder()
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        if ($this->ctrl->getRequest()->isGet())
        {
            return $this->ctrl->askBeforeDone(
                'Are you sure to create new placeholder?',
                $this->url('layout', 'newholder'),
                ['suretocreatenew' => 'suretocreatenew']
            );
        }
        else {
            if ($this->ctrl->getRequest()->isPost())
                if ($this->ctrl->isConfirm(['suretocreatenew' => 'suretocreatenew']))
                {
                    /**
                     * @var $holderManager PlaceholderManager
                     */
                    $holderManager = $this->serviceManager->get(PlaceholderManager::class);

                    $id = $holderManager->createnewholder();
                    return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder', 'id' =>$id]);
                }
        }

        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder']);

    }

    //EDIT PHTML

    function layoutedit()
    {

        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);
        $request = $this->ctrl->getRequest();

        $csrflayoutedit = new \Zend\Form\Element\Csrf('csrflayoutedit',['csrf_options'=>['timeout'=>'1200']] );
        if ($request->isPost()) {
            //CHECK CSRF
            if (!$csrflayoutedit->getCsrfValidator()->isValid($this->ctrl->params()->fromPost('csrflayoutedit'))) {
                foreach ($csrflayoutedit->getCsrfValidator()->getMessages() as $mgs) {
                    echo $mgs;
               }
               die;
            }

            $fileedit = '';
            $filecontent = '';
            $action  = '';

            $fileedit = $this->ctrl->params()->fromPost('file','');
            $action = $this->ctrl->params()->fromPost('action','Edit');


            if ($action == 'New') {
                if ($this->ctrl->params()->fromPost('filecreatenew', null) != null)
                {
                    $filename = $this->ctrl->params()->fromPost('filecreatenew', null);

                    return $this->ctrl->askBeforeDone('Create new file:'.$filename,
                        $this->url('layout', 'addphtml'),
                        ['filecreatenew' => $filename, 'createnew' => 'createnewok']
                    );
                }
                else {

                    return MessageBox::redirectMgs('Please input filename!',
                        $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'layoutedit']),
                        '',
                        5
                    );
                }
            }
            else if ($action == 'Map')
            {
                return $this->ctrl->askBeforeDone('Build Map template (optimization load)',
                    $this->url('layout', 'buildingmaptemplate'),
                    ['buildingmaptemplate' => 'buildingmaptemplate']
                );
            }
            else if ($action == 'ClearMap')
            {
                return $this->ctrl->askBeforeDone('Remove Map Template Files',
                    $this->url('layout', 'removemaptemplate'),
                    ['removemaptemplate' => 'removemaptemplate']
                );
            }





            $filecontent = $this->ctrl->params()->fromPost('filecontent','');
            $cdir = realpath(Common::$cf->CF->common->pathtemplatedefault);

            $realfile = realpath($cdir.'/'.$fileedit);


            if ($action == 'Edit')
            {
                //Copy from SYS
                if ($realfile === false )
                {
                   $filecopy  = realpath(Common::$sm->get('config')['templatepathsys'].'/'.$fileedit);
                   if ($filecopy === false)
                       return MessageBox::redirectMgs('Not Found!!',
                           $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'layoutedit']),
                           '',
                           5
                       );
                   $pi = pathinfo($cdir.'/'.$fileedit);
                   if (!file_exists($pi['dirname']))
                       @mkdir($pi['dirname'], 0777, true);


                   copy($filecopy, $cdir.'/'.$fileedit);
                   $realfile = realpath($cdir.'/'.$fileedit);
                   if (file_exists('config/autoload/templatemap.global.php'))
                   {
                       $this->_exportmaptemplate();
                       Common::removeCacheMapConfig();
                   }

                }



                $filecontent = file_get_contents($realfile);
            }
            else if ($action == 'Save')
            {
                //chmod($cdir.'/'.$fileedit,0777);
                $filename = $realfile;
                $byte = file_put_contents($filename, $filecontent);
                if ($byte === false)
                    throw new \Exception("Can not save: $filename");
 
            }
            else if ($action == 'Delete')
            {
                return $this->ctrl->askBeforeDone('Delete file:'.$fileedit,
                    $this->url('layout', 'deletephtml'),
                    ['filecreatenew' => $fileedit, 'delete' => 'delete']
                );
            }

            $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
            $view->setVariables([
                'fileedit' => $fileedit,
                'filecontent' =>$filecontent
            ]);
            return $view;
        }
        else {
            //View List

            $fs = TemplateFiles::listfilesEdit();


            $view = $this->createView(__DIR__, __CLASS__, 'phtmlindex', 'layoutedit');
            $view->setVariables([ 'files' => $fs ]);
            return $view;
        }




    }

    function removemaptemplate()
    {


        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);
        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            if ($this->ctrl->isConfirm(['removemaptemplate' => 'removemaptemplate'])) {

                unlink('config/autoload/templatemap.global.php');
                Common::removeCacheMapConfig();

            }
        }

        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'layoutedit']);

    }

    function _exportmaptemplate()
    {
        $pathapp = Common::$cf->CF->common->pathtemplatedefault;
        $pathsys = Common::$sm->get('config')['templatepathsys'];



        $maps = [];
        $fs = TemplateFiles::listfilesEdit();
        foreach ($fs as $f) {
            foreach ($f as $fi)
            {
                if ($fi['type'] == 'system') {
                    $maps[$fi['name']] = str_replace('\\', '/', realpath($pathsys.'/'.$fi['name']));
                }
                else
                {
                    $maps[$fi['name']] = str_replace('\\', '/', realpath($pathapp.'/'.$fi['name']));
                }

            }
        }
        $cf = new Config(['view_manager' => ['template_map' => $maps]]);
        $writer = new \Zend\Config\Writer\PhpArray();
        $writer->toFile('config/autoload/templatemap.global.php',$cf);
        Common::removeCacheMapConfig();
    }


    function buildingmaptemplate()
    {


        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);
        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            if ($this->ctrl->isConfirm(['buildingmaptemplate' => 'buildingmaptemplate'])) {

                $this->_exportmaptemplate();

                return MessageBox::redirectMgs('Saved to /config/autoload/templatemap.global.php',
                    $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'layoutedit']),
                    '',
                    5
                );
            }
        }

        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'layoutedit']);

    }

    function deletephtml() {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $filename = $this->ctrl->params()->fromPost('filecreatenew', null);
            if ($this->ctrl->isConfirm(['filecreatenew' => $filename, 'delete' => 'delete'])) {

                $cdir = realpath(Common::$cf->CF->common->pathtemplatedefault);
                $filedelete = $filename;
                $realfile = realpath($cdir.'/'.$filedelete);
                if ($realfile === false )
                    $realfile = realpath(Common::$sm->get('config')['templatepathsys'].'/'.$filedelete);

                $fi = pathinfo('data/template.bak'.'/'.$filedelete);
                if (!file_exists($fi['dirname']))
                @mkdir($fi['dirname'], 0777, true);
                copy($realfile, $fi['dirname'].'/'.$fi['filename'].'.'.time().'.'.$fi['extension']);
                unlink($realfile);


//                throw new \Exception('(TODO)Check not use template before delete file:'.$filedelete);

//                $rows = $this->ctrl->dbAdapter->get_rows_select($this->ctrl->dbAdapter->sql->select('blockhtmlevent')->columns(['Block']));
//                foreach ($rows as $ctrl)
//                    if ($cfile == $ctrl['Block'])
//                    {
//                        $candel = false;
//                    }
//                if ($candel) @unlink($cdir.'/'.$fileedit);
//                $fileedit = '';





            }
        }

        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'layoutedit']);
    }

    function addphtml() {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $filename = $this->ctrl->params()->fromPost('filecreatenew', null);
            if ($this->ctrl->isConfirm(['filecreatenew' => $filename, 'createnew' => 'createnewok'])) {


                $filterFilename = new FilterChain(
                    ['filters' => [
                        ['name' => PregReplace::class,   'options' => ['pattern'=>'/\.\./','replacement'=>'']],
                        ['name' => StringToLower::class, 'options' => [ 'encoding' => 'UTF-8' ]],
                        ['name' => StringTrim::class],
                        ['name' => StringToLower::class]
                    ]
                    ]
                );

                $newfile = $filterFilename->filter($filename);

                $cdir = realpath(Common::$cf->CF->common->pathtemplatedefault);
                $newfile = $cdir.'/'.$newfile;

                $validatorChain = new ValidatorChain();
                $validatorChain->attachByName(NotExists::class, []);
                $validatorChain->attachByName(ExtensionAllow::class, ['extension' => 'phtml']);

                if (!$validatorChain->isValid($newfile)) {
                    foreach ($validatorChain->getMessages() as $mgs) {
                        echo $mgs;
                    }
                    die;
                }

                $dircreate = pathinfo($newfile,PATHINFO_DIRNAME);
                if (!file_exists($dircreate))
                    @mkdir($dircreate);
                $byte = file_put_contents($newfile, 'NEW FILE BLOCK');
                if ($byte === false)
                    throw new \Exception("Can not save: $newfile");



            }
        }

        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'layoutedit']);
    }


    //EVENT INSERT HTML
    function eventinserthtml($id) {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);


        /**
         * @var $inserthtmlmanager InsertHtmlManager
         */
        $inserthtmlmanager = $this->serviceManager->get(InsertHtmlManager::class);

        $obs = $inserthtmlmanager->allEventHTMLInsert();
        $listeventblock = new \ArrayObject();
        foreach ($obs as $ob)
        {
            $listeventblock[] = $ob;
        }

        if ($this->ctrl->getRequest()->isPost())
        {
            $inserthtmlmanager->exportconfig();
        }


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['listeventblock' => $listeventblock]);
        return $view;
    }

    function eventinserthtmledit($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $inserthtmlmanager InsertHtmlManager
         * @var $ControllerElement Select
         * @var $EventElement Select
         * @var $BlockElement Select
         * @var $ActiveElement Checkbox
         * @var $ev InsertHTML
         */
        $inserthtmlmanager = $this->serviceManager->get(InsertHtmlManager::class);

        $form = new FromInsertHtml('edit');

        $ev = $inserthtmlmanager->find($id);
        if ($ev == null)
        return MessageBox::redirectMgs('Not found',
            $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'eventinserthtml'])

        );
        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $params = $this->ctrl->params();

            $action = $params->fromPost('action','Save');
            if ($action == 'Delete')
            {
                return $this->ctrl->askBeforeDone(
                    'Delete Event?',
                    $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'eventinserthtmldelete', 'id' => $id]),
                    ['id' => $id, 'deleteinsertevent' => $id]
                );
            }



            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $ev->exchangeArray($data);
                $data = $ev->getArrayCopy();
                unset($data['ControllerAction']);

                $inserthtmlmanager->update($data, $id);
                return MessageBox::redirectMgs(
                    'Save success',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']),
                    'Save',
                    2
                );

            }
        }
        else
        $form->setData($ev->getArrayCopy());


        $valrt =  [
            'form' => $form,
            'InsertHTML' => $ev
        ];

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables($valrt);
        return $view;

    }

    function eventinserthtmldelete($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $inserthtmlmanager InsertHtmlManager
         */
        $inserthtmlmanager = $this->serviceManager->get(InsertHtmlManager::class);


        if ($this->ctrl->isConfirm( ['id' => $id, 'deleteinsertevent' => $id]))
        {
            $inserthtmlmanager->delete($id);
            return MessageBox::redirectMgs(
                'Deleted',
                $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']),
                'Save',
                2
            );

        }
        return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']);


    }

    function eventinserthtmladd($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $inserthtmlmanager InsertHtmlManager
         * @var $ControllerElement Select
         * @var $EventElement Select
         * @var $BlockElement Select
         * @var $ActiveElement Checkbox
         * @var $ev InsertHTML
         */
        $inserthtmlmanager = $this->serviceManager->get(InsertHtmlManager::class);

        $form = new FromInsertHtml('edit');


        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $params = $this->ctrl->params();


            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $ev = new InsertHTML();
                $ev->exchangeArray($data);
                $data = $ev->getArrayCopy();
                unset($data['ControllerAction']);

                $inserthtmlmanager->insertnew($data);
                return MessageBox::redirectMgs(
                    'Create success',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']),
                    'Save',
                    2
                );

            }
        }



        $valrt =  [
            'form' => $form
        ];


        $view = $this->createView(__DIR__, __CLASS__, 'eventinserthtmledit');
        $view->setVariables($valrt);

        return $view;

    }

    //BLOCK EVENT
    function applyblocklayout()
    {

        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $blcoklayoutManager BlockLayoutManager
         */
        $blcoklayoutManager = $this->serviceManager->get(BlockLayoutManager::class);
        $listeventblock = $blcoklayoutManager->allBlocks();


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['listeventblock'=>$listeventblock]);
        return $view;
    }

    function exportblocklayout() {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);


        /**
         * @var $blcoklayoutManager BlockLayoutManager
         */
        $blcoklayoutManager = $this->serviceManager->get(BlockLayoutManager::class);
        return MessageBox::redirectMgs(
            'Save success to : listener_insert_layout.php',
            $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'applyblocklayout']),
            '',
            2
        );


    }

    function applyblocklayoutedit($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);


        /**
         * @var $blacklayoutManager BlockLayoutManager
         * @var $eventblock BlockLayout
         */
        $blacklayoutManager = $this->serviceManager->get(BlockLayoutManager::class);
        $eventblock = $blacklayoutManager->find($id);
        if ($eventblock == null)
            return MessageBox::redirectMgs(
                'Not Found Item',
                $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'applyblocklayout']),
                '',
                2
            );

        $form = new FormInsertBlock('edit');


        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $params = $this->ctrl->params();

            $action = $params->fromPost('action','Save');
            if ($action == 'Delete')
            {
                return $this->ctrl->askBeforeDone(
                    'Delete Event?',
                    $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'applyblocklayoutdelete', 'id' => $id]),
                    ['id' => $id, 'deleteblock' => $id]
                );
            }


            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $eventblock->exchangeArray($data);
                $data = $eventblock->getArrayCopy();
                unset($data['ControllerAction']);

                $blacklayoutManager->update($data, $id);
                return MessageBox::redirectMgs(
                    'Save success',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'applyblocklayout']),
                    'Save',
                    2
                );

            }
        }
        else
            $form->setData($eventblock->getArrayCopy());


        $valrt =  [
            'form' => $form,
            'eventblock' => $eventblock
        ];

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables($valrt);
        return $view;

    }

    function applyblocklayoutdelete($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $blacklayoutManager BlockLayoutManager
         * @var $eventblock BlockLayout
         */
        $blacklayoutManager = $this->serviceManager->get(BlockLayoutManager::class);
        $eventblock = $blacklayoutManager->find($id);
        if ($eventblock == null)
            return MessageBox::redirectMgs(
                'Not Found Item',
                $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'applyblocklayout']),
                '',
                2
            );


        if ($this->ctrl->isConfirm( ['id' => $id, 'deleteblock' => $id]))
        {
            $blacklayoutManager->delete($id);
            return MessageBox::redirectMgs(
                'Deleted',
                $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'applyblocklayout']),
                'Save',
                2
            );

        }
        return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'applyblocklayout']);


    }


    function applyblocklayoutadd()
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $blacklayoutManager BlockLayoutManager
         * @var $eventblock BlockLayout
         */
        $blacklayoutManager = $this->serviceManager->get(BlockLayoutManager::class);

        $form = new FormInsertBlock('edit');


        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $params = $this->ctrl->params();


            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $ev = new BlockLayout();
                $ev->exchangeArray($data);
                $data = $ev->getArrayCopy();
                unset($data['ControllerAction']);
                $blacklayoutManager->insertnew($data);
                return MessageBox::redirectMgs(
                    'Create success',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'applyblocklayout']),
                    'Save',
                    2
                );

            }
        }



        $valrt =  [
            'form' => $form
        ];


        $view = $this->createView(__DIR__, __CLASS__, 'applyblocklayoutedit', 'AddBlockHTML');
        $view->setVariables($valrt);

        return $view;

    }

    //VIEWPLACE
    function viewplace($id) {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $viewplaceManager ViewPlaceManager
         */
        $viewplaceManager = $this->serviceManager->get(ViewPlaceManager::class);


        $listeventblock = $viewplaceManager->allClass();

        if ($this->ctrl->getRequest()->isPost())
        {
            $viewplaceManager->exportconfig();
            return MessageBox::redirectMgs(
                'Save to config file',
                $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout']),
                'Save',
                2
            );
        }

        //Load file
        $loadfromfiles = function ($dir, $sub = null) use (&$loadfromfiles)
        {
            $rs = [];
            foreach (new \DirectoryIterator($dir) as $fileInfo) {

                if($fileInfo->isDot()) continue;

                if ($fileInfo->isDir())
                {
                    if ($sub == null)
                        $rs = array_merge($loadfromfiles($dir.DIRECTORY_SEPARATOR.$fileInfo->getFilename(), $fileInfo->getFilename()), $rs);
                }

                else {

                    $nameclss = explode('.', $fileInfo->getFilename())[0];
                    if ($sub != null) {
                        $nameclss = $sub . '\\' . $nameclss;
                    }


                    $class = \Zend\Code\Generator\ClassGenerator::fromReflection(
                        new \Zend\Code\Reflection\ClassReflection("XT\Core\Event\Listener\\" . $nameclss)
                    );

                    $rs[] = [
                        'class' =>'XT\Core\Event\Listener\\'.(($sub)?($sub.'\\'):'') . $class->getName(),
                        //'code' => $class->getMethod('execute')->getBody(),
                        //'whatdo' => $class->getDocBlock()->getLongDescription()
                    ];
                }


            }

            return $rs;
        };


        $defaultplugins = $loadfromfiles(realpath(__DIR__ .'/../../Event/Listener')); //Common::$cf->AHD->common->rootvendor


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['listeventblock' => $listeventblock, 'defaultplugins' => $defaultplugins]);
        return $view;

    }

    function applyviewplace($id) {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $viewplaceManager ViewPlaceManager
         */
        $viewplaceManager = $this->serviceManager->get(ViewPlaceManager::class);

        if ($this->ctrl->isConfirm(['applyviewplace'=>'applyviewplace']))
        {
            $viewplaceManager->exportconfig();
            return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'viewplace']);
        }

        return $this->ctrl->askBeforeDone(
            'Save to config?',
            $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'applyviewplace']),
            ['applyviewplace'=>'applyviewplace']
        );
    }

    function viewplaceedit($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $viewplaceManager ViewPlaceManager
         * @var $viewplace ViewPlace
         */
        $viewplaceManager = $this->serviceManager->get(ViewPlaceManager::class);
        $viewplace = $viewplaceManager->find($id);
        if ($viewplace == null)
            return MessageBox::redirectMgs(Common::translate('Not found'),
                $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'viewplace']),
                '',
                5
            );


        $form = new FormViewPlace('edit');
        $request = $this->ctrl->getRequest();
        if ($request->isPost())
        {
            $params = $this->ctrl->params();

            $action = $params->fromPost('action','Save');
            if ($action == 'Delete')
            {
                return $this->ctrl->askBeforeDone(
                    'Delete Event?',
                    $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'viewplacedelete', 'id' => $id]),
                    ['id' => $id, 'viewplacedelete' => $id]
                );
            }


            $form->setData($request->getPost());
            if ($form->isValid())
            {
                $data = $form->getData();
                $viewplace->exchangeArray($data);
                $data = $viewplace->getArrayCopy();
                unset($data['ControllerAction']);

                $viewplaceManager->update($data, $id);
                return MessageBox::redirectMgs(
                    'Save success',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'viewplace']),
                    'Save',
                    2
                );

            }
        }
        else
            $form->setData($viewplace->getArrayCopy());


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['form' => $form, 'viewplace' => $viewplace]);
        return $view;

    }

    function viewplaceadd()
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $viewplaceManager ViewPlaceManager
         * @var $viewplace ViewPlace
         */
        $viewplaceManager = $this->serviceManager->get(ViewPlaceManager::class);
        $viewplace =  new ViewPlace();

        $form = new FormViewPlace('add');
        $request = $this->ctrl->getRequest();
        if ($request->isPost())
        {
            $params = $this->ctrl->params();



            $form->setData($request->getPost());
            if ($form->isValid())
            {
                $data = $form->getData();
                $viewplace->exchangeArray($data);
                $data = $viewplace->getArrayCopy();
                unset($data['ControllerAction']);
                unset($data['id']);

                $viewplaceManager->insertnew($data);
                return MessageBox::redirectMgs(
                    'Save success',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'viewplace']),
                    'Save',
                    2
                );

            }
        }
        else
            $form->setData($viewplace->getArrayCopy());


        $view = $this->createView(__DIR__, __CLASS__, 'viewplaceedit', 'viewplaceadd');
        $view->setVariables(['form' => $form, 'viewplace' => $viewplace]);
        return $view;

    }

    function viewplacedelete($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $viewplaceManager ViewPlaceManager
         * @var $viewplace ViewPlace
         */
        $viewplaceManager = $this->serviceManager->get(ViewPlaceManager::class);


        if ($this->ctrl->isConfirm( ['id' => $id, 'viewplacedelete' => $id]))
        {
            $viewplaceManager->delete($id);
            return MessageBox::redirectMgs(
                'Deleted',
                $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'viewplace']),
                'Save',
                2
            );

        }
        return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'viewplace']);


    }


    //GLOABAL LISTENR
    function listenerglobal($i)
    {


        /***
         * @var $globallistenermanager GlobalListenerManager
         * @var $dbListener GlobalListener
         */


        $globallistenermanager  = $this->serviceManager->get(GlobalListenerManager::class);
        $_dbListeners           = $globallistenermanager->allListener();

        $dbListeners = [];
        $localListeners         = TemplateFiles::getGlobalListener();

        //Remove element have in Db
        foreach ($_dbListeners as $dbListener) {

            $dbListeners[$dbListener->getClassname()] = $dbListener;
            if (isset($localListeners[$dbListener->getClassname()]))
                unset($localListeners[$dbListener->getClassname()]);
        }







        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['dbListeners' => $dbListeners, 'localListeners' => $localListeners]);
        return $view;


    }

    function editgloballistener($i)
    {

        /***
         * @var $globallistenermanager GlobalListenerManager
         * @var $dbListener GlobalListener
         */
        $globallistenermanager  = $this->serviceManager->get(GlobalListenerManager::class);


        $dbListener = $globallistenermanager->findbyClass($i);


        if ($dbListener == null) {

            $localListeners         = TemplateFiles::getGlobalListener();
            if (isset($localListeners[$i]))
            {

                $dbListener = $localListeners[$i];
                $globallistenermanager->savetoDiskListener($i, $dbListener->getDescription(), $dbListener->getCode());
                $data = $dbListener->getArrayCopy();
                unset($data['id']);

                $globallistenermanager->insertnew($data);
                return MessageBox::redirectMgs(
                    'New Listener create from System',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'editgloballistener' , 'id' => $i]),
                    'Save',
                    2
                );
            }


            return MessageBox::redirectMgs(
                'Not found',
                $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'listenerglobal']),
                'Save',
                2
            );
        }

        $form = new FormGlobalListener('edit');
        $req = $this->ctrl->getRequest();
        if ($req->isPost())
        {



            if ($req->getPost('action') == 'Delete')
            {
                return $this->ctrl->askBeforeDone(
                    'Delete Listener?'.$dbListener->getClassname(),
                    $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'deletegloballistener', 'id' => $i]),
                    ['id' => $i, 'delete' => $i]
                );
            }

            $form->setData($req->getPost());


            //No Exist Class with name before update
            $validatorchain = $form->getInputFilter()->get('classname')->getValidatorChain();
            $validator_checkdatabase = new NoRecordExists(
                [
                    'table' => 'global_listener',
                    'field' => 'classname',
                    'adapter' => $this->dbAdapter
                ]
            );

            $validator_checkdatabase->setExclude(['field'=>'id', 'value' => $dbListener->getId()]);
            $validatorchain->addValidator($validator_checkdatabase);




            if ($form->isValid()) {
                $dbListener->exchangeArray($form->getData());
                $data = $dbListener->getArrayCopy();

                $globallistenermanager->update($data, $dbListener->getId());
                return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'listenerglobal']);


            }
        }
        else
            $form->bind($dbListener);


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['dbListener' => $dbListener, 'form' => $form]);
        return $view;


    }

    function deletegloballistener($id)
    {
        /***
         * @var $globallistenermanager GlobalListenerManager
         */
        $globallistenermanager  = $this->serviceManager->get(GlobalListenerManager::class);

        $dbListener = $globallistenermanager->findbyClass($id);


        if ($dbListener == null) {


            return MessageBox::redirectMgs(
                'Not found',
                $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'listenerglobal']),
                'Save',
                2
            );
        }

        if ($this->ctrl->isConfirm(['id' => $id, 'delete' => $id]))
        {
            $globallistenermanager->delete($dbListener->getId());
        }
        return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'listenerglobal']);


    }

    function createglobalistener($i)
    {

        /***
         * @var $globallistenermanager GlobalListenerManager
         * @var $dbListener GlobalListener
         */
        $globallistenermanager  = $this->serviceManager->get(GlobalListenerManager::class);


        $dbListener = new GlobalListener();



        $form = new FormGlobalListener('add');
        $req = $this->ctrl->getRequest();
        if ($req->isPost())
        {


            $form->setData($req->getPost());


            //No Exist Class with name before update
            $validatorchain = $form->getInputFilter()->get('classname')->getValidatorChain();
            $validator_checkdatabase = new NoRecordExists(
                [
                    'table' => 'global_listener',
                    'field' => 'classname',
                    'adapter' => $this->dbAdapter
                ]
            );

//            $validator_checkdatabase->setExclude(['field'=>'id', 'value' => $dbListener->getId()]);
            $validatorchain->addValidator($validator_checkdatabase);




            if ($form->isValid()) {
                $dbListener->exchangeArray($form->getData());
                $data = $dbListener->getArrayCopy();

                $globallistenermanager->insertnew($data);
                return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'listenerglobal']);


            }
        }
        else
            $form->bind($dbListener);


        $view = $this->createView(__DIR__, __CLASS__, 'editgloballistener', 'createglobalistener');
        $view->setVariables(['form' => $form]);
        return $view;


    }


}