<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once 'include/MVC/View/views/view.detail.php';

class CasesViewDetail extends ViewDetail {

    function __construct() {
        parent::ViewDetail();
    }

    public function getModuleTitle($show_help = true) {
        global $current_user, $db;
        $follow_result = $db->query("SELECT id from followup where module_id='{$this->bean->id}' and deleted=0 and module_name='Cases' and user_id='{$current_user->id}'");
        $follow_row = $db->fetchByAssoc($follow_result);
        $watchIcon = '<script type="text/javascript" src="custom/include/js/Home/add_follow_list.js"></script><h2>';
        if ($follow_row)
            $watchIcon .= '<img src="custom/image/follow2.png" style="height:17px;width:20px;cursor:pointer;" id="' . $this->bean->id . '" onclick="addToWatchList(this,\'' . $current_user->id . '\',\'Cases\');" title="Remove from Watch List" />';
        else
            $watchIcon .= '<img src="custom/image/follow1.png" style="height:17px;width:20px;cursor:pointer;" id="' . $this->bean->id . '" onclick="addToWatchList(this,\'' . $current_user->id . '\',\'Cases\');" title="Add to Watch List" />';
        $print = '<a href="index.php?module=Cases&amp;action=index"><img src="themes/Sugar5/images/icon_Cases_32.gif" alt="Cases" title="Cases" align="absmiddle"></a><span class="pointer">»</span>' . $this->bean->name . '</h2>';
        echo $watchIcon . $print;
        parent::getModuleTitle($show_help);
    }

    function display() {
        require_once 'custom/include/custom_utils.php';
        if (strlen(nl2br($this->bean->description)) > 250) {
            $short_description = "<span id='shortDesc' style='display:block'>" . nl2br(substr($this->bean->description, 0, 250));
            $short_description .= "...<a href='javascript:void(0)' id='showmore' style='text-decoration:none;'>[Show More]</a></span>";
            $long_description = "<span id='longDesc'  style='display:none'>" . nl2br($this->bean->description);
            $long_description .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' id='showless' style='text-decoration:none;'>[Show Less]</a></span>";
            $script = <<<EOS
            <script type="text/javascript">
            //$(document).ready(function(){
                $('#showmore').click(function(){
                    $('#shortDesc').slideUp('slow');
                    $('#longDesc').slideDown('slow');
                    });
                $('#showless').click(function(){
                    $('#shortDesc').slideDown('slow');
                    $('#longDesc').slideUp('slow');
                });
            //});
            </script>
EOS;
            $this->ss->assign('DESCRIPTION', $short_description . $long_description . $script);
        } else {
            $description = $this->bean->description;
            $this->ss->assign('DESCRIPTION', nl2br($description));
        }
        /* Display Products */
        $products = getProductName($this->bean->product_c);
        $html = '';
        foreach ($products as $key => $prod_name) {
            $html .= '<li style="margin-left:10px;">' . $prod_name . '</li>';
        }
        $this->ss->assign('PRODUCTS', $html);

        $supplierList = '';
        if ($this->bean->supplier_c != '') {
            $supplier_temp = explode(",", $this->bean->supplier_c);
            foreach ($supplier_temp as $ids => $vals) {
                if (!empty($vals)) {
                    $supplierList .= '<li style="margin-left:10px;">' . $vals . '</li>';
                }
            }
        }
        $this->ss->assign('SUPPLIERS', $supplierList);
        /* End */
        parent::display();
    }

}

?>