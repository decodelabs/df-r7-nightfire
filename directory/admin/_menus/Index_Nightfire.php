<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_Nightfire extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~admin/nightfire/', 'Pages & elements')
                ->setId('nightfire')
                ->setDescription('Build pages and reusable content to display on the front end of your site')
                ->setIcon('content')
                ->setWeight(7)
        );
    }
}