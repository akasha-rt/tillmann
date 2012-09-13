<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class CRMSummary extends DashletGenericBarChart {

    protected $_seedName = 'Accounts';
    var $current_user = '';
    var $where = '';

    function CRMSummary($id, $def = null) {
        global $current_user, $app_strings;
        require('custom/modules/Charts/Dashlets/CRMSummary/CRMSummary.data.php');
        parent::__construct($id, $def);
        $this->_searchFields = $dashletData['CRMSummary']['searchFields'];
    }

    protected function getDataset() {
        global $db, $current_user;
        $this->current_user = $current_user;
        $returnArray = array();

        foreach ($this->assigned_user_id as $key => $userId) {
            $this->where .= " assigned_user_id = '{$userId}' OR ";
        }
        $this->where = rtrim($this->where, ' OR ');

        if (is_null($this->where) || $this->where == "") {
            $this->where = "assigned_user_id = '{$this->current_user->id}'";
        }


        //Total Email
        $EmailSql = $this->getNumberOfEmailsQuery();
        $EmailData = $db->query($EmailSql);
        $EmailRow = $db->fetchByAssoc($EmailData);

        //Total Opp
        $OppSql = $this->getNumberOfOpportunitiesQuery();
        $OppData = $db->query($OppSql);
        $OppRow = $db->fetchByAssoc($OppData);

        //OVerdue tasks
        $OverduetaskSql = $this->getNumberOfOverdueTaskQuery();
        $OverduetaskData = $db->query($OverduetaskSql);
        $OverduetaskRow = $db->fetchByAssoc($OverduetaskData);

        //Open Cases
        $OpencasesSql = $this->getNumberOfOpenCasesQuery();
        $OpencasesData = $db->query($OpencasesSql);
        $OpencasesRow = $db->fetchByAssoc($OpencasesData);

        //Pending Cases
        $PendingcasesSql = $this->getNumberOfPendingCasesQuery();
        $PendingcasesData = $db->query($PendingcasesSql);
        $PendingcasesRow = $db->fetchByAssoc($PendingcasesData);

        //PO cases
        $POcasesSql = $this->getNumberOfPOCasesQuery();
        $POcasesData = $db->query($POcasesSql);
        $POcasesRow = $db->fetchByAssoc($POcasesData);

        //New cases
        $NewcasesSql = $this->getNumberOfNewCasesQuery();
        $NewcasesData = $db->query($NewcasesSql);
        $NewcasesRow = $db->fetchByAssoc($NewcasesData);

        //Cases on FollowUp
        $casesInFollowupSql = $this->getNumberOfCasesInFollowupQuery();
        $casesInFollowupData = $db->query($casesInFollowupSql);
        $casesInFollowupRow = $db->fetchByAssoc($casesInFollowupData);

        //Total calls
        $CallSql = $this->getNumberOfCallsQuery();
        $CallData = $db->query($CallSql);
        $CallRow = $db->fetchByAssoc($CallData);

        $returnArray = array(
            'Total emails' => $EmailRow['cnt_email'],
            'Total Opportunities' => $OppRow['cnt_opp'],
            'Overdue Tasks' => $OverduetaskRow['cnt_overduetask'],
            'Open Cases' => $OpencasesRow['cnt_opencase'],
            'Pending Cases' => $PendingcasesRow['cnt_pendingcase'],
            'PO Cases' => $POcasesRow['cnt_pocase'],
            'New Cases' => $NewcasesRow['cnt_newcase'],
            'Cases in Follow Up' => $casesInFollowupRow['cnt_followupcase'],
            'My Calls' => $CallRow['cnt_call'],
        );


        return $returnArray;
    }

    /**
     * To get Query
     * @return string $query
     * @author Reena Sattani
     */
    protected function getNumberOfEmailsQuery() {
        return "SELECT COUNT(id) AS cnt_email FROM emails WHERE ({$this->where}) AND deleted = 0 AND (date_entered >= DATE_SUB(NOW(),INTERVAL 3 HOUR))";
    }

    protected function getNumberOfOpportunitiesQuery() {
        return "SELECT COUNT(id) AS cnt_opp FROM opportunities WHERE ({$this->where}) AND deleted = 0 AND (date_entered BETWEEN CURDATE() - INTERVAL 14 DAY AND CURDATE())";
    }

    protected function getNumberOfOverdueTaskQuery() {
        return "SELECT COUNT(id) AS cnt_overduetask FROM tasks WHERE ({$this->where}) AND date_due < NOW() AND status != 'Completed' AND deleted = 0";
    }

    protected function getNumberOfOpenCasesQuery() {
        return "SELECT COUNT(id) AS cnt_opencase FROM cases WHERE ({$this->where}) AND status='Open' AND deleted = 0";
    }

    protected function getNumberOfPendingCasesQuery() {
        return "SELECT COUNT(id) AS cnt_pendingcase FROM cases WHERE ({$this->where}) AND (status='Pending Input' OR status='pending_customer' OR status='pending_supplier') AND deleted = 0";
    }

    protected function getNumberOfPOCasesQuery() {
        return "SELECT COUNT(id) AS cnt_pocase FROM cases WHERE ({$this->where}) AND status='PO' AND deleted = 0";
    }

    protected function getNumberOfNewCasesQuery() {
        return "SELECT COUNT(id) AS cnt_newcase FROM cases WHERE ({$this->where}) AND status='New' AND deleted = 0";
    }

    protected function getNumberOfCasesInFollowupQuery() {
        return "SELECT COUNT(id) AS cnt_followupcase FROM cases WHERE ({$this->where}) AND status='Followup' AND deleted = 0";
    }

    protected function getNumberOfCallsQuery() {
        return "SELECT COUNT(id) AS cnt_call FROM calls WHERE ({$this->where}) AND deleted = 0";
    }

}

?>