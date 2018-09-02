<?

namespace XT\Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;

class askBeforeDone extends AbstractPlugin
{
    /**
     * @param $title
     * @param $urlpost
     * @param array $elementform
     * @return ViewModel
     */
    public function __invoke($title, $urlpost, $elementform = [])
    {
        $view = new ViewModel([
            'urlpost' => $urlpost,
            'elementform' => $elementform,
            'ask' => $title]);
        $view->setTemplate('toolbox/askbeforedone.phtml');
        return $view;
    }
}