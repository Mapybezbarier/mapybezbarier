$(document).ready(function () {
    bindRoleSelect();
    bindIcChange();
});

function bindRoleSelect() {
    $('#snippet-user-form').on('change', '#frm-user-form-role_id', function () {
        var $this = $(this);

        var config = {
            url: $this.data('set-role-url'),
            data: {
                'user-role': $this.val()
            }
        };

        $.nette.ajax(config);
    });
}

/**
 * Po doplneni IC se pokusi dohledat dalsi udaje z ARES
 */
function bindIcChange() {
    var $this = $('#frm-user-form-ic');
    $this.on('input',function() {
        var ic = $this.val().trim();

        if (ic.length == 8) {
            var config = {
                url: $this.data('get-ares-data'),
                data: {
                    'user-ic': ic
                },
                success: function (payload) {
                    if (payload.ic_title) {
                        $('#frm-user-form-ic_title').val(payload.ic_title);
                    }

                    if (payload.ic_place) {
                        $('#frm-user-form-ic_place').val(payload.ic_place);
                    }

                    if (payload.ic_form) {
                        $('#frm-user-form-ic_form').val(payload.ic_form);
                    }
                },
                error: function () {
                    // o chybe netreba informovat, nepredvyplni se
                }
            };

            $.nette.ajax(config);
        }
    });
}
