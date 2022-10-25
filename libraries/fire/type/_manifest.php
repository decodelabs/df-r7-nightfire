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
use df\opal;

interface INode extends opal\record\IRecord
{
    public function getId(): ?string;
    public function getSlug();
    public function getDate();
    public function getOwnerId();
    public function getOwner();
    public function getTitle(): ?string;
    public function getDescription();

    public function getNodeDefaultAccess();
    public function getNodeAccessSignifiers();

    public function isMappable();

    public function getTypeName();
    public function getType();
    public function getTypeId();
    public function getTypeData();
}

interface IType
{
    public function getName(): string;
    public function getDisplayName(): string;

    public function createResponse(arch\IContext $context, INode $node, $versionId=null);
    public function renderPreview(aura\view\IView $view, INode $node, $version=null);

    public function loadAddFormDelegate(arch\node\IFormNode $form, $delegateId, INode $node);
    public function loadEditFormDelegate(arch\node\IFormNode $form, $delegateId, INode $node, $versionId=null);
    public function loadDeleteFormDelegate(arch\node\IFormNode $form, $delegateId, INode $node);
}

interface IVersionedType
{
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

interface IVersion
{
    public function getId(): ?string;
    public function getDate();
    public function getOwnerId();
    public function getOwner();
    public function getTitle(): ?string;
    public function isActive(INode $node);
}


interface IFormDelegate extends arch\node\ISelfContainedRenderableDelegate
{
    public function setNode(INode $node);
    public function getNode();
    public function setVersionId($versionId);
    public function getVersionId();
    public function shouldMakeNew(bool $flag=null);
    public function isSpecificVersion(bool $flag=null);
    public function getDefaultNodeValues();
    public function validate();
    public function apply(): mixed;
}
