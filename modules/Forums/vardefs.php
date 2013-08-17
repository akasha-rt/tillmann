<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['Forums'] = array(
    'table' => 'forums', 'comment' => 'Forums are named collections of threads',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'required' => true,
            'type' => 'id',
            'reportable'=>false,
            'comment' => 'Unique identifier',
        ),
        'date_entered' => array(
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record created',
        ),
        'category' => array(
            'name' => 'category',
            'vname' => 'LBL_CATEGORY',
            'type' => 'varchar',
            'required' => true,
            'len' => 255,
            'comment' => 'Category forum is associated',
        ),
        'category_ranking' => array(
            'name' => 'category_ranking',
            'source' => 'non-db',
            'type' => 'int',
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
            'comment' => 'User who created record',
        ),
        'date_modified' => array(
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record last modified',
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
            'default' => '0',
            'reportable'=>true,
            'comment' => 'User ID who last modified record',
        ),
        // basically the post count
        // threads are posts, so they are included in this
        //   (which is why it's called threadAndPostCount)
        'threadandpostcount'=>array(
            'name' =>'threadandpostcount',
            'vname' => 'LBL_THREAD_POST_COUNT',
            'type' => 'int',
            'default' => '0',
            'len' => 255,
            'comment' => 'The number of posts in this Forum. Threads are included in this count.'
        ),
        'threadcount'=>array(
            'name' =>'threadcount',
            'vname' => 'LBL_THREAD_COUNT',
            'type' => 'int',
            'default' => '0',
            'len' => 255,
            'comment' => 'The number of threads in this Forum.'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => true,
            'default' => '0',
            'comment' => 'Record deletion indicator',
        ),  
        'title' => array(
            'name' => 'title',
            'vname' => 'LBL_TITLE',
            'required' => true,
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'Forum title',
        ),
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'required' => false,
            'type' => 'text',
            'comment' => 'Forum description',
        ),
        'recent_thread_title' => array(
            'name' => 'recent_thread_title',
            'source'=>'non-db',
            'type' => 'varchar',
        ),
        'recent_thread_id' => array(
            'name' => 'recent_thread_id',
            'source'=>'non-db',
            'type' => 'id',
        ),
        'recent_thread_modified_name' => array(
            'name' => 'recent_thread_modified_name',
            'source'=>'non-db',
            'type' => 'assigned_user_name',
        ),
        'recent_thread_modified_id' => array(
            'name' => 'recent_thread_modified_id',
            'source'=>'non-db',
            'type' => 'id',
        ),
    ),
    'indices' => array(
        array('name' =>'forum_primary_key_index',
            'type' =>'primary',
            'fields'=>array('id')
            ),
    ),
);
?>
