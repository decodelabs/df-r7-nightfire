<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;
use df\fire;

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const TITLE = 'Nodes';
    const ICON = 'node';
    const ADAPTER = 'axis://nightfire/Node';
    const NAME_FIELD = 'slug';

    protected $_sections = [
        'details',
        'versions' => 'list',
        'history' => 'history'
    ];

    protected $_recordListFields = [
        'slug', 'title', 'type', 'owner', 'creationDate',
        'lastEditDate', 'currentVersion', 'isLive'
    ];

    protected $_recordDetailsFields = [
        'title', 'slug', 'type', 'owner', 'isLive',
        'creationDate', 'lastEditDate', 'currentVersion'
    ];

// Record data
    protected function prepareRecordList($query, $mode) {
        $query->importRelationBlock('owner', 'link');
    }

    protected function countSectionItems($record) {
        return [
            'versions' => $record['versionCount'],
            'history' => $this->data->content->history->countFor($record)
        ];
    }


// Components
    public function getRecordOperativeLinks($node, $mode) {
        return [
            // Preview
            $this->apex->component('NodeLink', $node, $this->_('Preview'))
                ->setNode('preview')
                ->setIcon('preview')
                ->render()
                ->setAttribute('target', '_blank'),

            parent::getRecordOperativeLinks($node, $mode)
        ];
    }


// Sections
    public function renderDetailsSectionBody($node) {
        return [
            parent::renderDetailsSectionBody($node),

            $this->html('h3', $this->_('Preview')),
            $node->getType()->renderPreview($this->view, $node)
        ];
    }

    public function renderVersionsSectionBody($node) {
        $type = $node->getType();

        if(!$isVersioned = $type instanceof fire\type\IVersionedType) {
            return $this->html->flashMessage($this->_(
                'This node\'s type does not support versioning'
            ), 'error');
        }

        $versionList = $type->getVersionList($node);

        return $this->apex->component('VersionList')
            ->setNode($node)
            ->setCollection($versionList);
    }

    public function renderHistorySectionBody($task) {
        $historyList = $this->data->content->history->fetchFor($task)
            ->paginateWith($this->request->query);

        return $this->apex->component('~admin/content/history/HistoryList')
            ->setCollection($historyList);
    }


// Fields
    public function defineCurrentVersionField($list, $mode) {
        $list->addField('currentVersion', $this->_('Version'), function($node) {
            if(!$node['versionCount']) {
                return;
            }

            return $this->_('%v% of %c%', [
                '%v%' => $node['currentVersion'],
                '%c%' => $node['versionCount']
            ]);
        });
    }
}