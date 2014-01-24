(function($) {
    //redirect when locale change
    $('#display_cms_menu_locale').on('change', function() {
        window.location.href = location.href.replace(/new.*/, 'new/' + $(this).val());
    });


    //menu item recording
    var menus = $('#menus'),
        pages = $('#pages'),
        input = $('.pages')
    ;

    //nestable to handle nested list sorting
    menus.nestable();
    pages.nestable({maxDepth: 1});

    //set the hidden value on list change
    menus.on('change', function() {
        input.val(JSON.stringify(menus.nestable('serialize')));
    });
}(jQuery))