<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$dictionary['Case']['fields']['account_name']['required'] = false;
$dictionary['Case']['fields']['resolution']['unified_search'] = true;
$dictionary['Case']['fields']['description']['unified_search'] = true;

$dictionary['Case']['fields']['complaint_product'] = array(
    'name' => 'complaint_product',
    'type' => 'varchar',
    'source' => 'non-db',
    'vname' => 'LBL_COMPLAINT_PRODUCT'
);

$dictionary['Case']['fields']['complaint'] = array(
    'name' => 'complaint',
    'type' => 'varchar',
    'source' => 'non-db',
    'vname' => 'LBL_COMPLAINT'
);
?>
