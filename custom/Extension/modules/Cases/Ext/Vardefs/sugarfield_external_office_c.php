<?php

// created: 2013-12-20 15:20:29
if (!empty($dictionary)) {
    unset($dictionary['Case']['fields']['external_office_c']['options']);
}
$dictionary['Case']['fields']['external_office_c']['function'] = 'getExternalOfficeList';
?>