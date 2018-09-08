<?php
namespace XT\Core\Helper;


use XT\Core\Common\Common;

class TemplateFiles
{

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
}