<?php

$listViewDefs ['Emails'] =
        array(
            'NAME' =>
            array(
                'width' => '25%',
                'label' => 'LBL_LIST_SUBJECT',
                'link' => true,
                'default' => true,
            ),
            /*            'DESCRIPTION' => array(
              'label' => 'Body',
              'width' => '20%',
              'default' => true,
              ), */
            'STATUS' =>
            array(
                'label' => 'Status',
                'width' => '10%',
                'default' => true,
            ),
            'DATE_ENTERED' =>
            array(
                'type' => 'datetime',
                'label' => 'LBL_DATE_ENTERED',
                'width' => '10%',
                'default' => true,
            ),
            'DATE_MODIFIED' =>
            array(
                'width' => '20%',
                'label' => 'LBL_DATE_MODIFIED',
                'link' => false,
                'default' => false,
            ),
);
?>
