<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared\nightfire\_formDelegates\types;

use df\arch;

class PageDelete extends arch\node\form\NightfireTypeDelegate
{
    public function apply(): mixed
    {
        $this->data->nightfire->page->delete()
            ->where('node', '=', $this->_node)
            ->execute();

        return null;
    }
}
