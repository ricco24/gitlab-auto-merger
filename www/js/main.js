$(function(){
    $('#confirm-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var title = button.data('modal-title');
        var body = button.data('modal-body');

        var btnNoText = button.data('modal-btn-no-text');
        var btnNoLink = button.data('modal-btn-no-link');
        var btnYesText = button.data('modal-btn-yes-text');
        var btnYesLink = button.data('modal-btn-yes-link');

        var modal = $(this)
        modal.find('.modal-title').html(title);
        modal.find('.modal-body').html(body);
        modal.find('#btn-no').text(btnNoText);
        modal.find('#btn-no').attr('href', btnNoLink);
        modal.find('#btn-yes').text(btnYesText);
        modal.find('#btn-yes').attr('href', btnYesLink);
    })
});
