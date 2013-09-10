function removeFromFollowList(id, module) {
    $.ajax({
        url: 'index.php?module=Home' + '&action=remove_follow_list&record=' + id + '&module_name=' + module,
        type: "GET",
        success: function(data) {
            SUGAR.mySugar.retrieveDashlet($('#deshlate_id').val());
            return false;
        },
    });
}
var storeDirection = '';
function pagination(direction) {
    storeDirection = direction;
    $.ajax({
        url: 'index.php?module=Home' + '&action=pagination&direction=' + storeDirection + '&start=' + $('#start_pagination').val() + "&number_row=" + $('#pagination').val() + "&last_sort=" + $('#last_sort').val() + "&sort_direction=" + $('#last_sort_direction').val() + '&my_item=' + $('#my_item').val(),
        type: "POST",
        success: function(data) {
            var trLen = $('tr[id=oddListRowS1]').length;
            if (data != "") {
                for (var i = 0; i < trLen; i++) {
                    $('tr[id=oddListRowS1]')[0].remove();
                }
                $('#follow_up').append(data);
                if (storeDirection == 'next') {
                    $('#start_pagination').val(parseInt($('#start_pagination').val()) + parseInt($('#pagination').val()));
                    $('#Previous').removeAttr('disabled');
                    $('#img_prev').attr('src', 'themes/Sugar5/images/previous.gif');
                    $('#Start').removeAttr('disabled');
                    $('#img_first').attr('src', 'themes/Sugar5/images/start.gif');

                } else if (storeDirection == 'prev') {
                    $('#start_pagination').val(parseInt($('#start_pagination').val()) - parseInt($('#pagination').val()));
                } else if (storeDirection == 'end') {
                    var startPage = parseInt($('#total_record').val() / $('#pagination').val()) * $('#pagination').val();
                    $('#start_pagination').val(startPage);
                    $('#Previous').removeAttr('disabled');
                    $('#img_prev').attr('src', 'themes/Sugar5/images/previous.gif');
                    $('#Start').removeAttr('disabled');
                    $('#img_first').attr('src', 'themes/Sugar5/images/start.gif');
                } else if (storeDirection == 'start') {
                    $('#start_pagination').val(0);
                    $('#Previous').attr('disabled', 'disabled');
                    $('#img_prev').attr('src', 'themes/Sugar5/images/previous_off.gif');
                    $('#Start').attr('disabled', 'disabled');
                    $('#img_first').attr('src', 'themes/Sugar5/images/start_off.gif');
                }
                if ($('#start_pagination').val() == 0) {
                    $('#Previous').attr('disabled', 'disabled');
                    $('#img_prev').attr('src', 'themes/Sugar5/images/previous_off.gif');
                    $('#Start').attr('disabled', 'disabled');
                    $('#img_first').attr('src', 'themes/Sugar5/images/start_off.gif');
                }
                if ((parseInt($('#start_pagination').val()) + parseInt($('#start_pagination').val())) >= parseInt($('#total_record').val())) {
                    $('#Next').attr('disabled', 'disabled');
                    $('#img_next').attr('src', 'themes/Sugar5/images/next_off.gif');
                    $('#End').attr('disabled', 'disabled');
                    $('#img_end').attr('src', 'themes/Sugar5/images/end_off.gif');
                } else {
                    $('#Next').removeAttr('disabled');
                    $('#img_next').attr('src', 'themes/Sugar5/images/next.gif');
                    $('#End').removeAttr('disabled');
                    $('#img_end').attr('src', 'themes/Sugar5/images/end.gif');
                }
                var pageNumbers = 0;
                if ((parseInt($('#pagination').val()) + parseInt($('#start_pagination').val())) < parseInt($('#pagination').val()))
                    pageNumbers = '(' + (parseInt($('#start_pagination').val()) + 1) + '-' + $('#total_record').val() + ' of ' + $('#total_record').val() + ")";
                else {
                    if ((parseInt($('#start_pagination').val()) + parseInt($('#pagination').val())) < parseInt($('#total_record').val()))
                        pageNumbers = '(' + (parseInt($('#start_pagination').val()) + 1) + '-' + (parseInt($('#pagination').val()) + parseInt($('#start_pagination').val())) + ' of ' + $('#total_record').val() + ")";
                    else
                        pageNumbers = '(' + (parseInt($('#start_pagination').val()) + 1) + '-' + $('#total_record').val() + ' of ' + $('#total_record').val() + ")";
                }
                $('#pageNumbers').text(pageNumbers);
            }
        }
    });
}
var elementStored, storeColumn, storeOrdered;
function sortRow(column, ordered, currentElement) {
    elementStored = currentElement.id;
    storeColumn = column;
    storeOrdered = ordered;
    $.ajax({
        url: 'index.php?module=Home' + '&action=remove_sort_rows&column_by=' + column + '&ordered=' + ordered + "&numberOfRow=" + $('#pagination').val() + '&my_item=' + $('#my_item').val(),
        type: "GET",
        success: function(data) {
            $('#last_sort').val(storeColumn);
            $('#last_sort_direction').val(storeOrdered);
            if (column != "number") {
                $('#sort_image_number').attr('src', "themes/Sugar5/images/arrow.gif");
            }
            if (column != "name") {
                $('#sort_image_name').attr('src', "themes/Sugar5/images/arrow.gif");
            }
            if (column != "status") {
                $('#sort_image_status').attr('src', "themes/Sugar5/images/arrow.gif");
            }
            if (column != "user") {
                $('#sort_image_user').attr('src', "themes/Sugar5/images/arrow.gif");
            }
            var trLen = $('tr[id=oddListRowS1]').length;
            for (var i = 0; i < trLen; i++) {
                $('tr[id=oddListRowS1]')[0].remove();
            }
            var elemId = '#' + elementStored;
            var index = $(elemId).attr('onClick').indexOf('DSC')
            if (index == -1) {
                $(elemId).attr('onClick', $(elemId).attr('onClick').replace('ASC', 'DSC'));
                var sort_imageId = '#sort_image_' + column;
                $(sort_imageId).attr('src', "themes/Sugar5/images/arrow_down.gif");
            }
            else {
                $(elemId).attr('onClick', $(elemId).attr('onClick').replace('DSC', 'ASC'));
                var sort_imageId = '#sort_image_' + column;
                $(sort_imageId).attr('src', "themes/Sugar5/images/arrow_up.gif");
            }
            var pageNumbers = '';
            var starNumber = 0;
            if (parseInt($('#total_record').val()) != 0)
                starNumber = parseInt($('#total_record').val());
            if ((parseInt($('#pagination').val())) < parseInt($('#total_record').val()))
                pageNumbers = '(' + startNumber + '-' + $('#pagination').val() + ' of ' + $('#total_record').val() + ")";
            else
                pageNumbers = '(' + startNumber + '-' + $('#total_record').val() + ' of ' + $('#total_record').val() + ")";
            $('#pageNumbers').text(pageNumbers);
            $('#start_pagination').val('0');
            if ($('#start_pagination').val() == 0) {
                $('#Previous').attr('disabled', 'disabled');
                $('#img_prev').attr('src', 'themes/Sugar5/images/previous_off.gif');
                $('#Start').attr('disabled', 'disabled');
                $('#img_first').attr('src', 'themes/Sugar5/images/start_off.gif');
            }
            if ((parseInt($('#start_pagination').val()) + parseInt($('#start_pagination').val())) >= parseInt($('#total_record').val())) {
                $('#Next').attr('disabled', 'disabled');
                $('#img_next').attr('src', 'themes/Sugar5/images/next_off.gif');
                $('#End').attr('disabled', 'disabled');
                $('#img_end').attr('src', 'themes/Sugar5/images/end_off.gif');
            } else {
                $('#Next').removeAttr('disabled');
                $('#img_next').attr('src', 'themes/Sugar5/images/next.gif');
                $('#End').removeAttr('disabled');
                $('#img_end').attr('src', 'themes/Sugar5/images/end.gif');
            }
            if (parseInt($('#pagination').val()) >= parseInt($('#total_record').val())) {
                $('#Next').attr('disabled', 'disabled');
                $('#img_next').attr('src', 'themes/Sugar5/images/next_off.gif');
                $('#End').attr('disabled', 'disabled');
                $('#img_end').attr('src', 'themes/Sugar5/images/end_off.gif');
            }
            $('#follow_up').append(data);
        },
    });
}