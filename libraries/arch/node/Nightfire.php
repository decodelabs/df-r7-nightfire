<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\arch\node;

abstract class Nightfire extends Base
{
    public function dispatch()
    {
        $record = $this->data->nightfire->node->load($this->context->request);

        if ($record !== null) {
            $this
                ->shouldCheckAccess(true)
                ->setDefaultAccess($record->getNodeDefaultAccess())
                ->setAccessSignifiers(...$record->getAccessSignifiers())
                ->setCallback(function ($node) use ($record) {
                    return $record->createResponse($this->context);
                });
        }

        return parent::dispatch();
    }
}
