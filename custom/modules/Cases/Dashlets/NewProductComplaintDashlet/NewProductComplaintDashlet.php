<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/* * *******************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 * ****************************************************************************** */




require_once('custom/include/Dashlets/DashletGeneric.php');

class NewProductComplaintDashlet extends CustomDashletGeneric {

    function NewProductComplaintDashlet($id, $def = null) {
        global $current_user, $app_strings;
        require('custom/modules/Cases/Dashlets/NewProductComplaintDashlet/NewProductComplaintDashlet.data.php');

        parent::DashletGeneric($id, $def);

        if (empty($def['title'])) {
            $this->title = translate('LBL_PRODUCT_COMPLAINT_DASHLET', 'Cases');
        }
        $this->columns = $dashletData['NewProductComplaintDashlet']['columns'];
        $this->seedBean = new aCase();
        $this->lvs = new ListViewSmarty();
    }

    function process($lvsParams = array()) {
        $this->lvs->quickViewLinks = false;
        if (isset($_REQUEST['lvso']) && $_REQUEST['Home2_CASE_ORDER_BY']) {
            $field = $_REQUEST['Home2_CASE_ORDER_BY'];
            $order = $_REQUEST['lvso'];
            $orderBy = " Order By  $field $order ";
        } else {
            $orderBy = " Order By  complaint desc ";
        }
        $this->seedBean->ComplaintDashlet = true;
        $lvsParams = array(
            'custom_select' => "SELECT a.complaint_product AS complaint_product,a.complaint AS complaint ",
            'custom_from' => " FROM
                                        (SELECT
                                        bc_storedata.sku      AS complaint_product,
                                        COUNT(bc_storedata.sku) AS complaint FROM cases
                                        LEFT JOIN cases_cstm
                                          ON cases.id = cases_cstm.id_c
                                          LEFT JOIN bc_storedata ON  FIND_IN_SET(bc_storedata.sku,cases_cstm.product_c) WHERE cases.deleted = 0
                                        AND cases_cstm.technical_c = 'Complaint'
                                        AND cases_cstm.product_c IS NOT NULL
                                         GROUP BY bc_storedata.sku
                                        HAVING COUNT(bc_storedata.sku) >= 2 ) AS a",
            'custom_order_by' => $orderBy,
            'distinct' => true
        );
        parent::process($lvsParams);
        $this->seedBean->ComplaintDashlet = false;
    }

    public function displayOptions() {
        $ss = new Sugar_Smarty();
        $ss->assign('titleLBL', translate('LBL_DASHLET_OPT_TITLE', 'Home'));
        $ss->assign('title', $this->title);
        $ss->assign('id', $this->id);
        $ss->assign('saveLBL', $GLOBALS['app_strings']['LBL_SAVE_BUTTON_LABEL']);
        $ss->assign('displayRows', $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_DISPLAY_ROWS']);

        // display rows
        $displayRowOptions = $GLOBALS['sugar_config']['dashlet_display_row_options'];
        $ss->assign('displayRowOptions', $displayRowOptions);
        $ss->assign('displayRowSelect', $this->displayRows);

        if ($this->isAutoRefreshable()) {
            $ss->assign('isRefreshable', true);
            $ss->assign('autoRefresh', $GLOBALS['app_strings']['LBL_DASHLET_CONFIGURE_AUTOREFRESH']);
            $ss->assign('autoRefreshOptions', $this->getAutoRefreshOptions());
            $ss->assign('autoRefreshSelect', $this->autoRefresh);
        }

        return $ss->fetch('custom/modules/Cases/Dashlets/NewProductComplaintDashlet/NewProductComplaintDashletConfigure.tpl');
    }

    /**
     * @see Dashlet::saveOptions()
     */
    public function saveOptions($req) {
        $options = array();

        if (isset($req['title'])) {
            $options['title'] = $req['title'];
        }
        $options['autoRefresh'] = empty($req['autoRefresh']) ? '0' : $req['autoRefresh'];

        $options['displayRows'] = empty($req['displayRows']) ? '5' : $req['displayRows'];


        return $options;
    }

}

?>
