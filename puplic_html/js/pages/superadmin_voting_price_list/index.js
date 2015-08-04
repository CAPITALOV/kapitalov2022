$(document).ready(function () {
    $('.buttonDelete').click(function () {
        if (confirm('Подтвердите удаление')) {
            var $this = $(this);
            ajaxJson({
                url: '/votingPriceList/' + $this.data('id') + '/delete',
                success: function () {
                    $this.parent().parent().remove();
                    //alert('ok');
                }
            });
        }
    });
});