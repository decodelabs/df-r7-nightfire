<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\migrate\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;

class TaskConvertNodes extends arch\task\Action {
    
    protected $_connection;
    protected $_nodeMap = [];
    protected $_pageMap = [];

    public function execute() {
        $this->_connection = $this->data->nightfire->node->getUnitAdapter()->getConnection();

        $newNodeTable = $this->_buildNewTable('node');
        $newPageTable = $this->_buildNewTable('page');

        $this->_mapNodes($newNodeTable);
        $this->_mapPages($newPageTable);
        $this->_mapTypes($newNodeTable);

        $this->_swapTables();
        $this->_clearSchemaCache();
    }

    protected function _buildNewTable($unitName) {
        $this->io->writeLine('Building new '.$unitName.' table');
        $unit = $this->data->nightfire->{$unitName};
        $schema = $unit->buildInitialSchema();
        $unit->updateUnitSchema($schema);
        $unit->validateUnitSchema($schema);

        $bridge = new axis\schema\bridge\Rdbms($unit, $this->_connection, $schema);
        $dbSchema = $bridge->createFreshTargetSchema();
        $targetName = $dbSchema->getName();
        $dbSchema->setName($targetName.'__new__');

        $newConnection = clone $this->_connection;
        return $newConnection->createTable($dbSchema, true);
    }

    protected function _mapNodes($nodeTable) {
        $this->io->writeLine('Mapping nodes');
        $oldNodeTable = $this->_connection->getTable('nightfire_node');

        foreach($oldNodeTable->select() as $row) {
            $row['id'] = $this->_nodeMap[$row['id']] = core\string\Uuid::comb();
            $nodeTable->insert($row)->execute();
        }
    }

    protected function _mapPages($pageTable) {
        $this->io->writeLine('Mapping pages');
        $oldPageTable = $this->_connection->getTable('nightfire_page');

        foreach($oldPageTable->select() as $row) {
            $row['id'] = $this->_pageMap[$row['id']] = core\string\Uuid::comb();

            if(isset($this->_nodeMap[$row['node_id']])) {
                $row['node_id'] = $this->_nodeMap[$row['node_id']];
            } else {
                $row['node_id'] = null;
            }

            $pageTable->insert($row)->execute();
        }
    }

    protected function _mapTypes($nodeTable) {
        $this->io->writeLine('Mapping types');
        $ids = $this->_connection->getTable('nightfire_node')->select('id', 'typeId')
            ->toList('id', 'typeId');

        foreach($ids as $nodeId => $typeId) {
            $newId = $this->_nodeMap[$nodeId];
            $newTypeId = $this->_pageMap[$typeId];

            $nodeTable->update(['typeId' => $newTypeId])
                ->where('id', '=', $newId)
                ->execute();
        }
    }

    protected function _swapTables() {
        $this->io->writeLine('Swapping tables');

        $swapTables = [
            'nightfire_page', 'nightfire_node'
        ];

        foreach($swapTables as $name) {
            $this->_connection->getTable($name)->drop();
            $table = $this->_connection->getTable($name.'__new__');

            if(!$table->exists()) {
                continue;
            }

            $table->rename($name);
        }
    }

    protected function _clearSchemaCache() {
        $this->io->writeLine('Updating schema cache');
        
        $clearTables = [
            'nightfire_page', 'nightfire_node'
        ];

        $this->_connection->getTable('axis_schemas')->delete()
            ->where('storeName', 'in', $clearTables)
            ->execute();

        axis\schema\Cache::getInstance()->clearAll();
    }
}