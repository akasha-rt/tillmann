<?php

$module_name = 'bc_ExternalOffice';
$viewdefs [$module_name] =
        array(
            'EditView' =>
            array(
                'templateMeta' =>
                array(
                    'form' =>
                    array(
                        'buttons' =>
                        array(
                            0 => 'SAVE',
                            1 => 'CANCEL',
                            2 => array(
                                'customCode' => '<input type="button" value="Test Connection" name="TestConnection" id="TestConnection">
                                    <input type="hidden" id="old_api_pass" name="old_api_pass" value="{$fields.api_user_pass.value}" >'),
                        ),
                    ),
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
                    'includes' =>
                    array(
                        0 =>
                        array(
                            'file' => 'custom/include/js/jquery.md5.min.js',
                        ),
                        1 =>
                        array(
                            'file' => 'custom/modules/bc_ExternalOffice/javascript/EditView.js',
                        ),
                    ),
                    'useTabs' => false,
                    'syncDetailEditViews' => true,
                ),
                'panels' =>
                array(
                    'default' =>
                    array(
                        0 =>
                        array(
                            0 => 'name',
                            1 =>
                            array(
                                'name' => 'office_code',
                                'label' => 'LBL_OFFICE_CODE',
                            ),
                        ),
                        1 =>
                        array(
                            0 =>
                            array(
                                'name' => 'api_url',
                                'label' => 'LBL_API_URL',
                            ),
                        ),
                        2 =>
                        array(
                            0 =>
                            array(
                                'name' => 'api_user',
                                'label' => 'LBL_API_USER',
                            ),
                        ),
                        3 =>
                        array(
                            0 =>
                            array(
                                'name' => 'api_user_pass',
                                'label' => 'LBL_API_USER_PASS',
                                'customCode' => '<input type="password" name="api_user_pass" id="api_user_pass" size="30" maxlength="255" value="{$fields.api_user_pass.value}" title="">'
                            ),
                        ),
                    ),
                ),
            ),
);
?>
