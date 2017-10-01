$(document).ready(function() {
    $('.nwjs_flash_message').each(function() {
        var $this = $(this);

        var message = $this.data('message');
        var config = $.extend(getDefaultFlashMessage(), $this.data());

        $.jGrowl(message, config);

        $this.remove();
    });

    $('.nwjs_imagebox').colorbox(getDefaultColorbox());
});

/**
 * Obecny prepinac trid
 * @param selector
 * @param target
 */
function classToggler(selector, target) {
    $(selector).on('click', function (event) {
        event.preventDefault();

        var element = null;

        if (null == target) {
            element = $(this);
        } else {
            element = $(target);
        }

        element.toggleClass('opened');
    });
}

/**
 * Vrati konfiguraci vychozi instance flash message.
 * @returns {object}
 */
function getDefaultFlashMessage() {
    return {
        position: 'center',
        closer: false,
        themeState: '',
        corners: ''
    };
}

/**
 * Vrati konfiguraci vychozi instance spinneru.
 *
 * @param {object} settings
 *
 * @returns {object}
 */
function getDefaultSpinner(settings) {
    return $.extend({
        color: ["#250e62", "#e4002b"],
        lines: 13,
        length: 0,
        width: 16,
        radius: 52,
        scale: 0.25,
        corners: 1.0,
        opacity: 0.15,
        rotate: 0,
        direction: 1,
        speed: 1,
        trail: 60,
        top: "50%",
        left: "50%"
    }, settings);
}

/**
 * Vrati konfiguraci vychozi instance colorboxu.
 * @returns {object}
 */
function getDefaultColorbox() {
    return {
        maxWidth: '95%',
        maxHeight: '90%',
        closeButton: true
    };
}

/**
 * Metoda, ktera zajistuje spravnou funkcnost matchMedia() napric prohlizeci
 * @see window.matchMedia()
 */
window.matchMedia = window.matchMedia || (function( doc, undefined ) {
    "use strict";

    var bool,
        docElem = doc.documentElement,
        refNode = docElem.firstElementChild || docElem.firstChild,
    // fakeBody required for <FF4 when executed in <head>
        fakeBody = doc.createElement( "body" ),
        div = doc.createElement( "div" );

    div.id = "mq-test-1";
    div.style.cssText = "position:absolute;top:-100em";
    fakeBody.style.background = "none";
    fakeBody.appendChild(div);

    return function(q){

        div.innerHTML = "&shy;<style media=\"" + q + "\"> #mq-test-1 { width: 42px; }</style>";

        docElem.insertBefore( fakeBody, refNode );
        bool = div.offsetWidth === 42;
        docElem.removeChild( fakeBody );

        return {
            matches: bool,
            media: q
        };

    };

}( document ));
