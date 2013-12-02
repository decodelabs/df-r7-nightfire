<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\nightfire\element;

use df;
use df\core;
use df\apex;
use df\opal;
use df\axis;
    
class Record extends opal\record\Base {
    
    public function getSlotDefinition() {
        return $this->getRecordAdapter()
            ->getUnitSchema()
            ->getField('body')
            ->getSlotDefinition();
    }
}