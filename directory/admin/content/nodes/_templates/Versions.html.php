<?php
echo $this->import->component('DetailHeaderBar', '~admin/content/nodes/', $this['node']);

echo $this->import->component('VersionList', '~admin/content/nodes/');
