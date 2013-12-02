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
}

interface IType {
    public function getName();
    public function getDisplayName();

    public function createResponse(arch\IContext $context, INode $node, $versionId=null);

    public function loadAddFormDelegate(arch\form\IAction $form, $delegateId, INode $node);
    public function loadEditFormDelegate(arch\form\IAction $form, $delegateId, INode $node);
    public function loadDeleteFormDelegate(arch\form\IAction $form, $delegateId, INode $node);
}

interface IVersionedType {
    public function countVersions(INode $node);
    public function isValidVersionId($id);
    public function getVersionList(INode $node);
    public function getVersionInfo(INode $node);
    public function getCurrentVersionId(INode $node);
    public function getLatestVersionId(INode $node);
    public function getVersionNumber(INode $node, $versionId=null);
    public function applyVersion(INode $node, $versionId, $deleteUnused=false, $keepCurrent=true);
    public function deleteVersion(INode $node, $versionId);
}

interface IVersion {
    public function getId();
    public function getDate();
    public function getOwner();
    public function getTitle();
    public function isLive();
}


interface IFormDelegate extends arch\form\ISelfContainedRenderableDelegate {
    public function validate();
    public function apply();
}