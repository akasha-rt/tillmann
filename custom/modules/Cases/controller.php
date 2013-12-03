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

    public function action_saveDataInbcDropdown() {
        global $db;
        //if()
        if (isset($_REQUEST['supplier_c']) && $_REQUEST['supplier_c'] == 1) {
            $query = "SELECT
                                              bc_storedata.supplierid   AS id,
                                              bc_storedata.supplierid  AS name
                                            FROM bc_storedata
                                            WHERE bc_storedata.deleted = 0
                                                AND bc_storedata.supplierid != ''
                                                AND bc_storedata.supplierid IS NOT NULL
                                                AND bc_storedata.supplierid LIKE '%{$_GET["q"]}%'
                                            GROUP BY bc_storedata.supplierid
                                            ORDER BY bc_storedata.name  LIMIT 500";
        } else if (isset($_REQUEST['product_c']) && $_REQUEST['product_c'] == 1) {
            $query = "SELECT
                                              bc_storedata.sku   AS id,
                                              bc_storedata.name  AS name
                                            FROM bc_storedata
                                            WHERE bc_storedata.deleted = 0
                                                AND bc_storedata.name != ''
                                                AND bc_storedata.name IS NOT NULL
                                                AND bc_storedata.sku != ''
                                                AND bc_storedata.sku IS NOT NULL
                                                AND (bc_storedata.name LIKE '%{$_GET["q"]}%'
                                                OR bc_storedata.sku LIKE '%{$_GET["q"]}%')
                                            GROUP BY bc_storedata.sku
                                            ORDER BY bc_storedata.name  LIMIT 500";
        } else {
            $query = "";
        }

        $arr = array();
        $rs = $db->query($query);

        # Collect the results
        while ($obj = $db->fetchByAssoc($rs)) {
            $arr[] = $obj;
        }


        $json_response = JSON::encode($arr);

        # Optionally: Wrap the response in a callback function for JSONP cross-domain support
        if ($_GET["callback"]) {
            $json_response = $_GET["callback"] . "(" . $json_response . ")";
        }

        # Return the response
        echo $json_response;
        exit;
    }

}

?>