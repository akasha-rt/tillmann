<?php

// created: 2012-05-23 12:05:45
//Get value for country from function for opp
$dictionary['Opportunity']['fields']['country_c']['type'] = 'enum';
unset($dictionary['Opportunity']['fields']['country_c']['options']);
$dictionary['Opportunity']['fields']['country_c']['function'] = array(
    'name' => 'getCountryForOpp',
    'returns' => 'html',
    'params' => 'Country',
    'include' => 'custom/include/function/utilFunctions.php'
);
?>