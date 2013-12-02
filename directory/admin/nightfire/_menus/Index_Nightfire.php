<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\nightfire\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_Nightfire extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~admin/nightfire/nodes/', 'Nodes')
                ->setId('nodes')
                ->setDescription('Create full content pages, redirects and more, update versions and control access')
                ->setIcon('node')
                ->setWeight(10),

            $entryList->newLink('~admin/nightfire/elements/', 'Elements')
                ->setId('elements')
                ->setDescription('Put together reusable blocks of content to use in your pages')
                ->setIcon('element')
                ->setWeight(20)
        );
    }
}