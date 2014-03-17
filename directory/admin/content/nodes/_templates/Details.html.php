<?php
use df\neon;

echo $this->import->component('DetailHeaderBar', '~admin/content/nodes/', $this['node']);


// Attributes
echo $this->html->attributeList($this['node'])

    ->addField('title')
    ->addField('slug')
    ->addField('type')
    ->addField('owner', function($node) {
        return $this->import->component('UserLink', '~admin/users/clients/', $node['owner'])
            ->setDisposition('transitive');
    })
    ->addField('isLive', function($node) {
        return $this->html->booleanIcon($node['isLive']);
    })
    ->addField('creationDate', $this->_('Created'), function($node) {
        return $this->html->timeSince($node['creationDate']);
    })
    ->addField('currentVersion', $this->_('Version'), function($node) {
        if(!$node['versionCount']) {
            return;
        }

        return $this->_('%v% of %c%', [
            '%v%' => $node['currentVersion'],
            '%c%' => $node['versionCount']
        ]);
    })
    ->addField('preview', function($node) {
        return $node->getType()->renderPreview($this->view, $node);
    })
    ;
