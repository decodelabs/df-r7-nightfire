<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\fire\type;

use df\arch;
use df\aura;

use DecodeLabs\R7\Legacy;

class Redirect extends Base
{
    public function createResponse(arch\IContext $context, INode $node, $versionId=null)
    {
        return Legacy::$http->redirect($context->uri->__invoke($node['typeData']));
    }

    public function renderPreview(aura\view\IView $view, INode $node, $version=null)
    {
        return $view->html->link($view->uri->__invoke($node['typeData']))
            ->setIcon('link')
            ->setTarget('_blank');
    }
}
