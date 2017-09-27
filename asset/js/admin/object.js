var isCheckingAddress2 = false;
var isCheckingAddress3 = false;

$(document).ready(function () {
    monkeyPatchAutocomplete();

    bindObjectAutocomplete();
    bindRegionAutocomplete();
    bindAddress1Autocomplete();
    bindAddress2Autocomplete();
    bindAddress3Autocomplete();
    bindCustomObjectType();
    bindImage();
    bindObjectTabs();
    bindGpsPicker();
    bindAddAttachementOpener();
    bindTabIndexes();
    bindTabValidation();
    bindAddressToggler();
    bindBackWithNotices();
    bindOpenNotifications();
    bindAutosave();
});

function bindCustomObjectType() {
    var $form = $('.nwjs_object_form');
    var $select = $form.find('input[name="objectType"]');
    var $input = $form.find('input[name="objectTypeCustom"]');
    var $wrapper = $input.closest('.form_pair');

    if ($input.data('id') != $form.find('input[name="objectType"]:checked').val()) {
        $wrapper.hide();
    }

    $select.on('change', function() {
        var $this = $(this);

        $input.val(null);
        $wrapper.toggle($input.data('id') == $this.val());
    });
}

function bindImage()
{
    var $form = $('.nwjs_object_form');
    var $input = $form.find('input[name="image"]');
    var $submit = $form.find('input[name="save"]');

    $input.on('change', function() {
        $submit.click();
    });
}

function bindObjectAutocomplete() {
    var $form = $('.nwjs_object_form');

    $('.nwjs_autocomplete').each(function () {
        var $this = $(this);
        var $target = $('#' + $this.data('target'));

        $this.on('input', function() {
            $target.val(null);
        });

        $this.on('blur', function() {
            $target.blur();
            $form.change();
        });

        $this.autocomplete({
            source: $this.data('source'),
            minLength: 3,
            select: function(event, ui) {
                if (undefined != ui.item) {
                    $target.val(ui.item.id);
                }

                $this.blur();
            },
            open: function() {
                $(".ui-autocomplete").width($this.outerWidth());
            }
        });
    });
}

function monkeyPatchAutocomplete() {
    $.ui.autocomplete.prototype._renderItem = function (ul, item) {
        // Escape any regex syntax inside this.term
        var cleanTerm = this.term.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');

        // Build pipe separated string of terms to highlight
        var keywords = $.trim(cleanTerm).replace('  ', ' ').split(' ').join('|');

        // Get the new label text to use with matched terms wrapped
        // in a span tag with a class to do the highlighting
        var re = new RegExp("(" + keywords + ")", "gi");
        var label = '' + item.label;
        var output = label.replace(re, '<span class="ui-menu-item-highlight">$1</span>');

        return $("<li>").append($("<a>").html(output)).appendTo(ul);
    };
}

/**
 * Predvyplneni regionu z hodnot u ostatnich objektu
 */
function bindRegionAutocomplete() {
    var $this = $('#frm-object-form-region');

    $this.autocomplete({
        source: $this.data('autocomplete'),
        open: function() {
            $(".ui-autocomplete").width($this.outerWidth());
        }
    });
}

function bindAddress1Autocomplete() {
    var $form = $('.nwjs_object_form');
    var $this = $('#frm-object-form-helpAddress1');
    var $zipcode = $('#frm-object-form-zipcode');
    var $city = $('#frm-object-form-city');
    var $cityPart = $('#frm-object-form-cityPart');

    $this.on('focus', function() {
        hideGpsPicker();
        resetAddress1();
    });

    $this.on('blur', function() {
        $.each([$zipcode, $city, $cityPart], function() {
            $(this).blur();
        });

        $form.change();
    });

    $this.autocomplete({
        source: $this.data('source'),
        minLength: 3,
        select: function(event, ui) {
            if (undefined !== ui.item) {
                if (typeof ui.item.zipcode !== 'object') {
                    $zipcode.val(ui.item.zipcode);
                }

                if (typeof ui.item.city !== 'object') {
                    $city.val(ui.item.city);
                }

                if (typeof ui.item.city_part !== 'object') {
                    $cityPart.val(ui.item.city_part);
                }

                $zipcode.data('autocomplete', ui.item.zipcode);
                $city.data('autocomplete', ui.item.city);
                $cityPart.data('autocomplete', ui.item.city_part);

                bindAddress2Autocomplete();
                bindAddress3Autocomplete();
            }

            $this.blur();
        },
        open: function() {
            $(".ui-autocomplete").width($this.outerWidth());
        }
    });
}

