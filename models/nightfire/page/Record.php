<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\nightfire\page;

use df;
use df\core;
use df\apex;
use df\axis;
use df\fire;
use df\opal;

class Record extends opal\record\Base implements fire\type\IVersion {

    const BROADCAST_HOOK_EVENTS = true;

    public function getId(): ?string {
        return $this['id'];
    }

    public function getDate() {
        return $this['date'];
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

    public function isActive(fire\type\INode $node) {
        return $this['#node'] == $node->getId()
            && $this['id'] == $node->getTypeId();
    }

    public function getHistory() {
        $output = [];

        if(!$this->isNew()) {
            foreach($this->getChangedValues() as $field => $value) {
                switch($field) {
                    case 'description':
                        $output[] = 'Updated description';
                        break;

                    case 'body':
                        $output[] = 'Updated body content';
                        break;
                }
            }
        }

        return $output;
    }
}
