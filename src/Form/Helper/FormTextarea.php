<?
namespace XT\Core\Form\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;

class FormTextarea extends AbstractHelper
{
    protected $script = 'form/element/textarea-autogrow.phtml';
    public function __invoke(ElementInterface $element)
    {
        $name   = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }
        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $content            = (string) $element->getValue();
        $escapeHtml         = $this->getEscapeHtmlHelper();

        $content = $escapeHtml($content);
        $attributes = $this->createAttributesString($attributes);


        return $this->getView()->render($this->script, [
            'element' => $element, 'content'=>$content, 'attributes'=>$attributes
        ]);
    }
}