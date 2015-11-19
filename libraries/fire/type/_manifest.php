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
use df\aura;

// Exceptions
interface IException {}
class RuntimeException extends \RuntimeException implements IException {}
class InvalidArgumentException extends \InvalidArgumentException implements IException {}


// Interfaces
interface INode {
    public function getId();
    public function getSlug();
    public function getDate();
    public function getOwnerId();
    public function getOwner();
    public function getTitle();
    public function getDescription();
    public function getKeywords();
    public function isMappable();

    public function getTypeName();
    public function getType();
    public function getTypeId();
    public function getTypeData();
}

interface IType {
    public function getName();
    public function getDisplayName();

    public function createResponse(arch\IContext $context, INode $node, $versionId=null);
    public function renderPreview(aura\view\IView $view, INode $node, $version=null);

    public function loadAddFormDelegate(arch\action\IFormAction $form, $delegateId, INode $node);
    public function loadEditFormDelegate(arch\action\IFormAction $form, $delegateId, INode $node, $versionId=null);
    public function loadDeleteFormDelegate(arch\action\IFormAction $form, $delegateId, INode $node);
}

interface IVersionedType {
    public function countVersions(INode $node);
    public function isValidVersionId($id);
    public function getVersion(INode $node, $versionId=null);
    public function getVersionList(INode $node);
    public function getCurrentVersionId(INode $node);
    public function getLatestVersionId(INode $node);
    public function getVersionNumber(INode $node, $versionId=null);
    public function applyVersion(INode $node, $version, $deleteUnused=false, $keepCurrent=true);
    public function deleteVersion(INode $node, $version);
}

interface IVersion {
    public function getId();
    public function getDate();
    public function getOwnerId();
    public function getOwner();
    public function getTitle();
    public function isActive(INode $node);
}


interface IFormDelegate extends arch\action\ISelfContainedRenderableDelegate {
    public function setNode(INode $node);
    public function getNode();
    public function setVersionId($versionId);
    public function getVersionId();
    public function shouldMakeNew($flag=null);
    public function isSpecificVersion($flag=null);
    public function getDefaultNodeValues();
    public function validate();
    public function apply();
}