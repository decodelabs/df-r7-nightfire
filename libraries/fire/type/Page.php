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
        if($versionId === null) {
            $versionId = $node['typeId'];
        }

        $page = $this->_getUnit()->fetch()
            ->where('node', '=', $node)
            ->where('id', '=', $versionId)
            ->toRow();

        $content = fire\layout\Content::fromXmlString($page['body']);
        $view = $context->aura->getBarebonesView('Html');
        $view->setContentProvider(new aura\view\content\NightfireLayoutContentProvider($context, $content));
        $view->setTitle($page['title']);
        $view->setKeywords($page['keywords']);
        $view->setMeta('description', $page['description']);
        
        return $view;
    }

    public function renderPreview(aura\view\IView $view, INode $node, $page=null) {
        if(!$page instanceof fire\type\IVersion) {
            $page = $this->getVersion($node, $page);
        }

        if($page) {
            return $view->nightfire->renderLayoutPreview($page['body']);
        }
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

    public function getVersion(INode $node, $versionId=null) {
        if(!$versionId) {
            $versionId = $node['typeId'];
        }

        return $this->_getUnit()->fetch()
            ->where('node', '=', $node)
            ->where('id', '=', $versionId)
            ->toRow();
    }

    public function getVersionList(INode $node) {
        return $this->_getUnit()->fetch()
            ->populateSelect('owner', 'id', 'fullName')
            ->where('node', '=', $node)
            ->orderBy('date DESC')
            ->toArray();
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

    public function applyVersion(INode $node, $page, $deleteUnused=false, $keepCurrent=true) {
        if(!$page instanceof fire\type\IVersion) {
            $page = $this->getVersion($node, $page);
        }

        if(!$page) {
            return false;
        }

        if($deleteUnused) {
            $this->_getUnit()->delete()
                ->where('node', '=', $node)
                ->where('id', '!=', $page['id'])
                ->chainIf($keepCurrent, function($query) use($node) {
                    $query->where('id', '!=', $node['typeId']);
                })
                ->execute();
        }

        $node->title = $page['title'];
        $node->lastEditDate = 'now';
        $node->typeId = $page['id'];
        $node->save();

        return true;
    }

    public function deleteVersion(INode $node, $page) {
        if(!$page instanceof fire\type\IVersion) {
            $page = $this->getVersion($node, $page);
        }

        $page->delete();
        return $this;
    }


    protected function _getUnit() {
        return axis\Model::factory('nightfire')->getUnit('page');
    }
}