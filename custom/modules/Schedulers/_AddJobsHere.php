<?php

/**
 * Custom Scheduler File
 * @author Dhaval darji
 */
if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

//Add the new job type to the Option in the job dropdown in scheduler
$job_strings[] = 'createOppFromCase';

//Function to call when the new job is called from cronjob
function createOppFromCase() {
    $GLOBALS['log']->debug('Custom Scheduler : Starting createOppFromCase');
    require_once('modules/Opportunities/Opportunity.php');
    $op = new Opportunity();

    global $db;
    $case_list = $db->query("SELECT
                              c.id,
                              c.name,
                              c.description,
                              c.assigned_user_id,
                              c.case_number,
                              c_c.contact_id
                            FROM cases c
                              LEFT JOIN contacts_cases c_c
                                ON c.id = c_c.case_id
                            WHERE DATE_ADD(c.date_modified,INTERVAL 14 DAY) <= NOW()
                                AND c.status = 'Closed'
                                AND c.convertedtoopp = '0'
                                AND c.deleted = 0
                                AND c_c.deleted = 0");
    while ($case = $db->fetchByAssoc($case_list)) {
        //$op->id = create_guid();  //if id is set save() will update the record if not then will insert new row
        $op->name = $case['name'];
        $op->description = $case['description'];
        $op->assigned_user_id = $case['assigned_user_id'];
        $op->deleted = 0;
        $op->date_entered = date('%Y-%m-%d H:i:s');
        $op->date_modified = date('%Y-%m-%d H:i:s');
        $op->save();

        $op->Updateconvertedtoopp($case['id']);
        $op->set_opportunity_contact_relationship($case['contact_id']);
    }

    //Return true to notify the successfull execution of the job
    return true;
}

?>
