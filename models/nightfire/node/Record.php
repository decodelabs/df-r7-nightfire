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

class Record extends opal\record\Base implements fire\type\INode {

    public function getId() {
        return $this['id'];
    }

    public function getSlug() {
        return $this['slug'];
    }

    public function getDate() {
        return $this['creationDate'];
    }

    public function getOwnerId() {
        return $this->getRawId('owner');
    }

    public function getOwner() {
        return $this['owner'];
    }

    public function getTitle() {
        return $this['title'];
    }

    public function getDescription() {
        return $this['description'];
    }

    public function getKeywords() {
        $keywords = $this['keywords'];

        if($keywords !== null) {
            return core\string\Util::parseDelimited($keywords);
        }

        return null;
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
}