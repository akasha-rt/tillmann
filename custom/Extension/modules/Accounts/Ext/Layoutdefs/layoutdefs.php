<?php

//Dhaval
//To customize all subpanel of Account for umbrella flow :)
$layout_defs['Accounts']['subpanel_setup']['activities']['collection_list']['meetings']['get_subpanel_data'] = 'function:getActivityQuery';
$layout_defs['Accounts']['subpanel_setup']['history']['collection_list']['tasks']['get_subpanel_data'] = 'function:getHistoryQuery';
$layout_defs['Accounts']['subpanel_setup']['documents']['get_subpanel_data'] = 'function:getDocumentQuery';
$layout_defs['Accounts']['subpanel_setup']['opportunities']['get_subpanel_data'] = 'function:getOpportunityQuery';
//$layout_defs['Accounts']['subpanel_setup']['campaigns']['get_subpanel_data'] = 'function:getCampaignQuery';
$layout_defs['Accounts']['subpanel_setup']['leads']['get_subpanel_data'] = 'function:getLeadQuery';
$layout_defs['Accounts']['subpanel_setup']['bugs']['get_subpanel_data'] = 'function:getBugQuery';
$layout_defs['Accounts']['subpanel_setup']['cases']['get_subpanel_data'] = 'function:getCaseQuery';
$layout_defs['Accounts']['subpanel_setup']['project']['get_subpanel_data'] = 'function:getProjectQuery';
?>