<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $theme_path;
global $image_path;
$GLOBALS['displayListView'] = true;

require_once('XTemplate/xtpl.php');
require_once("data/Tracker.php");
require_once('modules/Forums/Forum.php');
/*if (!defined('THEMEPATH'))
  define('THEMEPATH', $theme_path);
require_once(THEMEPATH.'layout_utils.php');*/

require_once('include/ListView/ListView.php');
require_once('modules/ForumTopics/ForumTopic.php');

if(!ACLController::checkAccess('Forums', 'list', true)){
    ACLController::displayNoAccess(false);
    sugar_cleanup(true);
}

global $app_strings;
global $app_list_strings;
global $current_language;
$current_module_strings = return_module_language($current_language, 'Forums');
echo get_form_header($current_module_strings['LBL_SEARCH_FORM_TITLE'], "", false);
$xtpl=new XTemplate ('modules/Forums/ForumsSearch.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);
$xtpl->assign("THEME", $theme);
$xtpl->assign("IMAGE_PATH", $image_path); $xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
$xtpl->assign("QUERY_STRING_USER", get_select_options_with_id(get_user_array(TRUE), ''));
$xtpl->parse("main");
$xtpl->out("main");

global $urlPrefix;

$where_clauses = Array();

global $currentModule;

if (!isset($where))
  $where = "";

$forumForQuery = new Forum();

global $current_user;

//BEGIN: everything used to seperate forums into categories

$forumListQuery = getForumListQuery($forumForQuery);
//echo $forumListQuery."<BR>";

$result_set = $GLOBALS['db']->query($forumListQuery);

//first we pull all the rows into $rowarr and then sort them by category_ranking
$rowarr = array();
while($row = $GLOBALS['db']->fetchByAssoc($result_set))
  $rowarr[] = $row;

// if this returns true, then there are no forums
if(count($rowarr) < 1)
{
  print("<BR>".$mod_strings['LBL_NO_FORUMS']);
}
else
{
  $team_where = "";

  usort($rowarr, "sortByCategory");
  
  // now we display per category
  foreach($rowarr as $row)
  {
    $where_backup = $where;
    array_push($where_clauses, "forums.category = '".$GLOBALS['db']->quote($row['category'])."'");
  	foreach($where_clauses as $clause)
  	{
  		if($where != "")
  		$where .= " and ";
  		$where .= $clause;
  	}

    $where .= $team_where;

  //BEGIN: standard list view procedure
    $ListView = new ListView();
  
    $ListView->initNewXTemplate('modules/Forums/ListView.html',$current_module_strings);
  
  
    //Is Domains Installed
	global $current_user, $db;

	$DomainsQuery = $db->query("SELECT DISTINCT count(id_name) as count FROM upgrade_history WHERE id_name='AlineaSolDomains' AND status='installed'");
	$DomainsRow = $db->fetchByAssoc($DomainsQuery);

	if ($DomainsRow['count'] > 0) {
		$domainName = " (".$row['name'].")";
	}
	//Is Domains Installed
  
  
    $ListView->setHeaderTitle($row['category'].$domainName);

    if(is_admin($current_user) && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])){
    	$ListView->setHeaderText("<a href='index.php?action=index&module=DynamicLayout&from_action=ListView&from_module=".$_REQUEST['module'] ."'>".get_image($image_path."EditLayout","border='0' alt='Edit Layout' align='bottom'")."</a>" );
    }
    $ListView->show_mass_update = false;
    $ListView->show_mass_update_form = false;
    $ListView->records_per_page = 10000;
    $ListView->show_paging = false;
    $ListView->show_export_button = false;
    $ListView->setQuery($where, "", "", "FORUM");
    $ListView->processListView($forumForQuery, "main", "FORUM");

	
  //END: standard list view procedure
  
    array_pop($where_clauses);  
    $where = $where_backup;
  
  }
  //END: everything used to seperate forums into categories
}


function getForumListQuery($bean)
{

  $forumListQuery = "select category from forums where deleted=0 ";
  
	//Is Domains Installed
	global $current_user, $db;

	$DomainsQuery = $db->query("SELECT DISTINCT count(id_name) as count FROM upgrade_history WHERE id_name='AlineaSolDomains' AND status='installed'");
	$DomainsRow = $db->fetchByAssoc($DomainsQuery);

	if ($DomainsRow['count'] > 0) {
	
		$forumListQuery = "SELECT asol_domains.name, forums.category FROM forums LEFT JOIN asol_domains ON forums.asol_domain_id=asol_domains.id where forums.deleted=0 ";

		if ((!$current_user->is_admin) || (($current_user->is_admin) && (!empty($current_user->asol_default_domain)))){
					
			require_once ('modules/asol_Domains/asol_Domains.php');
			$domainsBean = new asol_domains();
			$domainsBean->retrieve($current_user->asol_default_domain);
			
			if ($domainsBean->asol_domain_enabled) {
					
				$forumListQuery .= " AND ( (forums.asol_domain_id='".$current_user->asol_default_domain."')";
				
				if ($current_user->asol_only_my_domain == 0) {
				
				
					//asol_domain_child_share_depth
					if (strtolower($e) != 'users') {
					
						$parentQuery = $db->query("SELECT DISTINCT asol_domains_id_c as parent_domain FROM asol_domains WHERE id = '".$current_user->asol_default_domain."'");
						$parentRow = $db->fetchByAssoc($parentQuery);
						$parentDomain = $parentRow['parent_domain'];
						$i=1;
						
						while (!empty($parentDomain)) {
						
							$forumListQuery .= " OR ((forums.asol_domain_id = '".$parentRow['parent_domain']."') AND (forums.asol_domain_child_share_depth >= $i) AND (forums.asol_published_domain = 1)) ";
							
							$parentQuery = $db->query("SELECT DISTINCT asol_domains_id_c as parent_domain FROM asol_domains WHERE id = '".$parentDomain."'");
							$parentRow = $db->fetchByAssoc($parentQuery);
							$parentDomain = $parentRow['parent_domain'];
							
							$i++;
						
						} 
				
					}
					
					//asol_domain_child_share_depth condition
					
					//asol_multi_create_domain 
					if (strtolower($e) != 'users')
						$forumListQuery .= " OR ((forums.asol_multi_create_domain LIKE '%;".$current_user->asol_default_domain.";%') AND (forums.asol_published_domain = 1)) ";
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
					$forumListQuery .= (count($childDomainsIds) > 0) ? "OR (forums.asol_domain_id IN ('".implode("','", $childDomainsStr)."')) )" : ") " ;

				} else {
				
					$forumListQuery .= ") ";
					
				}
				
			} else {
			
				$forumListQuery .= " AND (1!=1) ";
				
			}
						
		
		}
		
	}
	//Is Domains Installed
  
  
  $forumListQuery .= "group by category ";
  
  return $forumListQuery;
}

function sortByCategory($a, $b)
{
  $a_rank = ForumTopic::get_order($a['category']);
  $b_rank = ForumTopic::get_order($b['category']);
  
  if($a_rank == $b_rank)
    return 0;
  
  return ($a_rank < $b_rank) ? -1 : 1;
}
?>
