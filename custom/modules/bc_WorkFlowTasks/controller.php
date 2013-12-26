<?PHP

require_once('include/MVC/Controller/SugarController.php');

class bc_WorkFlowTasksController extends SugarController {

    function action_completeWfTask() {
        $id = $_REQUEST['id'];
        $this->bean->retrieve($id);
        $this->bean->status = 'Completed';
        $this->bean->Save(false);
        exit;
    }

}

?>