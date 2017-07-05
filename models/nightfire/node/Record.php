<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\nightfire\node;

use df;
use df\core;
use df\apex;
use df\axis;
use df\fire;
use df\opal;
use df\arch;
use df\flex;

class Record extends opal\record\Base implements fire\type\INode {

    const BROADCAST_HOOK_EVENTS = true;

    protected $_typeHistory = [];

    public function setTypeHistory(array $history) {
        $this->_typeHistory = $history;
        return $this;
    }

    protected function onPreSave($queue, $job) {
        $this->_writeHistory($queue, $job);
    }

    protected function _writeHistory($queue, $job) {
        $isNew = $this->isNew();

        if(!$isNew && !$this->hasChanged()) {
            return $this;
        }

        if($isNew) {
            $description = 'Created node: '.$this['title'];
        } else {
            $lines = $this->_typeHistory;

            foreach($this->getChangedValues() as $field => $value) {
                switch($field) {
                    case 'slug':
                        $lines[] = 'Moved to '.$value;
                        break;

                    case 'title':
                        $lines[] = 'Updated title to "'.$value.'"';
                        break;

                    case 'type':
                        $lines[] = 'Changed type to '.$value;
                        break;

                    case 'owner':
                        if(is_object($value)) {
                            $lines[] = 'Set owner to '.$value['fullName'];
                        } else if(!empty($value)) {
                            $lines[] = 'Set new owner';
                        } else {
                            $lines[] = 'Removed owner';
                        }

                        break;

                    case 'defaultAccess':
                        $lines[] = 'Set default access to '.$value;
                        break;

                    case 'notes':
                        $lines[] = 'Updated notes';
                        break;

                    case 'isLive':
                        $lines[] = 'Set node '.($value ? 'active' : 'inactive');
                        break;
                }
            }

            if(empty($lines)) {
                return $this;
            }

            $description = implode("\n", $lines);
        }

        $this->getAdapter()->context->data->content->history->createRecordEntry(
            $this, $queue, $job, $description
        );
    }

    public function getId(): ?string {
        return $this['id'];
    }

    public function getSlug() {
        return $this['slug'];
    }

    public function getDate() {
        return $this['creationDate'];
    }

    public function getOwnerId() {
        return $this['#owner'];
    }

    public function getOwner() {
        return $this['owner'];
    }

    public function getTitle(): ?string {
        return $this['title'];
    }

    public function getDescription() {
        return $this['description'];
    }

    public function isMappable() {
        return $this['isMappable'];
    }


    public function getTypeName() {
        return $this['type'];
    }

    public function getType() {
        return fire\type\Base::factory($this['type']);
    }

    public function getTypeId() {
        return $this['typeId'];
    }

    public function getTypeData() {
        return $this['typeData'];
    }


    public function getNodeDefaultAccess() {
        switch($this['defaultAccess']) {
            case 'all': return arch\IAccess::ALL;
            case 'none': return arch\IAccess::NONE;

            case 'deactivated': return arch\IAccess::DEACTIVATED;
            case 'guest': return arch\IAccess::GUEST;
            case 'pending': return arch\IAccess::PENDING;
            case 'bound': return arch\IAccess::BOUND;
            case 'confirmed': return arch\IAccess::CONFIRMED;

            case 'dev': return arch\IAccess::DEV;
        }
    }

    public function getNodeAccessSignifiers() {
        if($sigs = $this['accessSignifiers']) {
            return $sigs->toArray();
        } else {
            return [];
        }
    }

    public function createResponse(arch\IContext $context) {
        return $this->getType()->createResponse($context, $this);
    }
}
