<?php

namespace XT\Core\Admin\Common;


use XT\Admin\Controller\AbstractPlugin;
use XT\Core\Common\Common;
use XT\Core\ToolBox\MessageBox;
use XT\Core\System\RBAC_PERMISSION;
use Zend\Db\Sql\Select;
use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\StringTrim;
use Zend\I18n\Filter\Alpha;
use Zend\Log\Logger;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;

class BasicCommand extends AbstractPlugin
{

    protected $nameplugin = 'Basic';

    protected $description = 'Basic Settings';


    function index($i)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::BASIC_SETTING))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), RBAC_PERMISSION::BASIC_SETTING);


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);




        $view->setVariables(['list' => $this->getListactionIndex()]);
        return $view;
    }

    public function intPlugin()
    {
        $ar = [];
        $ar['clearcache'] = [
            'name' => 'Xóa cache hệ thống',
            'description' => 'Xóa cache để hệ thống render lại theo dữ liệu mới nhất',
            'index' => true
        ];
        $ar['siteinfo'] = [
            'name' => 'Thông tin tham số website',
            'description' => 'Các tham số nhân của website',
            'index' => true
        ];
        $ar['templatemap'] = [
            'name' => 'Các Template Map',
            'description' => 'Tham số trỏ đến các file .phtml của hệ thống',
            'index' => true
        ];
        $ar['favicon'] = [
            'name' => 'Đổi biểu tượng Favicon',
            'description' => 'Đổi biểu tượng Favicon',
            'index' => true
        ];
        $ar['logo'] = [
            'name' => 'Đổi Logo',
            'description' => 'Thay đổi logo của website',
            'index' => true
        ];
        $ar['listeventname'] = [
            'name' => 'Event',
            'description' => 'Tên các event trong hệ thống',
            'index' => true
        ];
        $ar['memcached'] = [
            'name' => 'MemCached',
            'description' => 'Thông tin về Memcached',
            'index' => true
        ];

        $ar['opcache'] = [
            'name' => 'OPcache',
            'description' => 'Thông tin về OPcache',
            'index' => true
        ];

        $ar['log'] = [
            'name' => 'Logs',
            'description' => 'Activity logs',
            'index' => true
        ];


        $ar['help'] = [
            'name' => 'Help',
            'description' => 'Thông tin Code',
            'index' => true
        ];

        $this->setListaction($ar);
    }




    function clearcache()
    {
        if (!$this->ctrl->isGranted(AdminController::RBAC_ADMIN_INDEX_PAGE))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), AdminController::RBAC_ADMIN_INDEX_PAGE);

        /**
         * @var $cache \Zend\Cache\Storage\Adapter\Filesystem
         */
        $serviceManager = $this->serviceManager;
        Common::clearCache();
        Common::simpleRemoveCache('*.cache');

        //Create Cache Footer
        $pagefoots = Common::getDbAdapter()->execute("select url,anchor from pages where active = 1 and infooter=1");
        $html = '';
        foreach ($pagefoots as $page)
        {
            $html .=  '<a href="'.$this->ctrl->url()->fromRoute('page',['page'=>$page['url']]).'">'.$page['anchor'].'</a>';
        }
        Common::simpleCacheSave('cache_data_url_pages_footer', ['html'=>$html]);

        $url = $this->ctrl->plugin('url')->fromRoute('admin', ['plugin'=>'basiccommand']);
        return $this->ctrl->returnRedirect('Xóa cache', 'Đã xóa cache', $url, 0);
    }

    function templatemap()
    {
        if (!$this->ctrl->isGranted(AdminController::RBAC_ADMIN_INDEX_PAGE))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), AdminController::RBAC_ADMIN_INDEX_PAGE);

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        return $view;
    }

    function siteinfo()
    {
        /*

        $options = [
            'ttl' => 600,
            'servers' => [
                '127.0.0.1',
                '11211'
            ],
            'lib_options' => [
                \Memcached::OPT_PREFIX_KEY => 'AVC'
            ]
        ];

        $cache = new \Zend\Cache\Storage\Adapter\Memcached($options);

        $cache->setItem('xxxx', ['asdfs', 'sdfsd']);
        */



        if (!$this->ctrl->isGranted(AdminController::RBAC_ADMIN_INDEX_PAGE))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), AdminController::RBAC_ADMIN_INDEX_PAGE);

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        return $view;
    }

    function logo()
    {
        if (!$this->ctrl->isGranted(AdminController::RBAC_ADMIN_INDEX_PAGE))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), AdminController::RBAC_ADMIN_INDEX_PAGE);

        $uploader = new Uploader('uploadlogowebsite', false,
            $this->url('basiccommand', 'logo'),
            null,
        '',
        false
            );

        $respone = $this->ctrl->getResponse();
        if ($this->ctrl->getRequest()->isPost()) {
            $data = array_merge(
                $this->ctrl->params()->fromPost(),
                $this->ctrl->params()->fromFiles()
            );



            if (file_exists('public/img/logo.png'))
            {
                copy('public/img/logo.png', 'public/img/logo.png.backup'.time());
            };

            $option = [
                'IsImage' => true,
                'extension' => '*',//or ['jpg', 'gif' ...]
                'filter' => [
                    'target' => 'public/img', //Thư mục
                    'use_upload_name' => true, //Sử dụng tên file gốc
                    'use_upload_extension' => true,//Sử dụng tên file gốc
                    'overwrite' => true,
                    'dbAdapter' => null,
                    'user' => $this->ctrl->identity(),
                    'extractcolor' => false,
                    'createthumb' => false,
                    'widththumb' => 50,
                    'valuesInsertDb' => [
                    ],
                    'fixfullname' =>  'logo.png',
                    'fixthumbname' => null,
                ]
            ];
            $datas = $uploader->process($option, $data);

            $respone->setContent(json_encode([
                'code' => 200,
                'message' => 'Upload Success',
                'numberfile' => 1,
                'list' => []
            ]));

            return $respone;
        }
        else
        {



        }



        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['uploader' => $uploader]);
        return $view;
    }

    function favicon()
    {
        if (!$this->ctrl->isGranted(AdminController::RBAC_ADMIN_INDEX_PAGE))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), AdminController::RBAC_ADMIN_INDEX_PAGE);

        $uploader = new Uploader('faviconupload', false,
            $this->url('basiccommand', 'favicon'),
            null,
            '',
            false
        );

        $respone = $this->ctrl->getResponse();
        if ($this->ctrl->getRequest()->isPost()) {
            $data = array_merge(
                $this->ctrl->params()->fromPost(),
                $this->ctrl->params()->fromFiles()
            );



            if (file_exists('public/img/favicon.ico'))
            {
                copy('public/img/favicon.ico', 'public/img/favicon.ico'.time());
            };

            $option = [
                'IsImage' => true,
                'extension' => '*',//or ['jpg', 'gif' ...]
                'filter' => [
                    'target' => 'public/img', //Thư mục
                    'use_upload_name' => true, //Sử dụng tên file gốc
                    'use_upload_extension' => true,//Sử dụng tên file gốc
                    'overwrite' => true,
                    'dbAdapter' => null,
                    'user' => $this->ctrl->identity(),
                    'extractcolor' => false,
                    'createthumb' => false,
                    'widththumb' => 50,
                    'valuesInsertDb' => [
                    ],
                    'fixfullname' =>  'favicon.ico',
                    'fixthumbname' => null,
                ]
            ];
            $datas = $uploader->process($option, $data);

            $respone->setContent(json_encode([
                'code' => 200,
                'message' => 'Upload Success',
                'numberfile' => 1,
                'list' => []
            ]));

            return $respone;
        }
        else
        {



        }



        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['uploader' => $uploader]);
        return $view;
    }

    function listeventname()
    {
        if (!$this->ctrl->isGranted(AdminController::RBAC_ADMIN_INDEX_PAGE))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), AdminController::RBAC_ADMIN_INDEX_PAGE);
        return $this->createView(__DIR__, __CLASS__, __FUNCTION__);

    }

    function memcached()
    {
        $cache = new \Memcached;
        $cache->addServer('124.158.4.73', '11211');
        $keys = $cache->getAllKeys();
        $data = [];
        foreach($keys as $key)
        {
            $rkey = explode('.', $key);
            if (!isset($data[$rkey[0]]))
                $data[$rkey[0]] = [];
            $data[$rkey[0]][] = $key;
        }

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['data' => $data,'viewkey' => $this->ctrl->params()->fromQuery('viewkey', null)]);
        return $view;
    }

    function opcache()
    {
 
        $options = [
            'allow_filelist'   => true,          // show/hide the files tab
            'allow_invalidate' => true,          // give a link to invalidate files
            'allow_reset'      => true,          // give option to reset the whole cache
            'allow_realtime'   => true,          // give option to enable/disable real-time updates
            'refresh_time'     => 5,             // how often the data will refresh, in seconds
            'size_precision'   => 2,             // Digits after decimal point
            'size_space'       => false,         // have '1MB' or '1 MB' when showing sizes
            'charts'           => true,          // show gauge chart or just big numbers
            'debounce_rate'    => 250,           // milliseconds after key press to send keyup event when filtering
            'cookie_name'      => 'opcachegui',  // name of cookie
            'cookie_ttl'       => 1              // days to store cookie
        ];

        if (!extension_loaded('Zend OPcache')) {
            die('The Zend OPcache extension does not appear to be installed');
        }
        $ocEnabled = ini_get('opcache.enable');
        if (empty($ocEnabled)) {
            die('The Zend OPcache extension is installed but not turned on');
        }

        $opcache = OpCacheService::init($options);
        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['opcache' => $opcache]);

        Common::addJsfile('https://cdn.jsdelivr.net/react/15.4.2/react.min.js');
        Common::addJsfile('https://cdn.jsdelivr.net/react/15.4.2/react-dom.min.js');
        Common::addJsfile('https://cdn.jsdelivr.net/jquery/3.1.1/jquery.min.js');

        return $view;

    }

    function memcacheclear()
    {
        $cache = new \Memcached;
        $cache->addServer('124.158.4.73', '11211');
        $cache->flush();
        $keys = $cache->getAllKeys();
        $cache->deleteMulti($keys);

        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'basiccommand', 'act' => 'memcached']);


    }

    function help() {
        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        return $view;
    }



 







    function log($i) {

        $request = Common::getRequest();
        if ($request->isGet()) {
            if ($this->ctrl->params()->fromQuery('action') == 'clear') {
                return $this->ctrl->askBeforeDone('Are you sure to delete?',
                    $this->url('basiccommand','log'), ['deletelog' => 'deletelog']);
            }
        }
        else if ($request->isPost()) {
            if ($this->ctrl->isConfirm(['deletelog' => 'deletelog'])) {
                $this->dbAdapter->execute("TRUNCATE log");
                return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'basiccommand']);
            }
        }
        $logs = $this->dbAdapter->get_rows_select(new Select('log'), null, ['log_id'=>'DESC'], null, new Log());

        $logs = iterator_to_array($logs);

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['logs' => $logs]);
        return $view;


    }
}