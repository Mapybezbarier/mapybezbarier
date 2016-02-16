$(document).ready(function () {
    $.nette.init();

    bindConfirm();
    bindBlockLinks();
    bindDashboard();

    Tracy.Dumper.init();
});

/**
 * Naveseni confirm - typicky na odkazy na nevratne akce
 */
function bindConfirm() {
    $('.nwjs_confirm').on('click', function (e) {
        var question = $(this).data('confirm');

        if (question) {
            if (!confirm(question)) {
                e.preventDefault();
            }
        }
    });
}

/**
 * Naveseni blokovani vsech odkazu mimo vymezenou oblast
 * Pro upozorneni na ztratu dat pri opusteni editace
 */
function bindBlockLinks() {
    var $inner_block = $('.nwjs_block_outter_links');

    if ($inner_block.length > 0) {
        $('a:not([href^="mailto\\:"], .nwjs_block_outter_links a, #tracy-debug a)').on('click', function (e) {
            var question = $inner_block.data('confirm');

            if (question) {
                if (!confirm(question)) {
                    e.preventDefault();
                }
            }
        });
    }
}

/**
 * Naveseni otevirani/zavirani dasboardu
 */
function bindDashboard() {
    $('.nwjs_dashboard_opener').on('click', function () {
        $(this).toggleClass('active');
        $('body').toggleClass('dashboard_opened');
    });
    $('.nwjs_dashboard_overlay').click(function() {
        $(this).removeClass('active');
        $('body').removeClass('dashboard_opened');
    });
}