function checkAddress2Readonly($zipcode, $city, $cityPart) {
    if (false === isCheckingAddress2) {
        isCheckingAddress2 = true;

        var $this = $('#frm-object-form-helpAddress2');

        if (
            $zipcode.data('autocomplete')
            && $city.data('autocomplete')
            && $cityPart.data('autocomplete')
        ) {
            $.ajax({
                url: $this.data('check'),
                data: {
                    zipcode: $zipcode.data('autocomplete'),
                    city: $city.data('autocomplete'),
                    cityPart: $cityPart.data('autocomplete')
                },
                success: function (payload) {
                    $this.val(payload.message);
                    $this.attr('readonly', payload.hasStreet ? null : 'readonly');
                },
                complete: function() {
                    isCheckingAddress2 = false;
                }
            });
        } else {
            isCheckingAddress2 = false;

            $this.attr('readonly', 'readonly');
        }
    }

    checkAddress3Readonly($zipcode, $city, $cityPart);
}

function bindAddress2Autocomplete() {
    var $form = $('.nwjs_object_form');
    var $this = $('#frm-object-form-helpAddress2');
    var $previous = $('#frm-object-form-helpAddress1');
    var $street = $('#frm-object-form-street');
    var $zipcode = $('#frm-object-form-zipcode');
    var $city = $('#frm-object-form-city');
    var $cityPart = $('#frm-object-form-cityPart');

    $this.on('focus', function() {
        resetAddress2();
        checkAddress2Readonly($zipcode, $city, $cityPart);
    });

    $this.on('blur', function() {
        $.each([$street, $zipcode, $city, $cityPart], function() {
            $(this).blur();
        });

        $form.change();
    });

    $this.autocomplete({
        source: $this.data('source') + '&' + $.param({
            zipcode: $zipcode.data('autocomplete'),
            city: $city.data('autocomplete'),
            cityPart: $cityPart.data('autocomplete')
        }),
        minLength: 2,
        select: function(event, ui) {
            if (undefined !== ui.item) {
                if (ui.item.previous) {
                    $previous.val(ui.item.previous);
                }

                $zipcode.val(ui.item.zipcode);
                $city.val(ui.item.city);
                $cityPart.val(ui.item.city_part);
                $street.val(ui.item.street);

                bindAddress3Autocomplete();
            }

            $this.blur();
        },
        open: function() {
            $(".ui-autocomplete").width($this.outerWidth());
        }
    });

    checkAddress2Readonly($zipcode, $city, $cityPart);
}

function checkAddress3Readonly($zipcode, $city, $cityPart) {
    var address3 = $('#frm-object-form-helpAddress3');

    // $street nekontroluji, nektere obce nemaji ulice
    if (
        $zipcode.data('autocomplete')
        && $city.data('autocomplete')
        && $cityPart.data('autocomplete')
    ) {
        address3.attr('readonly', null);
    } else {
        address3.attr('readonly', 'readonly');
    }
}

function bindAddress3Autocomplete() {
    var $form = $('.nwjs_object_form');
    var $this = $('#frm-object-form-helpAddress3');

    var $streetDescNo = $('#frm-object-form-streetDescNo');
    var $streetNoIsAlternative = $("input[name='streetNoIsAlternative']");
    var $streetOrientNo = $('#frm-object-form-streetOrientNo');
    var $streetOrientSymbol = $('#frm-object-form-streetOrientSymbol');
    var $ruianAddress = $('#frm-object-form-ruianAddress');

    var $zipcode = $('#frm-object-form-zipcode');
    var $city = $('#frm-object-form-city');
    var $cityPart = $('#frm-object-form-cityPart');
    var $street = $('#frm-object-form-street');

    $this.on('focus', function() {
        resetAddress3();
        checkAddress3Readonly($zipcode, $city, $cityPart);
    });

    $this.on('blur', function() {
        $.each([$street, $zipcode, $city, $cityPart, $streetDescNo, $streetNoIsAlternative, $streetOrientNo, $streetOrientSymbol], function() {
            $(this).blur();
        });

        $form.change();
    });

    $this.autocomplete({
        source: $this.data('source') + '&' + $.param({
            zipcode: $zipcode.val(),
            city: $city.val(),
            cityPart: $cityPart.val(),
            street: $street.val()
        }),
        select: function(event, ui) {
            if (undefined !== ui.item) {
                $streetDescNo.val(ui.item.street_desc_no);

                if (ui.item.street_no_is_alternative) {
                    $('input[name=streetNoIsAlternative][value=1]').prop('checked', true);
                }

                $streetOrientNo.val(ui.item.street_orient_no);
                $streetOrientSymbol.val(ui.item.street_orient_symbol);
                $ruianAddress.val(ui.item.id);
            }

            $this.blur();
        },
        open: function() {
            $(".ui-autocomplete").width($this.outerWidth());
        }
    });

    checkAddress3Readonly($zipcode, $city, $cityPart);
}

