<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('data/SugarBean.php');
require_once('include/utils.php');

class ForumTopic extends SugarBean {
	//stored fields
	var $id;
	var $deleted;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $created_by;
	var $name;
	var $table_name = "forumtopics";
	var $object_name = "ForumTopic";
	var $module_dir = 'ForumTopics';
	var $new_schema = true;
	
	// non-db fields
	var $can_delete;

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array();

	function ForumTopic() {
		parent::SugarBean();

		$this->disable_row_level_security = true;

	}

	function get_summary_text()
	{
		return "$this->name";
	}

	function bean_implements($interface){
		switch($interface){
			case 'ACL': return false;
		}
		return false;
	}

	function get_topics($add_blank=false)
	{
		$query = "SELECT name FROM $this->table_name where deleted=0 ";
		
		
		//Is Domains Installed
		global $current_user, $db;
		
		$DomainsQuery = $db->query("SELECT DISTINCT count(id_name) as count FROM upgrade_history WHERE id_name='AlineaSolDomains' AND status='installed'");
		$DomainsRow = $db->fetchByAssoc($DomainsQuery);
		
		if ($DomainsRow['count'] > 0) {
	
			if ((!$current_user->is_admin) || (($current_user->is_admin) && (!empty($current_user->asol_default_domain)))){
						
				require_once ('modules/asol_Domains/asol_Domains.php');
				$domainsBean = new asol_domains();
				$domainsBean->retrieve($current_user->asol_default_domain);
				
				if ($domainsBean->asol_domain_enabled) {
						
					$query .= " AND ( ($this->table_name.asol_domain_id='".$current_user->asol_default_domain."')";
					
					if ($current_user->asol_only_my_domain == 0) {
					
					
						//asol_domain_child_share_depth
						if (strtolower($e) != 'users') {
						
							$parentQuery = $db->query("SELECT DISTINCT asol_domains_id_c as parent_domain FROM asol_domains WHERE id = '".$current_user->asol_default_domain."'");
							$parentRow = $db->fetchByAssoc($parentQuery);
							$parentDomain = $parentRow['parent_domain'];
							$i=1;
							
							while (!empty($parentDomain)) {
							
								$query .= " OR (($this->table_name.asol_domain_id = '".$parentRow['parent_domain']."') AND ($this->table_name.asol_domain_child_share_depth >= $i) AND ($this->table_name.asol_published_domain = 1)) ";
								
								$parentQuery = $db->query("SELECT DISTINCT asol_domains_id_c as parent_domain FROM asol_domains WHERE id = '".$parentDomain."'");
								$parentRow = $db->fetchByAssoc($parentQuery);
								$parentDomain = $parentRow['parent_domain'];
								
								$i++;
							
							} 
					
						}
						
						//asol_domain_child_share_depth condition
						
						//asol_multi_create_domain 
						if (strtolower($e) != 'users')
							$query .= " OR (($this->table_name.asol_multi_create_domain LIKE '%;".$current_user->asol_default_domain.";%') AND ($this->table_name.asol_published_domain = 1)) ";
						//asol_multi_create_domain 
						
						
						//View hierarchy (any item above its hierarchy coul be seen)
						$childDomainsIds = $current_user->getChildDomains($current_user->asol_default_domain);
						$childDomainsStr = Array();
						foreach ($childDomainsIds as $key=>$domainId) {
							if (!$domainId['enabled'])
								array_splice($childDomainsIds, $key, 1);
							else
								$childDomainsStr[] = $domainId['id'];
						}
						$query .= (count($childDomainsIds) > 0) ? "OR ($this->table_name.asol_domain_id IN ('".implode("','", $childDomainsStr)."')) )" : ") " ;
		
					} else {
					
						$query .= ") ";
						
					}
					
				} else {
				
					$query .= " AND (1!=1) ";
					
				}
							
			
			}
			
		}
		//Is Domains Installed
		
		
		$query .= " order by list_order asc";
		$result = $GLOBALS['db']->query($query, false);
		$GLOBALS['log']->debug("get_topics");

		$list = array();
		if ($add_blank) {
			$list['']='';
		}
		while (($row = $this->db->fetchByAssoc($result)) != null) {
			$list[$row['name']] = $row['name'];
			$GLOBALS['log']->debug("row name is:".$row['name']);
		}
		return $list;
	}

	function &get_order($category_name)
	{
		$seed = new ForumTopic();
		$query = "SELECT list_order from forumtopics where name='".$GLOBALS['db']->quote($category_name)."' ";
		$result = $GLOBALS['db']->query($query, false);
		$row = $seed->db->fetchByAssoc($result);
		$GLOBALS['log']->debug("get_topics: result is ".$row);
		
		return $row['list_order'];
	}
	
	function create_list_query($order_by, $where, $show_deleted = 0)
	{
		$custom_join = $this->custom_fields->getJOIN();
                $query = "SELECT ";
               
                $query .= " $this->table_name.* ";
                if($custom_join){
   				$query .= $custom_join['select'];
 			}
                $query .= " FROM ".$this->table_name." ";
                if($custom_join){
  				$query .= $custom_join['join'];
			}
		$where_auto = '1=1';
				if($show_deleted == 0){
                	$where_auto = "$this->table_name.deleted=0";
				}else if($show_deleted == 1){
                	$where_auto = "$this->table_name.deleted=1";
				}

		if($where != "")
			$query .= "where ($where) AND ".$where_auto;
		else
			$query .= "where ".$where_auto;

		if(!empty($order_by))
			$query .= " ORDER BY $order_by";

		return $query;
	}

	function fill_in_additional_list_fields()
	{
		$this->fill_in_additional_detail_fields();
		
		$res = $GLOBALS['db']->query("select * from forums where category='".$this->name."' and deleted=0");
		$num_rows = $this->db->getRowCount($res);
		$this->can_delete = ($num_rows > 0 ? false : true);
		//echo $this->name." - can delete? - ".($this->can_delete ? "yes" : "no")."<BR>";
	}

	function fill_in_additional_detail_fields() {
		$res = $GLOBALS['db']->query("select * from forums where category='".$this->name."' and deleted=0");
		$num_rows = $this->db->getRowCount($res);
		$this->can_delete = ($num_rows > 0 ? false : true);
	}

	function get_list_view_data(){
		$temp_array = $this->get_list_view_array();
        $temp_array["ENCODED_NAME"]=$this->name;
//	$temp_array["ENCODED_NAME"]=htmlspecialchars($this->name, ENT_QUOTES);
    	return $temp_array;

	}
	/**
		builds a generic search based on the query string using or
		do not include any $this-> because this is called on without having the class instantiated
	*/
	function build_generic_where_clause ($the_query_string) {
	$where_clauses = Array();
	$the_query_string = PearDatabase::quote(from_html($the_query_string));
	array_push($where_clauses, "name like '$the_query_string%'");

	$the_where = "";
	foreach($where_clauses as $clause)
	{
		if($the_where != "") $the_where .= " or ";
		$the_where .= $clause;
	}


	return $the_where;
}

}
?>
