<?php

require_once('include/MVC/Controller/SugarController.php');

class CasesController extends SugarController {

    public function __construct() {
        parent::SugarController();
    }

    public function action_EmailMacro() {
        $case_number = $_GET['case_number'];
        $case = new aCase();
        $emailmacro = str_replace('%1', $case_number, $case->getEmailSubjectMacro());
        echo $emailmacro;
        exit;
    }

}

?>