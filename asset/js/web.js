$(document).ready(function () {
    // pridani tridy "mobile" elementu "body"
    bindIsMobile();

    // otevreni filtru na klik
    bindFilterOpener();

    // automaticke otevirani a zavirani filtru na mobilu
    bindFilterOpenerMobile();

    // na mobilu kliknuti do mapy zavre filtr
    bindClickTheMapInside();

    // otevreni footeru
    bindFooterOpener();

    // otevreni vyhledavani
    bindSearchOpener();

    // otevreni hlavniho menu (mobile hamburger)
    bindMainMenuOpener();

    // nastaveni odelsani hned po zmene
    bindFormAutoSubmit();

    // gmap
    window.googleMap = bindMap();

    // akce spojene s detailem
    bindDetail();

    // obecny popup s donacitanym obsahem
    bindPopup();

    // inicializace Nette ajaxu
    bindAjax();
}).keydown(function(e) {
    if (27 === e.keyCode) {
        togglePopup(false);
        return false;
    }
});

$(window).resize(function () {
    // pridani tridy "mobile" elementu "body"
    bindIsMobile();

    // na mobilu kliknuti do mapy zavre filtr
    bindClickTheMapInside();
});

/**
 * Otevreni filtru
 */
function bindFilterOpener() {
    var $filter = $('.nwjs_filter'),
        $detail = $('.nwjs_detail'),
        $search = $('.nwjs_search_opener').parent();

    $('.nwjs_filter_opener').on('click', function () {
        // na mobilu otevreni filtru zavre detail a vyhledavani
        if (isMobile() && !$filter.hasClass('opened')) {
            $detail.removeClass('opened');
            $search.removeClass('opened');
        }

        $filter.toggleClass('opened');
    });
}

/**
 * Automaticke otevirani a zavirani filtru na mobilu
 */
function bindFilterOpenerMobile() {
    var $filter = $('.nwjs_filter');

    if (isMobile() && $filter.hasClass('opened')) {
        $filter.removeClass('opened');
    } else {
        $filter.addClass('opened');
    }
}


/**
 * Na mobilu kliknuti do mapy zavre filtr
 */
function bindClickTheMapInside() {
    $('#map').on('click', function () {
        if (isMobile()) {
            $('.nwjs_filter').removeClass('opened');
        }
    });
}

/**
 * Otevreni footeru
 */
function bindFooterOpener() {
    // vyjeti paticky na click
    classToggler('.nwjs_footer_opener', '.nwjs_footer');

    // vyjeti paticky i na hover
    $('.nwjs_footer').hover(
        function() {
            $(this).addClass('opened');
        },
        function() {
            $(this).removeClass('opened');
        }
    );

    // po 3 s zajedou loga
    setTimeout(function () {
        $('.nwjs_footer').removeClass('half_opened');
    }, 3000);
}

/**
 * Otevreni vyhledavani a focus na input
 */
function bindSearchOpener() {
    $('.nwjs_search_opener').on('click', function () {
        var $parent = $(this).parent(),
            opened_class = 'opened',
            $autocomplete_container = $('.pac-container'),
            $filter = $('.nwjs_filter'),
            $detail = $('.nwjs_detail');

        if ($parent.hasClass(opened_class)) {
            $parent.removeClass(opened_class);
            $autocomplete_container.removeClass(opened_class);

        } else {
            $parent.addClass(opened_class);
            $autocomplete_container.addClass(opened_class);
            $('input', $parent).focus();

            // na mobilu otevreni vyhledavani zavre filtr a detail
            if (isMobile()) {
                $filter.removeClass(opened_class);
                $detail.removeClass(opened_class);
            }
        }
    });
}

/**
 * Otevreni hlavniho menu (mobile hamburger)
 */
function bindMainMenuOpener() {
    classToggler('.nwjs_main_menu_opener', '.nwjs_main_menu');
}

/**
 * Automaticke odeslani formulare po zmene
 */
function bindFormAutoSubmit() {
    $('#snippet--filter').on('change', '.nwjs_auto_submit', function () {
        $(this).submit();
    });
}

/**
 * Obluha mapy
 */
function bindMap() {
    var config = {
        item: $('#map'),
        map: mapConfig,
        markers: mapMarkers
    };

    return new Map(config);
}

/**
 * Obsluha detailu
 */
function bindDetail() {
    var $detail = $('#snippet--detail');

    if ($detail.length) {

        $detail.on('click', '.nwjs_description_opener', function () {
            $(this).toggleClass('opened');
        });

        $detail.on('click', '.nwjs_detail_closer', function () {
            $(this).closest('.nwjs_detail').removeClass('opened');
        });

        $detail.on('click', '.nwjs_imagebox', function() {
            $(this).colorbox(getDefaultColorbox());
        });
    }
}

/**
 * Otevreni popupu a nacteni jeho obsahu AJAXem
 * @param url
 * @param data
 */
function loadPopupContent(url, data) {
    var wrapper_class = 'nwjs_popup_wrapper',
        $wrapper = $('.' + wrapper_class),
        $content = $('.nwjs_popup_content');

    // inicializace
    $wrapper.removeClass('error');
    $content.html('');

    $($wrapper).spin(getDefaultSpinner());

    if (url.length > 0) {
        togglePopup(true);

        var config = {
            url: url,
            data: data,
            success: function (payload) {
                $content.html(payload);
            },
            error: function () {
                $wrapper.addClass('error');
            },
            complete: function () {
                $($wrapper).spin(false);
            }
        };

        $.nette.ajax(config);
    }
}

/**
 * Toggler popupu
 * @param open true otevre, false zavre
 */
function togglePopup(open) {
    var $body = $('body'),
        popup_opened_class = 'popup_opened';

    if (open) {
        $body.addClass(popup_opened_class);
    } else {
        $body.removeClass(popup_opened_class);
    }
}

/**
 * Inicializace ajaxu.
 */
function bindAjax() {
    $.nette.init();
    $.nette.ext({
        before: function (xhr, settings) {
            if (undefined !== settings.nette) {
                var $element = settings.nette.el;

                var spinnerSelector = $element.data('spinner');

                if (spinnerSelector) {
                    var $spinner = $(spinnerSelector);

                    if ($spinner.length) {
                        $($spinner).spin(getDefaultSpinner()).addClass('nwjs_spinner');
                    }
                }
            }
        },
        complete: function () {
            $('.nwjs_spinner').each(function () {
                $(this).spin(false).removeClass('nwjs_spinner');
            });
        }
    });
}

/**
 * Inicilizace popupu.
 */
function bindPopup() {
    var $popup = $('.nwjs_popup_wrapper');

    if ($popup.length) {
        // klik na okoli popupu zpusobi zavreni
        $popup.on('click', function(e) {
            if ($(e.target).hasClass('nwjs_popup_wrapper')) {
                togglePopup(false);
            }
        });

        $('.nwjs_popup_opener').on('click', function (e) {
            e.preventDefault();

            var url = $(this).attr('href');
            var data = {};

            loadPopupContent(url, data);
        });

        $('.nwjs_popup_close').on('click', function() {
            togglePopup(false);
        });
    }
}

/**
 * Oznaceni stranky na mobilnim viewportu
 */
function bindIsMobile() {
    if (isMobile()) {
        $('body').addClass('mobile');
    } else {
        $('body').removeClass('mobile');
    }
}

/**
 * Inicializace promenne, ktera udava, zda se jedna o mobilni zarizeni
 * Predava se retezec s kompletnim mediaQueries
 * Prevzato z NW6
 */
function isMobile() {
    return window.matchMedia('only screen and (max-width: 720px)').matches;
}
