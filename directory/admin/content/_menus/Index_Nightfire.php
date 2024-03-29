<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\_menus;

use df\arch;

class Index_Nightfire extends arch\navigation\menu\Base
{
    protected function createEntries($entryList)
    {
        $entryList->addEntries(
            $entryList->newLink('./nodes/', 'Nodes')
                ->setId('nodes')
                ->setDescription('Create full content pages, redirects and more, update versions and control access')
                ->setIcon('node')
                ->setWeight(10)
        );
    }
}
