function CompelteWfTasks(id) {
    $.ajax({
        url: 'index.php?module=bc_WorkFlowTasks&action=completeWfTask',
        type: "GET",
        data: {id: id},
        beforeSend: function() {
            ajaxStatus.showStatus('Completing Workflow Task...');
        },
        complete: function() {
            ajaxStatus.hideStatus();
        },
        success: function(result) {
            showSubPanel('bc_workflowtasks_cases', null, true, 'Cases');
        }

    });
}