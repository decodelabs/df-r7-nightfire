<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\nodes;

use DecodeLabs\Tagged as Html;
use df\arch;

use df\fire;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Nodes';
    public const ICON = 'node';
    public const ADAPTER = 'axis://nightfire/Node';
    public const NAME_FIELD = 'slug';

    public const SECTIONS = [
        'details',
        'versions' => 'list',
        'history' => 'history'
    ];

    public const LIST_FIELDS = [
        'slug', 'title', 'type', 'owner', 'creationDate',
        'lastEditDate', 'currentVersion', 'isLive'
    ];

    public const DETAILS_FIELDS = [
        'title', 'slug', 'type', 'owner', 'isLive',
        'creationDate', 'lastEditDate',
        'defaultAccess', 'accessSignifiers',
        'currentVersion'
    ];

    public const CAN_PREVIEW = true;

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query->importRelationBlock('owner', 'link');
    }

    protected function countSectionItems($record): array
    {
        return [
            'versions' => $record['versionCount'],
            'history' => $this->data->content->history->countFor($record)
        ];
    }


    // Components
    protected function getRecordPreviewUriString(array $node): ?string
    {
        return (string)$this->getRecordUri($node, 'preview');
    }


    // Sections
    public function renderDetailsSectionBody($node)
    {
        return [
            parent::renderDetailsSectionBody($node),

            Html::{'h3'}($this->_('Preview')),
            $node->getType()->renderPreview($this->view, $node)
        ];
    }

    public function renderVersionsSectionBody($node)
    {
        $type = $node->getType();

        if (!$isVersioned = $type instanceof fire\type\IVersionedType) {
            return $this->html->flashMessage($this->_(
                'This node\'s type does not support versioning'
            ), 'error');
        }

        $versionList = $type->getVersionList($node);

        return $this->apex->component('VersionList')
            ->setNode($node)
            ->setCollection($versionList);
    }

    public function renderHistorySectionBody($job)
    {
        $historyList = $this->data->content->history->fetchFor($job)
            ->paginateWith($this->request->query);

        return $this->apex->component('~admin/content/history/HistoryList')
            ->setCollection($historyList);
    }


    // Fields
    public function defineCurrentVersionField($list, $mode)
    {
        $list->addField('currentVersion', $this->_('Version'), function ($node) {
            if (!$node['versionCount']) {
                return;
            }

            return $this->_('%v% of %c%', [
                '%v%' => $node['currentVersion'],
                '%c%' => $node['versionCount']
            ]);
        });
    }

    public function defineDefaultAccessField($list, $mode)
    {
        $list->addField('defaultAccess', function ($node) {
            return $this->data->nightfire->accessOptions->label($node['defaultAccess']);
        });
    }

    public function defineAccessSignifiersField($list, $mode)
    {
        $list->addField('accessSignifiers', function ($node) {
            if ($node['accessSignifiers']) {
                return implode(', ', $node['accessSignifiers']->toArray());
            }
        });
    }
}
