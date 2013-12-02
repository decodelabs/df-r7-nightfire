<?php

echo $this->import->component('DetailHeaderBar', '~admin/nightfire/elements/', $this['element']);


// Attributes
echo $this->html->attributeList($this['element'])
    
    ->addField('slug')
    ->addField('name')
    ->addField('owner', function($element) {
        return $this->import->component('UserLink', '~admin/users/clients/', $element['owner'])
            ->setDisposition('transitive');
    })
    ->addField('creationDate', $this->_('Created'), function($element) {
        return $this->html->timeSince($element['creationDate']);
    })
    ->addField('lastEditDate', $this->_('Last edited'), function($element) {
        return $this->html->timeSince($element['lastEditDate']);
    })
    ->addField('body', function($element) {
        return $this->nightfire->renderSlot($element['body']);
    })
    ;
