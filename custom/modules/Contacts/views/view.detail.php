<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once 'modules/Contacts/views/view.detail.php';

class CustomContactsViewDetail extends ContactsViewDetail {

    function __construct() {
        parent::__construct();
    }

    function display() {
        if (strlen(nl2br($this->bean->description)) > 250) {
            $short_description = "<span id='shortDesc' style='display:block'>" . nl2br(substr($this->bean->description, 0, 50));
            $short_description .= "...<a href='javascript:void(0)' id='showmore' style='text-decoration:none;'>[Show More]</a></span>";
            $long_description = "<span id='longDesc'  style='display:none'>" . nl2br($this->bean->description);
            $long_description .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' id='showless' style='text-decoration:none;'>[Show Less]</a></span>";
            $script = <<<EOS
            <script type="text/javascript">
            $(document).ready(function(){
                $('#showmore').click(function(){
                    $('#shortDesc').slideUp('slow');
                    $('#longDesc').slideDown('slow');
                    });
                $('#showless').click(function(){
                    $('#shortDesc').slideDown('slow');
                    $('#longDesc').slideUp('slow');
                });
            });
            </script>
EOS;
            $this->ss->assign('DESCRIPTION', $short_description . $long_description . $script);
        } else {
            $description = $this->bean->description;
            $this->ss->assign('DESCRIPTION', nl2br($description));
        }

        parent::display();
    }

}

?>