<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('include/MVC/View/views/view.list.php');

class AccountsViewList extends ViewList {

    function __construct() {
        parent::__construct();
    }

    function preDisplay() {
        echo '<script type="text/javascript" src="custom/include/js/Accounts/accountItemHistory.js"></script>';
        parent::preDisplay();
    }

}

?>