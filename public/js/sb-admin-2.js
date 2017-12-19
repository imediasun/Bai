$(function () {
    $('[data-translit-source]').bind('change click keyup', function (e) {
        var el = $(e.target),
            id = el.data('translitTarget');
        $.post(
            Routing.generate('admin_ajax_tools_translit'),
            {
                'text': el.val()
            },
            function (data) {
                $('#' + id).val(data);
            }
        );
    });

});
