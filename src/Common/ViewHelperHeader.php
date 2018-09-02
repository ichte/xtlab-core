<?php
namespace XT\Core\Common;

use Zend\View\Helper\HeadLink;
use Zend\View\Helper\HeadMeta;
use Zend\View\Helper\HeadScript;
use Zend\View\Helper\HeadStyle;
use Zend\View\Helper\InlineScript;
use Zend\View\HelperPluginManager;

trait ViewHelperHeader
{
    /**
     * @return HelperPluginManager
     */
    public static function getViewHelper()
    {
        return self::$sm->get('ViewHelperManager');
    }

    public static function setCharsetUtf8() {
        /***
         * @var $headmeta HeadMeta
         */
        $headmeta = self::getViewHelper()->get(HeadMeta::class);
        $headmeta->setCharset('utf-8');
    }

    /***
     * @param string $name
     * @param string $value
     * @return HeadMeta
     */
    public static function headMeta($name, $value) {
        /***
         * @var $headmeta HeadMeta
         */
        $headmeta = self::getViewHelper()->get(HeadMeta::class);
        $headmeta->appendName($name, $value);
        return $headmeta;
    }

    public static function headMetaNoIndex() {
        self::headMeta('robots', 'noindex');
    }

    /***
     * @param string $content
     * @param bool $append
     */
    public static function addCssInline($content, $append = true) {

        /**
         * @var $headStyle HeadStyle;
         */
        $headStyle = Common::getViewHelper()->get(HeadStyle::class);
        if ($append)
            $headStyle->appendStyle($content);
        else
            $headStyle->prependStyle($content);

    }

    /***
     * @param string|array $files
     * @param bool $append
     * @return HeadLink
     */
    public static function addCssFiles($files, $append = true) {
        /***
         * @var $headlink HeadLink
         */
        $headlink = self::getViewHelper()->get(HeadLink::class);

        if (is_array($files)) {
            foreach ($files as $file)
            {
                if ($append)
                    $headlink->appendStylesheet($file);
                else
                    $headlink->prependStylesheet($file);
            }
        }
        else
        {
            if ($append)
                $headlink->appendStylesheet($files);
            else
                $headlink->prependStylesheet($files);
        }
        return $headlink;

    }

    /***
     * @param string|array $files
     * @param bool $append
     */
    public static function addJsFiles($files, $append = true) {
        /***
         * @var $headscript HeadScript
         */
        $headscript = self::getViewHelper()->get(HeadScript::class);

        if (is_array($files)) {
            foreach ($files as $file)
            {
                if ($append)
                    $headscript->appendFile($file);
                else
                    $headscript->prependFile($file);
            }
        }
        else
        {
            if ($append)
                $headscript->appendFile($files);
            else
                $headscript->prependFile($files);
        }
    }


    /***
     * @param string $content
     * @param bool $append
     */
    public static function addJsInline($content, $append = true) {

        /***
         * @var $InlineScript InlineScript
         */
        $InlineScript = self::getViewHelper()->get(InlineScript::class);
        if ($append)
            $InlineScript->appendScript($content);
        else
            $InlineScript->prependScript($content);

    }

    public static function defaultHeader() {
        self::addCssFiles('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
        self::addCssFiles('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
        self::addJsFiles('https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js');
        self::addJsFiles('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js');
        self::addJsFiles('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js');
        self::addJsFiles('https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.2/jquery.ui.touch-punch.min.js');
        self::addJsFiles('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');
        self::addCssFiles('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');


        self::headMeta('viewport','width=device-width, initial-scale=1.0')
            ->appendHttpEquiv('X-UA-Compatible', 'IE=edge')
            ->appendHttpEquiv('Content-Language', 'vi')
            ->appendProperty('fb:app_id', '1565807193699425');
    }

}