<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\nodes\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\fire;

class HttpVersions extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Versions.html');
        $this->controller->fetchNode($view);
        $type = $view['node']->getType();

        if(!$type instanceof fire\type\IVersionedType) {
            $this->throwError(403, 'Type not versioned');
        }

        $view['versionList'] = $type->getVersionList($view['node']);

        return $view;
    }
}