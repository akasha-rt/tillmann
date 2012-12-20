<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
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
 ********************************************************************************/




/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 9/29/11
 * Time: 11:55 AM
 * To change this template use File | Settings | File Templates.
 */

class CalendarUtils {

	/**
	 * Find first day of week according to user's settings
	 * @param SugarDateTime $date 
	 * @return SugarDateTime $date
	 */
	static function get_first_day_of_week(SugarDateTime $date){
		$fdow = $GLOBALS['current_user']->get_first_day_of_week();
		if($date->day_of_week < $fdow)
				$date = $date->get('-7 days');			
		return $date->get_day_by_index_this_week($fdow);
	}
	
	
	/**
	 * Get list of needed fields for modules
	 * @return array
	 */
	static function get_fields(){
		return array(
			'Meetings' => array(
				'name',
				'date_start',
				'duration_hours',
				'duration_minutes',
				'status',
				'description',
				'parent_type',
				'parent_name',
				'parent_id',
			),
			'Calls' => array(
				'name',
				'date_start',
				'duration_hours',
				'duration_minutes',
				'status',
				'description',
				'parent_type',
				'parent_name',
				'parent_id',
			),
			'Tasks' => array(
				'name',
				'date_start',
				'date_due',
				'status',
				'description',
				'parent_type',
				'parent_name',
				'parent_id',
			),
		);
	}
	
	/**
	 * Get array of needed time data
	 * @param SugarBean $bean 
	 * @return array
	 */
	static function get_time_data($bean){
					$arr = array();
					
					$date_field = "date_start";								
					if($bean->object_name == 'Task')
						$date_field = "date_due";
					
					$timestamp = SugarDateTime::createFromFormat($GLOBALS['timedate']->get_date_time_format(),$bean->$date_field,new DateTimeZone('UTC'))->format('U');				
					$arr['timestamp'] = $timestamp;
					$arr['time_start'] = $GLOBALS['timedate']->fromTimestamp($arr['timestamp'])->format($GLOBALS['timedate']->get_time_format());

					
					return $arr;
	}
        
        /**
     * Build array of datetimes for recurring meetings
     * @param string $date_start
     * @param array $params
     * @return array
     */
    static function build_repeat_sequence($date_start, $params) {

        $arr = array();

        $type = $params['type'];
        $interval = intval($params['interval']);
        if ($interval < 1)
            $interval = 1;

        if (!empty($params['count'])) {
            $count = $params['count'];
            if ($count < 1)
                $count = 1;
        }else
            $count = 0;

        if (!empty($params['until'])) {
            $until = $params['until'];
        }else
            $until = $date_start;

        if ($type == "Weekly") {
            $dow = $params['dow'];
            if ($dow == "") {
                return array();
            }
        }

        /**
         * @var SugarDateTime $start Recurrence start date.
         */
        $start = SugarDateTime::createFromFormat($GLOBALS['timedate']->get_date_time_format(), $date_start);
        /**
         * @var SugarDateTime $end Recurrence end date. Used if recurrence ends by date.
         */
        if (!empty($params['until'])) {
            $end = SugarDateTime::createFromFormat($GLOBALS['timedate']->get_date_format(), $until);
            $end->modify("+1 Day");
        } else {
            $end = $start;
        }
        $current = clone $start;

        $i = 1; // skip the first iteration
        $w = $interval; // for week iteration
        $last_dow = $start->format("w");

        $limit = SugarConfig::getInstance()->get('calendar.max_repeat_count', 1000);

        while ($i < $count || ($count == 0 && $current->format("U") < $end->format("U"))) {
            $skip = false;
            switch ($type) {
                case "Daily":
                    $current->modify("+{$interval} Days");
                    break;
                case "Weekly":
                    $day_index = $last_dow;
                    for ($d = $last_dow + 1; $d <= $last_dow + 7; $d++) {
                        $day_index = $d % 7;
                        if (strpos($dow, (string) ($day_index)) !== false) {
                            break;
                        }
                    }
                    $step = $day_index - $last_dow;
                    $last_dow = $day_index;
                    if ($step <= 0) {
                        $step += 7;
                        $w++;
                    }
                    if ($w % $interval != 0)
                        $skip = true;

                    $current->modify("+{$step} Days");
                    break;
                case "Monthly":
                    $current->modify("+{$interval} Months");
                    break;
                case "Yearly":
                    $current->modify("+{$interval} Years");
                    break;
                default:
                    return array();
            }

            if ($skip)
                continue;

            if (($i < $count || $count == 0 && $current->format("U") < $end->format("U"))) {
                $arr[] = $current->format($GLOBALS['timedate']->get_date_time_format());
            }
            $i++;

            if ($i > $limit + 100)
                break;
        }
        return $arr;
    }
}
