$(function () {
    $('#removalModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var routePath = button.attr('href');
      $(this).find('.modal-footer form').attr('action', routePath);
    });
    $('#revokeModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var routePath = button.attr('href');
      $(this).find('.modal-content form').attr('action', routePath);
    });
    $('.custom-datepicker').datepicker({
      showOn: 'both',
      buttonText: '<i class="far fa-calendar"></i>',
    });
    $('.custom-datepicker').datepicker('option', $.datepicker.regional["pt-BR"]);
});