<?php

namespace XT\Core\Admin\Layout;



use XT\Admin\Controller\AbstractPlugin;
use XT\Core\Common\Common;
use XT\Core\Event\InsertHtml\InsertHtmlManager;
use XT\Core\System\Placeholder\Placeholder;
use XT\Core\System\Placeholder\PlaceholderManager;
use XT\Core\ToolBox\MessageBox;
use XT\Core\System\RBAC_PERMISSION;
use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\StringTrim;
use Zend\I18n\Filter\Alpha;
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

    function index($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted'). ' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);


        $ar = [];

        $ar['placeholder'] = [
            'name' => 'PlaceHolder',
            'description' => 'PlaceHolder for View, mocking events, insert block HTML ...'
        ];
 




        $ar['layoutedit'] = [
            'name' => 'PHTML',
            'description' => 'Edit .phtml (template) files'
        ];



        $ar['applyblocklayout'] = [
            'name' => 'Block layout',
            'description' => 'Áp dụng block HTML vào layout'

        ];

        $ar['eventinserthtml'] = [
            'name' => 'Inset PHTML',
            'description' => 'Insert file.html to placeholder by trigger event'

        ];

        $ar['editslide'] = [
            'name' => 'Biên tập Slide',
            'description' => 'Biện tập slide ở trang chủ'

        ];


        $ar['pluginview'] = [
            'name' => 'Tích hợp Plugin View',
            'description' => 'Tích hợp các Plugin View vào template'

        ];

        $ar['listenerglobal'] = [
            'name' => 'Các Listener Global',
            'description' => 'Tích hợp các Plugin Listener vào template'

        ];


        $view->setVariables(['list' => $ar]);
        return $view;
    }



    function getValidator()
    {
        if ($this->validator == null)
        {
            $this->validator = new ValidatorChain();
            $this->validator->attach(new StringLength(['encoding' => 'UTF-8', 'min'=>'3', 'max' => 14]));


        }
        return $this->validator;
    }

    function getFilter()
    {
        if ($this->filter == null)
        {
            $this->filter = new FilterChain(
                ['filters' => [

                    ['name' => StringToLower::class,
                        'options' => [
                            'encoding' => 'UTF-8'
                        ]
                    ],

                    ['name' => StringTrim::class]

                ]
                ]
            );
        }
        return $this->filter;
    }



    function layoutedit()
    {
       if (!$this->ctrl->isGranted(Layout::RBAC_LAYOUT_EDIT))
           return MessageBox::viewNoPermission($this->ctrl->getEvent(), 'Không cấp quyền '.Layout::RBAC_LAYOUT_EDIT);

       $cdir = Common::$cf->AHD->common->pathtemplatedefault;

       $fileedit = '';
       $filecontent = '';
       $files = [];
       $action  = '';

       $request = $this->ctrl->getRequest();
       if ($request->isPost())
       {
           $fileedit = $this->ctrl->params()->fromPost('file','');
           $action = $this->ctrl->params()->fromPost('action','Edit');
           $filecontent = $this->ctrl->params()->fromPost('filecontent','');
       }

       if ($action == 'Edit')
       {
           $filecontent = file_get_contents($cdir.'/'.$fileedit);
       }
       else if ($action == 'Save')
       {
           //chmod($cdir.'/'.$fileedit,0777);
           $filename = realpath($cdir.'/'.$fileedit);
           $byte = file_put_contents($filename, $filecontent);
           if ($byte === false)
               throw new \Exception("Can not save: $filename");

           //$fileedit = '';
       }
       else if ($action == 'Delete')
       {
           $candel = true;
           $cfile = 'html/'.$fileedit;
           $rows = $this->ctrl->dbAdapter->get_rows_select($this->ctrl->dbAdapter->sql->select('blockhtmlevent')->columns(['Block']));
           foreach ($rows as $ctrl)
               if ($cfile == $ctrl['Block'])
               {
                   $candel = false;
               }
           if ($candel) @unlink($cdir.'/'.$fileedit);
           $fileedit = '';



       }
       else if ($action == 'New')
       {
           $newfile = $this->getFilter()->filter($fileedit);
           $newfile = $cdir.'/'.$newfile;


           if ((!file_exists($newfile)))
           {
               @mkdir(pathinfo($newfile,PATHINFO_DIRNAME));
               $byte = file_put_contents($newfile, 'NEW FILE BLOCK');
               if ($byte === false)
                   throw new \Exception("Can not save: $newfile");
           }

       }



       $folders = [];
       $getfile = function($cdir,$sub='') use(&$files,&$getfile) {
           $handle = opendir($cdir);
           if ($handle)
           {

               while (false !== ($entry = readdir($handle)))
               {
                   if ($entry != "." && $entry != ".." && $entry != '.htaccess')
                   {
                       if (is_dir($cdir . '/' . $entry))
                       {

                           $getfile($cdir.'/'.$entry,$sub.'/'.$entry);// $folders[] = $entry;
                       }
                       else
                           $files[] = $sub.'/'.$entry;
                   }
               }

               closedir($handle);

           }
       };
       $getfile($cdir);
       sort($files);

       //Group by folder
       $fs = [];
       foreach ($files as $file) {
           $f = explode('/', $file);
           $folder = $f[1];
           if (!isset($fs[$folder]))
               $fs[$folder] = [];

           unset($f[0], $f[1]);
           $f = implode('/', $f);


           $fs[$folder][$f] = $file;
       }






       $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
       $view->setVariables([
           'files' => $fs,
           'fileedit' => $fileedit,
           'filecontent' =>$filecontent
       ]);
       return $view;
    }





    function applyblocklayout()
    {


        if (!$this->ctrl->isGranted(Layout::RBAC_LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), 'Không cấp quyền '.Layout::RBAC_LAYOUT_EDIT);
        /**
         * @var $blcoklayoutManager BlockLayoutManager
         */
        $blcoklayoutManager = $this->serviceManager->get(BlockLayoutManager::class);
        $listeventblock = $blcoklayoutManager->allBlocks();

        if ($this->ctrl->getRequest()->isPost()) {

            $blcoklayoutManager->exportconfig();

            return $this->ctrl->returnRedirect(
                'Đã lưu dữ liệu ra file block-layout-config.php',
                'Thông báo',
                $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'applyblocklayout']),
                2
            );

        }
        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['listeventblock'=>$listeventblock]);
        return $view;
    }

    function applyblocklayoutedit($id)
    {

        if (!$this->ctrl->isGranted(Layout::RBAC_LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), 'Không cấp quyền '.Layout::RBAC_LAYOUT_EDIT);
        /**
         * @var $blcoklayoutManager BlockLayoutManager
         */
        $blcoklayoutManager = $this->serviceManager->get(BlockLayoutManager::class);
        /**
         * @var $holderManager HolderManager
         */
        $holderManager = $this->serviceManager->get(HolderManager::class);


        $id = $this->ctrl->params()->fromRoute('id');
        $blocklayout = $blcoklayoutManager->find($id);
        if ($blocklayout) $blocklayout = $blocklayout->getArrayCopy();


        $holder = '';
        $request = $this->ctrl->getRequest();

        if ($request->isPost())
        {
            $params = $this->ctrl->params();
            $action = $params->fromPost('action','Save');



            if ($this->ctrl->isConfirm(['id' => $id]))
            {
                $blcoklayoutManager->delete($id);
                return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'applyblocklayout']);
            }

            if ($action == 'Del')
            {
               return $this->ctrl->askBeforeDone('Xóa áp dụng Block layout',
                                   $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'applyblocklayoutedit', 'id' => $id]),
                   ['id' => $id]

                   );

            }

            $post = $request->getPost();
            $controlaction = $post->controlaction;
            $controlaction = explode('-',$controlaction);

            $data = [
                'Controller' => $controlaction[0],
                'Action' => $controlaction[1],
                'Block' => $post->Block,
                'active' =>(bool)$post->active,
                'holder' => $post->holder
            ];



            if ($id > 0) {
                $blcoklayoutManager->update($data, $id);
                $blocklayout = $blcoklayoutManager->find($id)->getArrayCopy();
                return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'applyblocklayout']);
            }
             else
            {
                $id = $blcoklayoutManager->insertnew($data);
                var_dump($id);

                return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'applyblocklayoutedit', 'id' => $id]);
            }

        }

        //Buiding template from directory
        $cdir = Common::$cf->AHD->common->pathtemplatedefault;



        $selectoption = '<select name="Block" class="form-control">';
        $dir = realpath(Common::$cf->AHD->common->pathtemplatedefault);
        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if ($fileInfo->isDir())
            {

                foreach (new \DirectoryIterator($fileInfo->getPathname()) as $fileInfonext)
                {
                    if($fileInfonext->isDot()||$fileInfonext->isDir()) continue;


                    $entry = $fileInfo->getBasename().'/'.$fileInfonext->getFilename();
                    $selected = '';
                    if ($blocklayout['Block'] == $entry) $selected = ' selected="selected"';
                    $options[$entry] = [
                        'name'      => $entry,
                        'select'    => $selected,
                        'class'     => ''
                    ];
                }
            }
        }
        $systemtemplatedir = realpath(__DIR__.'/../../template');

        foreach (new \DirectoryIterator($systemtemplatedir) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if ($fileInfo->isDir())
            {

                foreach (new \DirectoryIterator($fileInfo->getPathname()) as $fileInfonext)
                {
                    if($fileInfonext->isDot()||$fileInfonext->isDir()) continue;


                    $entry = $fileInfo->getBasename().'/'.$fileInfonext->getFilename();
                    $selected = '';
                    if ($blocklayout['Block'] == $entry) $selected = ' selected="selected"';
                    if (isset($options[$entry]))
                    {

                        $options[$entry] = [
                            'name'      => $entry .' (overrid)',
                            'select'    => $selected,
                            'class'     => " class='text-danger' "
                        ];
                    }
                    else
                    {

                        $options[$entry] = [
                            'name'      => $entry .' (in system)',
                            'select'    => $selected,
                            'class'     => " class='text-primary' "
                        ];
                    }
                }



            }
        }

        foreach ($options as $key => $option) {
            $selectoption .= "<option $option[class] value=\"$key\"$option[select]>$option[name]</option>";
        }

        $selectoption .= '</select>';





        $event = new ContainerParams($blocklayout);
        if ($event->isempty())
        {
            $event['id'] = 0;
            $event['Controller'] = '';
            $event['Block'] = '';
            $event['Action'] = '';
            $event['holder'] ='';
            $event['active'] = '1';
        }

        $curentct = $event['Controller'].'-'.$event['Action'];
        $controlaction = $this->getoptioncontrollerandaction('controlaction',$curentct);


        $holder = '';
        $holder = '<select name="holder" class="form-control controlaction">';

        $curentct = '';
        $curentct = $event['holder'];


        foreach ($holderManager->allHolder() as $ob) {
            $val = $ob['name'];
            $selected = '';
            if ($curentct == $val) $selected = ' selected="selected"';
            $holder .= "<option value=\"$val\"$selected>$val</option>";
        }
        $holder .= '</select>';



        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables([
            'event' => $event,
            'selectfile' =>$selectoption,
            'selectevent'=>null,
            'controlaction' =>$controlaction,
            'holder' =>$holder
        ]);
        return $view;
    }

    function getlistcontroller()
    {
        if (!$this->ctrl->isGranted(Layout::RBAC_LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), 'Không cấp quyền '.Layout::RBAC_LAYOUT_EDIT);

        $controls= $this->serviceManager->get('config')['controllers'];
        $controller = [];
        foreach ($controls as $key => $cc) {
            if (!isset($controller[$key]))
                $controller[$key] = [];
            foreach ($cc as $k=>$c)
                $controller[$key][$k] =  $k;

        }
        if (!isset($controller['invokables']))
            $controller['invokables'] = [];

        $controller = array_merge(['ALL' => 'ALL',], $controller['invokables'], $controller['factories']);
        return $controller;

    }

    function getlistactions($controllername) {
        if (!$this->ctrl->isGranted(Layout::RBAC_LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), 'Không cấp quyền '.Layout::RBAC_LAYOUT_EDIT);
        if ($controllername == 'ALL')
            return ['ALL', 'notfound'];

            $ctrls = \Common::$sm->get('ControllerManager');
            if (!$ctrls->has($controllername))  return [];
            $ct = $ctrls->get($controllername);
            $controllerClass = get_class($ct);

            $reflection = new \ReflectionClass($ct);
            $actions = ['*'];
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $methodName = $method->getName();
                if ($methodName == 'getMethodFromAction') {
                    continue;
                }
                if (substr_compare($methodName, 'Action', -strlen('Action')) === 0) {
                    $actions[] = substr($methodName, 0, strlen($methodName) - 6);
                }
            }

            return $actions;

    }

    public function getoptioncontrollerandaction($name='controlaction', $curentct='')
    {
        if (!$this->ctrl->isGranted(Layout::RBAC_LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), 'Không cấp quyền '.Layout::RBAC_LAYOUT_EDIT);

        $dscontroller = $this->getlistcontroller();


        $controlaction = "<select name=\"$name\" class=\"form-control controlaction\">";


        $obs = $dscontroller;




        foreach ($obs as $namecontroller => $class) {
            $action = $this->getlistactions($namecontroller);

            $controlaction .="<optgroup label=\"$namecontroller\">";
            foreach ($action as $ac)
            {



                $val = $namecontroller.'-'.$ac;
                $selected = '';
                if ($curentct == $val) $selected = ' selected="selected"';
                $controlaction .= "<option value=\"$val\"$selected>$val</option>";
            }
            $controlaction .="</optgroup>";

        }
        $controlaction .= '</select>';
        return $controlaction;
    }

    public function buildingoptionslistevent($value = '')
    {
        /**
         * @var $holderManager HolderManager
         */
        $holderManager = $this->serviceManager->get(HolderManager::class);
        $obs = $holderManager->allHolder();
        $listevent = new \ArrayObject();
        $option = '<select name="Event" class="form-control">';
        foreach ($obs as $ob) {
            $selected = '';
            $name = 'start-ahdesign-render-'.$ob['name'];
            if ($value == $name) $selected = ' selected="selected"';
            $option .= "<option value=\"$name\"$selected>$name</option>";
            $selected = '';
            $name = 'end-ahdesign-render-'.$ob['name'];
            if ($value == $name) $selected = ' selected="selected"';
            $option .= "<option value=\"$name\"$selected>$name</option>";

        }
        $option .= '</select>';
        return $option;

    }


    function editslide($id)
    {
        if (!$this->ctrl->isGranted(Layout::RBAC_LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), 'Không cấp quyền '.Layout::RBAC_LAYOUT_EDIT);

        $vars = \Ichte\Core\Uicoms\Slide\SlideEdit::editslideAction($this->ctrl);


        if (($vars instanceof ViewModel) || ($vars instanceof \Zend\Http\PhpEnvironment\Response))
            return $vars;

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);

        $view->setVariables($vars);
        return $view;
    }

    function pluginview() {
        return $this->ctrl->redirect()->toUrl($this->url('pluginview'));
    }

    function listenerglobal() {
        return $this->ctrl->redirect()->toUrl($this->url('pluginview', 'listenerglobal'));
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
                        'Xóa keyholder: '.$item->getName(),
                        $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'holderdelete', 'id' =>$id]),
                        ['id' => $id, 'confirmdel' => 'confirmdel']

                    );
                }
                $item = [
                    'name' => $post->name,
                    'description' => $post->description
                ];

                $placeholderManager->update($item, $id);

                return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder', 'id' =>$id]);

            }


            $view = $this->createView(__DIR__, __CLASS__, 'holderedit');
            $view->setVariables(['item' => $item]);
            return $view;

        }



        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['listevent'=>$listevent,'item' => $item]);
        return $view;
    }

    function newholder()
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);
        /**
         * @var $holderManager PlaceholderManager
         */
        $holderManager = $this->serviceManager->get(PlaceholderManager::class);

        $id = $holderManager->createnewholder();
        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder', 'id' =>$id]);
    }


    function eventinserthtml($i)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' ' .RBAC_PERMISSION::LAYOUT_EDIT);

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
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' ' .RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $inserthtmlmanager InsertHtmlManager
         */
        $inserthtmlmanager = $this->serviceManager->get(InsertHtmlManager::class);


        $ev = $inserthtmlmanager->find($id);
        if ($ev)
            $ev = $ev->getArrayCopy();

        $request = $this->ctrl->getRequest();
        if ($request->isPost())
        {
            $params = $this->ctrl->params();
            $action = $params->fromPost('action','Save');
            $geturl = $params->fromPost('geturl');

            if ($this->ctrl->isConfirm(['id' => $id]))
            {
                $inserthtmlmanager->delete($id);
                return $this->ctrl->returnRedirect(
                    'Đã xóa một Event Insert HTML',
                    'Save',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']),
                    2
                );
            }

            if ($action == 'Del')
            {
                return $this->ctrl->askBeforeDone(
                    'Xóa Insert HTML?',
                    $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'eventinserthtmledit', 'id' => $id]),
                    ['id' => $id]
                );
            }

            $post = $request->getPost();
            $controlaction = $post->controlaction;
            $controlaction = explode('-',$controlaction);

            $data = [
                'Controller' => $controlaction[0],
                'Action' => $controlaction[1],
                'Event' => $post->Event,
                'Block' => $post->Block,
                'active' =>(bool)$post->active
            ];
            if ($id>0)
            {
                $inserthtmlmanager->update($data, $id);
                return $this->ctrl->returnRedirect(
                    'Đã lưu dữ liệu',
                    'Save',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']),
                    2
                );
            }
            else
            {
                $id = $inserthtmlmanager->insertnew($data);
                return $this->ctrl->returnRedirect(
                    'Đã tạo mới thành công',
                    'Chèn HTML',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']),
                    2
                );
            }

            return $this->redirect()->toRoute('admin', ['plugin'=>'layout']);

        }

        $cdir = Common::$cf->AHD->common->pathtemplatedefault.'/html';


        $options = [];

        $selectoption = '<select name="Block" class="form-control">';
        $dir = realpath(Common::$cf->AHD->common->pathtemplatedefault);
        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if ($fileInfo->isDir())
            {

                foreach (new \DirectoryIterator($fileInfo->getPathname()) as $fileInfonext)
                {
                    if($fileInfonext->isDot()||$fileInfonext->isDir()) continue;


                    $entry = $fileInfo->getBasename().'/'.$fileInfonext->getFilename();
                    $selected = '';
                    if ($ev['Block'] == $entry) $selected = ' selected="selected"';
                    $options[$entry] = [
                        'name'      => $entry,
                        'select'    => $selected,
                        'class'     => ''
                    ];
                }
            }
        }
        $systemtemplatedir = realpath(__DIR__.'/../../template');

        foreach (new \DirectoryIterator($systemtemplatedir) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if ($fileInfo->isDir())
            {

                foreach (new \DirectoryIterator($fileInfo->getPathname()) as $fileInfonext)
                {
                    if($fileInfonext->isDot()||$fileInfonext->isDir()) continue;


                    $entry = $fileInfo->getBasename().'/'.$fileInfonext->getFilename();
                    $selected = '';
                    if ($ev['Block'] == $entry) $selected = ' selected="selected"';
                    if (isset($options[$entry]))
                    {

                        $options[$entry] = [
                            'name'      => $entry .' (overrid)',
                            'select'    => $selected,
                            'class'     => " class='text-danger' "
                        ];
                    }
                    else
                    {

                        $options[$entry] = [
                            'name'      => $entry .' (in system)',
                            'select'    => $selected,
                            'class'     => ''
                        ];
                    }
                }



            }
        }

        foreach ($options as $key => $option) {
            $selectoption .= "<option $option[class] value=\"$key\"$option[select]>$option[name]</option>";
        }

        $selectoption .= '</select>';


        $event = $ev;
        if ($ev == null)
        {
            $event['id'] = 0;
            $event['Controller'] = '';
            $event['Event'] = '';
            $event['Block'] = '';
            $event['Action'] = '';
            $event['active'] = '1';
        }

        $curentct = $event['Controller'].'-'.$event['Action'];

        $curentct = $event['Controller'].'-'.$event['Action'];
        $controlaction = $this->getoptioncontrollerandaction('controlaction',$curentct);

        $valrt =  [
            'event' => $event,
            'selectfile' =>$selectoption,
            'selectevent'=>$this->buildingoptionslistevent($event['Event']),
            'controlaction' =>$controlaction
        ];




        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables($valrt);
        return $view;

    }


}