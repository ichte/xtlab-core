<?php
namespace XT\Core\Helper;


use function foo\func;
use XT\Core\Common\Common;
use XT\Core\Event\GlobalListener\GlobalListener;
use Zend\Code\Generator\DocBlockGenerator;

class TemplateFiles
{

    public static function getGlobalListener() {
        $files = [];
        $dir = Common::$sm->get('config')['globallistenersys'];
        self::getfile($dir, $files, '');
        $classListener = [];
        foreach ($files as $file) {
            $pi = pathinfo($file);
            $nameclass = $pi['filename'];
            $classname = 'XT\Core\Event\Listener\GlobalListener\\'.$nameclass;

            $class = \Zend\Code\Generator\ClassGenerator::fromReflection(
                new \Zend\Code\Reflection\ClassReflection($classname)
            );

            $globalistener = new GlobalListener();
            $globalistener->setEvent('');
            $globalistener->setActive(false);
            $globalistener->setId(0);
            $globalistener->setPriority(1);
            $globalistener->setClassname($class->getName());
            $globalistener->setDescription($class->getDocBlock()->getLongDescription());
            $globalistener->setCode($class->getMethod('execute')->getBody());

            $classListener[$globalistener->getClassname()] = $globalistener;




        }
        return $classListener;
    }


    public static function getfile($dir, &$filesar, $dirrelate) {

        foreach (new \DirectoryIterator($dir) as $fileInfo) {

            if($fileInfo->isDot()) continue;

            if ($fileInfo->isDir()) {

                $newdirrelate = (($dirrelate != '') ? ($dirrelate .'/') : '').$fileInfo->getBasename();
                self::getfile($fileInfo->getPathname(), $filesar, $newdirrelate);

            }

            else {
                $entry = (($dirrelate != '') ? ($dirrelate .'/') : '').$fileInfo->getFilename();
                $filesar[$entry] = $entry;
            }
        }
    }

    public static function listfiles() {

        $fileApps = [];
        $fileSys = [];
        $dir = realpath(Common::$cf->CF->common->pathtemplatedefault);
        self::getfile($dir, $fileApps, '');

        $systemtemplatedir = realpath(__DIR__.'/../template');
        self::getfile($systemtemplatedir, $fileSys, '');



        $merg = [];
        foreach ($fileSys as $file) {
            if (isset($fileApps[$file])) {
                unset($fileApps[$file]);

                $merg[] = [
                    'label' => $file.' (overrided)',
                    'value' => $file,
                    'attributes' => ['class' => 'text-danger']

                ];



            }
            else
            {
                $merg[] = [
                    'label' => $file. ' (system)',
                    'value' => $file,
                    'attributes' => ['class' => 'text-primary']

                ];
            }
        }

        foreach ($fileApps as $file) {
            $merg[] = [
                'label' => $file,
                'value' => $file,
                'attributes' => ['class' => 'text-success']
            ];
        }


        return $merg;



    }

    public static function listfilesEdit() {

        $fileApps = [];
        $fileSys = [];
        $dir = realpath(Common::$cf->CF->common->pathtemplatedefault);
        self::getfile($dir, $fileApps, '');

        $systemtemplatedir = Common::$sm->get('config')['templatepathsys'];

        self::getfile($systemtemplatedir, $fileSys, '');

        $addto = function ($file, $type,  &$fs) {
            $f = explode('/', $file);

            if (count($f)>1) {
                $folder = $f[0];
                if (!isset($fs[$folder]))
                    $fs[$folder] = [];

                unset($f[0]);
                $f = implode('/', $f);
            }
            else {
                $folder = '';
                $f = $file;
            }


            $fs[$folder][] = [
                'short'=> $f,
                'name' => $file,
                'type' => $type
            ];



        };

        $merg = [];
        foreach ($fileSys as $file) {
            if (isset($fileApps[$file])) {
                unset($fileApps[$file]);

                $addto($file, 'overried', $merg);

            }
            else
            {
                $addto($file, 'system', $merg);
            }
        }

        foreach ($fileApps as $file) {
            $addto($file, 'app', $merg);
        }

        return $merg;



    }

}