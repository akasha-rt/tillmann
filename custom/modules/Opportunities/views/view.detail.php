<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('include/MVC/View/views/view.detail.php');
require_once('custom/include/function/utilFunctions.php');

class OpportunitiesViewDetail extends ViewDetail {

    public $useForSubpanel = true;

    function OpportunitiesViewDetail() {
        parent::ViewDetail();
    }

    function display() {

        $this->ss->assign('COUNTRY', get_dd_detail('Country', $this->bean->country_c));
        parent::display();
    }

}

?>
