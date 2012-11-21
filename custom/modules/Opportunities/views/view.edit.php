<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('include/MVC/View/views/view.edit.php');
require_once('custom/include/function/utilFunctions.php');

class OpportunitiesViewEdit extends ViewEdit {

    public $useForSubpanel = true;

    function OpportunitiesViewEdit() {
        parent::ViewEdit();
    }

    function display() {
        //$this->ss->assign('COUNTRY', '<select title="" size="1" id="country_c" name="country_c">' . get_dd_edit('Country', $this->bean->country_c) . '</select>');
        parent::display();
    }

}
?>