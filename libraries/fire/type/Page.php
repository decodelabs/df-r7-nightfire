<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\fire\type;

use df;
use df\core;
use df\fire;
use df\arch;
use df\axis;
use df\aura;
    
class Page extends Base implements fire\type\IVersionedType {

    public function createResponse(arch\IContext $context, INode $node, $versionId=null) {
        $page = $this->_getUnit()->fetch()
            ->where('node', '=', $node)
            ->where('id', '=', $node['typeId'])
            ->toRow();

        $content = fire\layout\Content::fromXmlString($page['body']);
        $view = $context->aura->getBarebonesView('Html');
        $view->setContentProvider(new aura\view\content\NightfireLayoutContentProvider($context, $content));
        $view->setTitle($page['title']);
        $view->setKeywords($page['keywords']);
        $view->setMeta('description', $page['description']);
        
        return $view;
    }


// Versions
    public function countVersions(INode $node) {
        return $this->_getUnit()->select()
            ->where('node', '=', $node->getId())
            ->count();
    }

    public function isValidVersionId($id) {
        return (bool)$this->_getUnit()->select()
            ->where('id', '=', $id)
            ->count();
    }

    public function getVersionList(INode $node) {
        core\stub();
    }

    public function getVersionInfo(INode $node) {
        core\stub();
    }

    public function getLatestVersionId(INode $node) {
        return $this->_getUnit()->select('id')
            ->where('node', '=', $node->getId())
            ->orderBy('date DESC')
            ->toValue('id');
    }

    public function getVersionNumber(INode $node, $versionId=null) {
        $unit = $this->_getUnit();

        if(!$versionId) {
            $versionId = $node['typeId'];
        }

        return $unit->select()
            ->where('node', '=', $node->getId())
            ->whereCorrelation('page.date', '<', 'current.date')
                ->from($unit, 'current')
                ->where('id', '=', $versionId)
                ->endCorrelation()
            ->count() + 1;
    }

    public function applyVersion(INode $node, $versionId, $deleteUnused=false, $keepCurrent=true) {
        core\stub();
    }

    public function deleteVersion(INode $node, $versionId) {
        core\stub();
    }


    protected function _getUnit() {
        return axis\Model::factory('nightfire')->getUnit('page');
    }
}