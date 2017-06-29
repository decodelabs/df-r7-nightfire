<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\aura\view\content;

use df;
use df\core;
use df\aura;
use df\arch;
use df\fire;

class NightfireLayoutContentProvider implements aura\view\IContentProvider {

    use core\TStringProvider;
    use core\TContextAware;
    use aura\view\TView_DeferredRenderable;

    protected $_content;

    public function __construct(arch\IContext $context, fire\ILayoutContent $content) {
        $this->context = $context;
        $this->_content = $content;
    }

    public function getView() {
        return $this->getRenderTarget()->getView();
    }

    public function toResponse() {
        return $this->getView();
    }

    public function render() {
        $view = $this->getView();

        $config = fire\Config::getInstance();
        $layout = $config->getLayoutDefinition($this->_content->getId());

        $view->setLayout($layout->getId());
        $view->bodyTag->setDataAttribute('layout', $layout->getId());
        $layoutSlots = $layout->getSlots();
        $output = '';

        foreach($this->_content->getSlots() as $slot) {
            if($slot->isPrimary()) {
                unset($layoutSlots['primary']);
                $output = $slot->renderTo($view);
            } else {
                unset($layoutSlots[$slot->getId()]);
                $view->setSlot($slot->getId(), $slot);
            }
        }

        foreach($layoutSlots as $key => $slot) {
            $view->setSlot($key, null);
        }

        return $output;
    }

    public function toString(): string {
        return (string)$this->render();
    }
}
