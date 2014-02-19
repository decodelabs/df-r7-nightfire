<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\nightfire\elements\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
    
class ElementSelector extends arch\form\template\SearchSelectorDelegate {

    protected function _fetchResultList(array $ids) {
        return $this->data->nightfire->element->fetch()
            ->where('slug', 'in', $ids)
            ->chain([$this, 'applyDependencies'])
            ->orderBy('name');
    }

    protected function _getSearchResultIdList($search, array $selected) {
        return $this->data->nightfire->element->select('slug')
            ->beginWhereClause()
                ->where('name', 'matches', $search)
                ->orWhere('slug', 'matches', $search)
                ->endClause()
            ->where('slug', '!in', $selected)
            ->chain([$this, 'applyDependencies'])
            ->toList('slug');
    }

    protected function _getResultId($result) {
        return $result['slug'];
    }

    protected function _renderCollectionList($result) {
        return $this->import->component('ElementList', '~admin/nightfire/elements/', [
                'actions' => false
            ])
            ->setCollection($result);
    }
}