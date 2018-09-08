<?
namespace XT\Core\Form\Helper;

use XT\Core\Form\Element\TextareaAutoGrow;
use XT\Core\Form\Element\TextAutoComplete;
use \Zend\Form\View\Helper\FormElement as BaseFormElement;
use \Zend\Form\ElementInterface;

class FormElement extends BaseFormElement
{
    public function render(ElementInterface $element)
    {


        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }
        //Add forcustome render element

        if ($element instanceof TextareaAutoGrow) {

            $helper = $renderer->plugin(FormTextarea::class);
            return $helper($element);
        }

        //End
        if ($element instanceof TextAutoComplete) {

            $helper = $renderer->plugin(FormTextAutoComplete::class);
            return $helper($element);
        }

        $renderedInstance = $this->renderInstance($element);

        if ($renderedInstance !== null) {
            return $renderedInstance;
        }

        $renderedType = $this->renderType($element);

        if ($renderedType !== null) {
            return $renderedType;
        }

        return $this->renderHelper($this->defaultHelper, $element);

    }
}