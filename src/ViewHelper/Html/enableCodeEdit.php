<?php 
namespace XT\Core\ViewHelper\Html;


use XT\Core\Common\Common;
use Zend\View\Helper\AbstractHelper;

class enableCodeEdit extends AbstractHelper
{
    public function __invoke($id_textarea) {
        Common::addCssFiles(["https://codemirror.net/lib/codemirror.css"]);
        Common::addJsFiles(
            [
            'https://codemirror.net/lib/codemirror.js',
            'https://codemirror.net/mode/javascript/javascript.js',
            'https://codemirror.net/mode/css/css.js',
            'https://codemirror.net/mode/htmlmixed/htmlmixed.js'
        ]);
        Common::addCssInline(".CodeMirror {height: 550px;}")
        ?>
        <script>
            var editor12 = CodeMirror.fromTextArea(<?=$id_textarea?>, {
                lineNumbers: true,

            });
        </script>
        <?php
    }
}