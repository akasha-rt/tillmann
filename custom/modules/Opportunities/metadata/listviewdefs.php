<?php

$listViewDefs ['Opportunities'] =
        array(
            'NAME' =>
            array(
                'width' => '20%',
                'label' => 'LBL_LIST_OPPORTUNITY_NAME',
                'link' => true,
                'default' => true,
            ),
            'CONTACT_NAME' =>
            array(
                'type' => 'relate',
                'link' => 'contacts',
                'label' => 'LBL_CONTACT_NAME',
                'width' => '10%',
                'default' => true,
            ),
            'SALES_STAGE' =>
            array(
                'width' => '10%',
                'label' => 'LBL_LIST_SALES_STAGE',
                'default' => true,
            ),
            'COUNTRY_C' =>
            array(
                'width' => '10%',
                'label' => 'LBL_COUNTRY',
                'default' => true,
            ),
            'AMOUNT_USDOLLAR' =>
            array(
                'width' => '10%',
                'label' => 'LBL_LIST_AMOUNT_USDOLLAR',
                'align' => 'right',
                'default' => true,
                'currency_format' => true,
            ),
            'DATE_CLOSED' =>
            array(
                'width' => '10%',
                'label' => 'LBL_LIST_DATE_CLOSED',
                'default' => true,
            ),
            'ASSIGNED_USER_NAME' =>
            array(
                'width' => '5%',
                'label' => 'LBL_LIST_ASSIGNED_USER',
                'module' => 'Employees',
                'id' => 'ASSIGNED_USER_ID',
                'default' => true,
            ),
            'DATE_ENTERED' =>
            array(
                'width' => '10%',
                'label' => 'LBL_DATE_ENTERED',
                'default' => true,
            ),
            'OPPORTUNITY_TYPE' =>
            array(
                'width' => '15%',
                'label' => 'LBL_TYPE',
                'default' => false,
            ),
            'LEAD_SOURCE' =>
            array(
                'width' => '15%',
                'label' => 'LBL_LEAD_SOURCE',
                'default' => false,
            ),
            'NEXT_STEP' =>
            array(
                'width' => '10%',
                'label' => 'LBL_NEXT_STEP',
                'default' => false,
            ),
            'PROBABILITY' =>
            array(
                'width' => '10%',
                'label' => 'LBL_PROBABILITY',
                'default' => false,
            ),
            'CREATED_BY_NAME' =>
            array(
                'width' => '10%',
                'label' => 'LBL_CREATED',
                'default' => false,
            ),
            'MODIFIED_BY_NAME' =>
            array(
                'width' => '5%',
                'label' => 'LBL_MODIFIED',
                'default' => false,
            ),
);
?>
