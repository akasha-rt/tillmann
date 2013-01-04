

<?php

require_once('include/MVC/Controller/SugarController.php');

class EmailTemplatesController extends SugarController {

    public function __construct() {
        parent::SugarController();
    }

    public function action_cannedresponse() {

        global $db;
        global $current_user;
        $id = create_guid();

        require_once('modules/EmailTemplates/EmailTemplate.php');

        $EmailTemplate = new EmailTemplate();
        $EmailTemplate->id = $id;
        $EmailTemplate->new_with_id = true;
        $EmailTemplate->date_entered = date('Y-m-d', time());
        $EmailTemplate->date_modified = date('Y-m-d', time());
        $EmailTemplate->name = $_REQUEST['name'];
        $EmailTemplate->subject = $_REQUEST['sub'];
        $EmailTemplate->deleted = '0';
        $EmailTemplate->department = $_REQUEST['dept'];
        $EmailTemplate->published = 'off';
        $EmailTemplate->body = $_REQUEST['body'];
        $EmailTemplate->body_html = str_replace("h3a5sh", "#", $_REQUEST['body_html']);
        $EmailTemplate->modified_user_id = $current_user->id;
        $EmailTemplate->created_by = $current_user->id;

        $EmailTemplate->save();

        //echo "Canned response created.";
        exit;
    }

}
?>