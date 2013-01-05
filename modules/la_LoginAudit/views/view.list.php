<?php

require_once('include/MVC/View/views/view.list.php');

class la_LoginAuditViewList extends ViewList {
    /* function listViewProcess() {
      $this->processSearchForm();
      $this->lv->searchColumns = $this->searchForm->searchColumns;

      if (!$this->headers)
      return;
      if (empty($_REQUEST['search_form_only']) || $_REQUEST['search_form_only'] == false) {
      $this->lv->ss->assign("SEARCH", true);
      $this->lv->setup($this->seed, 'modules/la_LoginAudit/metadata/ListView.tpl', $this->where, $this->params);
      $savedSearchName = empty($_REQUEST['saved_search_select_name']) ? '' : (' - ' . $_REQUEST['saved_search_select_name']);
      echo $this->lv->display();
      }
      } */

    public function listViewPrepare() {
        $this->options['show_title'] = false;
        $this->lv->showMassupdateFields = false;
        $this->lv->delete = false;
        $this->lv->quickViewLinks = false;
        parent::listViewPrepare();
    }

}

?>
