<?php

// created: 2016-04-08 13:01:38
$viewdefs['Opportunities']['QuickCreate'] = array(
    'templateMeta' =>
    array(
        'maxColumns' => '2',
        'widths' =>
        array(
            0 =>
            array(
                'label' => '10',
                'field' => '30',
            ),
            1 =>
            array(
                'label' => '10',
                'field' => '30',
            ),
        ),
        'javascript' => '{$PROBABILITY_SCRIPT}',
        'useTabs' => false,
        'tabDefs' =>
        array(
            'DEFAULT' =>
            array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
        ),
    ),
    'panels' =>
    array(
        'DEFAULT' =>
        array(
            0 =>
            array(
                0 =>
                array(
                    'name' => 'name',
                    'displayParams' =>
                    array(
                        'required' => true,
                    ),
                ),
                1 =>
                array(
                    'name' => 'account_name',
                ),
            ),
            1 =>
            array(
                0 =>
                array(
                    'name' => 'currency_id',
                ),
                1 =>
                array(
                    'name' => 'opportunity_type',
                ),
            ),
            2 =>
            array(
                0 => 'amount',
                1 => 'date_closed',
            ),
            3 =>
            array(
                0 => 'next_step',
                1 => 'sales_stage',
            ),
            4 =>
            array(
                0 =>
                array(
                    'name' => 'product_c',
                    'label' => 'LBL_PRODUCT',
                ),
                1 =>
                array(
                    'name' => 'country_c',
                    'label' => 'LBL_COUNTRY',
                ),
            ),
            5 =>
            array(
                0 => 'lead_source',
                1 => 'probability',
            ),
            6 =>
            array(
                0 =>
                array(
                    'name' => 'assigned_user_name',
                ),
            ),
        ),
    ),
);
