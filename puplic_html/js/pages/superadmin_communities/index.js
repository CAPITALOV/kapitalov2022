$(document).ready(function () {
    $('.buttonDelete').click(function () {
        if (confirm('Подтвердите удаление')) {
            var $this = $(this);
            ajaxJson({
                url: '/communities/' + $this.data('id') + '/delete',
                success: function () {
                    $this.parent().parent().remove();
                    //alert('ok');
                }
            });
        }
    });
    $('#tableSort').tableDnD({
        onDragClass: 'myDragClass',
        onDrop: function (table, row) {
            var ids = [];
            $(table).find('tbody tr').each(function () {
                ids.push($(this).data('id'));
            });
            ajaxJson({
                url: '/communities/sort',
                type: 'post',
                data: {
                    ids: ids
                },
                success: function () {
                    //alert('Отсортировано успешно');
                }
            });
        }
    });
});