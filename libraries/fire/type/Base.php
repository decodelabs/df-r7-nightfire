<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\fire\type;

use DecodeLabs\Dictum;
use DecodeLabs\Exceptional;

use DecodeLabs\R7\Legacy;
use df\arch;
use df\aura;

abstract class Base implements IType
{
    public static function loadAll()
    {
        $output = [];

        foreach (Legacy::getLoader()->lookupClassList('fire/type') as $name => $class) {
            try {
                $type = self::factory($name);
            } catch (\Throwable $e) {
                continue;
            }

            $output[$type->getName()] = $type;
        }

        ksort($output);
        return $output;
    }


    public static function factory($name)
    {
        $class = 'df\\fire\\type\\' . ucfirst($name);

        if (!class_exists($class)) {
            throw Exceptional::Runtime(
                'Nightfire node type ' . $name . ' could not be found'
            );
        }

        return new $class();
    }

    public function getName(): string
    {
        $parts = explode('\\', get_class($this));
        return (string)array_pop($parts);
    }

    public function getDisplayName(): string
    {
        return Dictum::name($this->getName());
    }

    public function renderPreview(aura\view\IView $view, INode $node, $versionId = null)
    {
    }

    public function loadAddFormDelegate(arch\node\IFormNode $form, $delegateId, INode $node)
    {
        /**
         * Delegate
         * @var  arch\node\form\NightfireTypeDelegate $delegate
         */
        $delegate = $form->loadDelegate($delegateId, '~/nightfire/#/types/' . $this->getName() . 'Add');
        $delegate
            ->setNode($node);

        return $this;
    }

    public function loadEditFormDelegate(arch\node\IFormNode $form, $delegateId, INode $node, $versionId = null, $makeNew = false)
    {
        $specific = true;

        if ($versionId === null) {
            $versionId = $node->getTypeId();
            $specific = false;
        }

        /**
         * Delegate
         * @var  arch\node\form\NightfireTypeDelegate $delegate
         */
        $delegate = $form->loadDelegate($delegateId, '~/nightfire/#/types/' . $this->getName() . 'Edit');
        $delegate
            ->setNode($node)
            ->setVersionId($versionId)
            ->shouldMakeNew($makeNew)
            ->isSpecificVersion($specific);

        return $this;
    }

    public function loadDeleteFormDelegate(arch\node\IFormNode $form, $delegateId, INode $node)
    {
        try {
            /**
             * Delegate
             * @var  arch\node\form\NightfireTypeDelegate $delegate
             */
            $delegate = $form->loadDelegate($delegateId, '~/nightfire/#/types/' . $this->getName() . 'Delete');
            $delegate
                ->setNode($node);
        } catch (arch\node\DelegateException $e) {
        }

        return $this;
    }



    public function getCurrentVersionId(INode $node)
    {
        return $node['typeId'];
    }
}
