<?php
// created: 2016-04-08 13:01:38
$viewdefs['Contacts']['EditView'] = array (
  'templateMeta' => 
  array (
    'form' => 
    array (
      'hidden' => 
      array (
        0 => '<input type="hidden" name="opportunity_id" value="{$smarty.request.opportunity_id}">',
        1 => '<input type="hidden" name="case_id" value="{$smarty.request.case_id}">',
        2 => '<input type="hidden" name="bug_id" value="{$smarty.request.bug_id}">',
        3 => '<input type="hidden" name="email_id" value="{$smarty.request.email_id}">',
        4 => '<input type="hidden" name="inbound_email_id" value="{$smarty.request.inbound_email_id}">',
      ),
    ),
    'maxColumns' => '2',
    'widths' => 
    array (
      0 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
      1 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
    ),
    'useTabs' => false,
    'tabDefs' => 
    array (
      'LBL_CONTACT_INFORMATION' => 
      array (
        'newTab' => false,
        'panelDefault' => 'expanded',
      ),
      'LBL_PANEL_ADVANCED' => 
      array (
        'newTab' => false,
        'panelDefault' => 'expanded',
      ),
      'LBL_PANEL_ASSIGNMENT' => 
      array (
        'newTab' => false,
        'panelDefault' => 'expanded',
      ),
    ),
  ),
  'panels' => 
  array (
    'lbl_contact_information' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'first_name',
          'customCode' => '{html_options name="salutation" id="salutation" options=$fields.salutation.options selected=$fields.salutation.value}&nbsp;<input name="first_name"  id="first_name" size="25" maxlength="25" type="text" value="{$fields.first_name.value}">',
        ),
        1 => 
        array (
          'name' => 'last_name',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'phone_mobile',
          'comment' => 'Mobile phone number of the contact',
          'label' => 'LBL_MOBILE_PHONE',
        ),
        1 => 
        array (
          'name' => 'phone_fax',
          'comment' => 'Contact fax number',
          'label' => 'LBL_FAX_PHONE',
        ),
      ),
      2 => 
      array (
        0 => 'department',
        1 => 
        array (
          'name' => 'type_c',
          'studio' => 'visible',
          'label' => 'LBL_TYPE',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'primary_address_street',
          'hideLabel' => true,
          'type' => 'address',
          'displayParams' => 
          array (
            'key' => 'primary',
            'rows' => 2,
            'cols' => 30,
            'maxlength' => 150,
          ),
        ),
        1 => 
        array (
          'name' => 'alt_address_street',
          'hideLabel' => true,
          'type' => 'address',
          'displayParams' => 
          array (
            'key' => 'alt',
            'copy' => 'primary',
            'rows' => 2,
            'cols' => 30,
            'maxlength' => 150,
          ),
        ),
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'email1',
          'studio' => 'false',
          'label' => 'LBL_EMAIL_ADDRESS',
        ),
        1 => 
        array (
          'name' => 'account_name',
          'displayParams' => 
          array (
            'key' => 'billing',
            'copy' => 'primary',
            'billingKey' => 'primary',
            'additionalFields' => 
            array (
              'phone_office' => 'phone_work',
            ),
          ),
        ),
      ),
      5 => 
      array (
        0 => 
        array (
          'name' => 'description',
          'label' => 'LBL_DESCRIPTION',
        ),
      ),
    ),
    'LBL_PANEL_ADVANCED' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'report_to_name',
          'label' => 'LBL_REPORTS_TO',
        ),
        1 => 
        array (
          'name' => 'sync_contact',
          'comment' => 'Synch to outlook?  (Meta-Data only)',
          'label' => 'LBL_SYNC_CONTACT',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'lead_source',
          'comment' => 'How did the contact come about',
          'label' => 'LBL_LEAD_SOURCE',
        ),
        1 => 
        array (
          'name' => 'do_not_call',
          'comment' => 'An indicator of whether contact can be called',
          'label' => 'LBL_DO_NOT_CALL',
        ),
      ),
      2 => 
      array (
        0 => 'campaign_name',
      ),
    ),
    'LBL_PANEL_ASSIGNMENT' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'assigned_user_name',
          'label' => 'LBL_ASSIGNED_TO_NAME',
        ),
      ),
    ),
  ),
);