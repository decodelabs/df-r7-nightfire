<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\content\nodes\_nodes;

use df\arch\node\form\NightfireTypeDelegate;

class HttpEdit extends HttpAdd
{
    protected $_versionId;

    protected function init(): void
    {
        $this->_node = $this->scaffold->getRecord();
        $this->_versionId = $this->request['version'];
    }

    protected function getInstanceId(): ?string
    {
        return $this->_node['id'] . ':' . $this->_versionId;
    }

    protected function initWithSession(): void
    {
        if (!$this->hasStore('nodeType')) {
            $this->setStore('nodeType', $this->_node['type']);
        }

        parent::initWithSession();
    }

    protected function loadDelegates(): void
    {
        if ($this->_type) {
            $this->_type->loadEditFormDelegate($this, 'type', $this->_node, $this->_versionId, $this->_versionId ? true : false);
        }
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_node, [
            'title', 'slug', 'defaultAccess', 'notes', 'isLive', 'isMappable'
        ]);

        if ($this->_node['accessSignifiers']) {
            $this->values->accessSignifiers = implode(',', $this->_node['accessSignifiers']->toArray());
        }
    }

    protected function afterInit()
    {
        if ($this->isNew()) {
            $values = $this['type']->as(NightfireTypeDelegate::class)
                ->getDefaultNodeValues();

            if (is_array($values) && !empty($values)) {
                $this->values->import($values);
            }
        }
    }
}
