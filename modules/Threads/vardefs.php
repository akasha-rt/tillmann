<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['Threads'] = array(
	'table' => 'threads',
	'fields' => array(
		'id' => array(
			'name' => 'id',
			'vname' => 'LBL_ID',
			'required' => true,
			'type' => 'id',
			'reportable'=>false,
		),
		'date_entered' => array(
			'name' => 'date_entered',
			'vname' => 'LBL_DATE_ENTERED',
			'type' => 'datetime',
			'required' => true,
		),
		'created_by' => array(
			'name' => 'created_by',
			'rname' => 'user_name',
			'id_name' => 'created_by',
			'vname' => 'LBL_CREATED_BY',
			'type' => 'assigned_user_name',
			'table' => 'created_by_users',
			'isnull' => 'false',
			'dbType' => 'id',
		),
		'date_modified' => array(
			'name' => 'date_modified',
			'vname' => 'LBL_DATE_MODIFIED',
			'type' => 'datetime',
			'required' => true,
		),
		'modified_user_id' => array(
			'name' => 'modified_user_id',
			'rname' => 'user_name',
			'id_name' => 'modified_user_id',
			'vname' => 'LBL_MODIFIED_USER_ID',
			'type' => 'assigned_user_name',
			'table' => 'users',
			'isnull' => 'false',
			'dbType' => 'id',
			'required' => true,
			'default' => '',
			'reportable'=>true,
		),
        'postcount'=>array(
            'name' =>'postcount',
            'vname' => 'LBL_POST_COUNT',
            'type' => 'int',
            'default' => '0',
            'len' => 255,
        ),
		'deleted' => array(
			'name' => 'deleted',
			'vname' => 'LBL_DELETED',
			'type' => 'bool',
			'required' => true,
			'default' => '0',
		),	
		'title' => array(
			'name' => 'title',
			'vname' => 'LBL_TITLE',
			'required' => true,
			'type' => 'varchar',
			'len' => 255,
		),
        'description_html' => array(
          'name' => 'description_html',
          'vname' => 'LBL_BODY',
          'type' => 'text',
        ),
		'forum_id' => array(
			'name' => 'forum_id',
			'vname' => 'LBL_FORUM_ID',
			'type' => 'id',
		),
		'is_sticky' => array(
			'name' => 'is_sticky',
			'vname' => 'LBL_IS_STICKY',
			'type' => 'bool',
			'default' => '0',
		),
		'view_count' => array(
			'name' => 'view_count',
			'vname' => 'LBL_VIEW_COUNT',
			'type' => 'int',
			'required' => true,
			'default' => 0,
		),
		'parent_forum' => array(
            'name' => 'parent_forum',
            'vname' => 'LBL_PARENT_FORUM_NAME',
            'type' => 'varchar',
            'len' => 255,
        ),
		'recent_post_date' => array(
	      'name' => 'recent_post_date',
		  'vname' => 'LBL_RECENT_POST_DATE',
          'type' => 'datetime',
	    ),
	    'recent_post_modified_name' => array(
	      'name' => 'recent_post_modified_name',
	      'vname' => 'LBL_RECENT_POST_MODIFIED_NAME',
          'type' => 'varchar',
	      'len' => 60,
	    ),
		'created_by_user'=>array(
			'name' =>'created_by_user',
			'source' => 'non-db',
		    'type' => 'assigned_user_name',
		),		
		'modified_by_user'=>array(
			'name' =>'modified_by_user',
			'source' => 'non-db',
		    'type' => 'assigned_user_name',
		),
		'stickyDisplay'=>array(
			'name' =>'stickyDisplay',
			'source' => 'non-db',
		    'type' => 'bool',
		),
	    'recent_post_title' => array(
	      'name' => 'recent_post_title',
          'source' => 'non-db',
		  'type' => 'varchar',
	    ),
	    'recent_post_id' => array(
	      'name' => 'recent_post_id',
          'source' => 'non-db',
	      'type' => 'id',
	    ),
	    'recent_post_modified_id' => array(
	      'name' => 'recent_post_modified_id',
          'source' => 'non-db',
	      'type' => 'id',
	    ),
		'accounts' => array (
			'name' => 'accounts',
			'type' => 'link',
			'relationship' => 'accounts_threads',
			'source' => 'non-db',
			'vname' => 'LBL_ACCOUNTS',
		),  
		'bugs' => array (
			'name' => 'bugs',
			'type' => 'link',
			'relationship' => 'bugs_threads',
			'source' => 'non-db',
			'vname' => 'LBL_BUGS',
		),  
		'cases' => array (
			'name' => 'cases',
			'type' => 'link',
			'relationship' => 'cases_threads',
			'source' => 'non-db',
			'vname' => 'LBL_CASES',
		),  
		'opportunities' => array (
			'name' => 'opportunities',
			'type' => 'link',
			'relationship' => 'opportunities_threads',
            'module'=>'opportunities',
            'bean_name'=>'Opportunities',
    		'source' => 'non-db',
			'vname' => 'LBL_OPPORTUNITIES',
		), 
        'project' => array (
            'name' => 'project',
            'type' => 'link',
            'relationship' => 'project_threads',
            'module'=>'project',
            'bean_name'=>'Project',
            'source' => 'non-db',
            'vname' => 'LBL_PROJECT',
        ), 
	),
	'indices' => array(
		array('name' =>'thread_primary_key_index',
			'type' =>'primary',
			'fields'=>array('id')
			),
	),
);
?>