function resetAddress1() {
    $('#frm-object-form-helpAddress1').val(null);
    $('#frm-object-form-zipcode').val(null).data('autocomplete', null);
    $('#frm-object-form-city').val(null).data('autocomplete', null);
    $('#frm-object-form-cityPart').val(null).data('autocomplete', null);
    resetAddress2();
}

function resetAddress2() {
    $('#frm-object-form-helpAddress2').val(null);
    $('#frm-object-form-street').val(null);
    resetAddress3();
}

function resetAddress3() {
    $('#frm-object-form-helpAddress3').val(null);
    $('#frm-object-form-streetDescNo').val(null);
    $('input[name=streetNoIsAlternative][value=0]').prop('checked', true);
    $('#frm-object-form-streetNoIsAlternative').val(null);
    $('#frm-object-form-streetOrientNo').val(null);
    $('#frm-object-form-streetOrientSymbol').val(null);
    $('#frm-object-form-ruianAddress').val(null);
}

function bindObjectTabs() {
    $('.nwjs_tab_opener').on('click', function (e) {
        e.preventDefault();

        var $this = $(this);
        var $tab = $this.closest('.nwjs_tab');
        var $content = $('#' + $this.data('tab'));

        $('.nwjs_tab').not($tab).removeClass('active');
        $('.nwjs_tab_content').not($content).removeClass('opened');

        $tab.addClass('active');
        $content.addClass('opened');
    });
}

function bindGpsPicker() {
    $('#gpspicker_opener').on('click', function(e) {
        e.preventDefault();

        var $holder = $('#gpspicker');

        $holder.locationpicker({
            location: {latitude: 49.5, longitude: 14.9},
            radius: 0,
            zoom: 7,
            inputBinding: {
                latitudeInput: $('#frm-object-form-latitude'),
                longitudeInput: $('#frm-object-form-longitude')
            },
            enableAutocomplete: true
        });
        $holder.show();
        $holder.locationpicker('autosize');

        resetAddress1();
    });
}

function hideGpsPicker() {
    var $holder = $('#gpspicker');
    $holder.hide();
    $('#frm-object-form-latitude').val(null);
    $('#frm-object-form-longitude').val(null);
}

function bindAddAttachementOpener() {
    classToggler('.nwjs_aa_opener', '.nwjs_add_attachements');
}

function bindTabIndexes() {
    $(":input:visible").each(function (i) {
        var $this = $(this);

        if (!$this.hasClass('hidden')) {
            $this.attr('tabindex', i + 1);
        }
    });
}

function bindTabValidation() {
    var $form = $('.nwjs_object_form');

    var validate = function() {
        var $tabs =  $('.nwjs_tab');

        $tabs.each(function() {
            var $tab = $(this);

            var valid = true;

            $('#' + $tab.find('.nwjs_tab_opener').data('tab')).find(':input').each(function() {
                var $input = $(this);

                if ($input.hasClass('has-error')) {
                    valid = false;
                }
            });

            if (valid) {
                $tab.removeClass('has-error');
            } else {
                $tab.addClass('has-error');
            }
        });
    };

    $form.on('change', function() {
        validate();
    });

    $form.on('submit', function () {
        Nette.validateForm($form.get(0));

        validate();

        if ($('.nwjs_tab').hasClass('has-error')) {
            $("body, html").scrollTop(0);
        }
    });
}

function bindAddressToggler() {
    $('.nwjs_address_toggler').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);

        $('.city_block,.street_block,.house_number_block,.gps_block,.gps_picker_block').removeClass('has_address');

        $this.closest('.open_button_wrapper').hide();
    });
}

function bindBackWithNotices() {
    $('.nwjs_back_with_notices').click(function() {
        $('.nwjs_float_notification').hide();
    });
}

function bindOpenNotifications() {
    $('.nwjs_notification_opener').click(function() {
        $('.nwjs_form_notification_wrapper').toggleClass('opened');
    });
}

function bindAutosave() {
    var $form = $('.nwjs_object_form');
    var timer = null;
    var interval = 5 * 60 * 1000;

    var handler = function() {
        var payload = {};

        $.each($form.serializeArray(), function(index, item) {
           payload[item['name']] = item['value'];
        });

        delete payload['do'];

        $.nette.ajax({
            url: $form.data('autosave'),
            method: 'post',
            data: payload
        });

        clearInterval(timer);
        timer = setInterval(handler, interval);
    };

    if ($form.length) {
        timer = setInterval(handler, interval);
    }
}
