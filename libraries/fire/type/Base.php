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
    
abstract class Base implements IType {

    public static function loadAll() {
        $output = array();
        
        foreach(df\Launchpad::$loader->lookupFileList('fire/type', ['php']) as $baseName => $path) {
            $name = substr($baseName, 0, -4);
            
            if($name === 'Base' || $name === '_manifest') {
                continue;
            }
            
            try {
                $type = self::factory($name);
            } catch(\Exception $e) {
                continue;
            }
            
            $output[$type->getName()] = $type;
        }
        
        ksort($output);
        return $output;
    }


    public static function factory($name) {
        $class = 'df\\fire\\type\\'.ucfirst($name);

        if(!class_exists($class)) {
            throw new RuntimeException(
                'Nightfire node type '.$name.' could not be found'
            );
        }

        return new $class();
    }

    public function getName() {
        $parts = explode('\\', get_class($this));
        return array_pop($parts);
    }

    public function getDisplayName() {
        return core\string\Manipulator::formatName($this->getName());
    }


    public function loadAddFormDelegate(arch\form\IAction $form, $delegateId, INode $node) {
        $form->loadDelegate($delegateId, 'types/'.$this->getName().'Add', '~/nightfire/')
            ->setNode($node);
        return $this;
    }

    public function loadEditFormDelegate(arch\form\IAction $form, $delegateId, INode $node) {
        $form->loadDelegate($delegateId, 'types/'.$this->getName().'Edit', '~/nightfire/')
            ->setNode($node);
        return $this;
    }

    public function loadDeleteFormDelegate(arch\form\IAction $form, $delegateId, INode $node) {
        try {
            $form->loadDelegate($delegateId, 'types/'.$this->getName().'Delete', '~/nightfire/')
                ->setNode($node);
        } catch(arch\form\DelegateException $e) {}

        return $this;
    }



    public function getCurrentVersionId(INode $node) {
        return $node['typeId'];
    }
}