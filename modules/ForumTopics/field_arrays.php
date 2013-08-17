<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$fields_array['ForumTopic'] = array ('column_fields' => Array("id"
		,"name"
		,"list_order"
		,"date_entered"
		,"date_modified"
		,"modified_user_id"
		, "created_by"
		),
        'list_fields' =>  Array('id', 'name', 'list_order'),
    'required_fields' =>  array("name"=>1,'list_order'=>1,),
);
?>
