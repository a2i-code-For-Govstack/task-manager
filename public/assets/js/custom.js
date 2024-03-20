$(document).ready(function () {
    if ($('.select-select2').length > 0) {
        $('.select-select2').select2();
    }
});

$(document).ajaxComplete(function () {
    $('.select-select2').select2();
});

function pad(str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : str;
}

function getUsername(value) {
    var inputVal = value;
    var firstPart = inputVal.substr(0, 1);
    var username = pad(inputVal.substr(1, inputVal.length), 11);
    username = firstPart + username;
    return username;
}

$(document).on('blur', "input[name=username]", function () {
    if ($.isNumeric($(this).val())) {
        $(this).val(getUsername($(this).val()));
    }
})

function makeAreaResizable(element) {
    var resizeOpts = {
        handles: 'e, w'
    };

    $(`${element}`).css("overflow-x", 'hidden');
    $(`${element}`).resizable(resizeOpts);
    $(`${element}`).removeClass('ui-resizable');
    $(`${element}`).resize(function () {
        $(`${element}`).css("left", 'auto');
        $(`${element}`).css("overflow-x", 'hidden');
        $(`${element}`).css("min-width", '500px !important');
    });
}

function submitModalData(url, data, method, modal_id) {
    ajaxCallAsyncCallbackAPI(url, data, method, function (response) {
        if (response.status === 'success') {
            toastr.success('Success')
            $('#' + modal_id).modal('hide');
        } else {
            toastr.error(response.data.message)
            if (response.data.errors) {
                $.each(response.data.errors, function (k, v) {
                    if (isArray(v)) {
                        $.each(v, function (n, m) {
                            toastr.error(m)
                        })
                    } else {
                        if (v !== '') {
                            toastr.error(v);
                        }
                    }
                });
            }
            console.log(response.data)
        }
    })
}

function emptyModalData(modal_id) {
    $('#' + modal_id + ' :input').val('')
    $('#' + modal_id + ' select').val('').trigger('change')
    $('#' + modal_id + ' input[type=checkbox]').prop('checked', false);
}

function jsTreeInit(jstree_class = 'jstree-init') {
    $(`.${jstree_class}`).jstree({
        "core": {
            "themes": {
                "responsive": true
            }
        },
        "types": {
            "default": {
                "icon": "fal fa-folder"
            },
            "person": {
                "icon": "fal fa-file "
            }
        },
        "plugins": ["types", "checkbox",]
    });
}

function resetGuestSearchedArea() {
    $('.searched_users_area').html('');
    $('.searched_users_area_for_others_calendar').html('');
    $('#search_key').val('')
    $('#layer_id').val('').trigger('change')
}


function customScrollInit(elem, type) {
    if (type == 'class') {
        myElement = document.getElementsByClassName(elem)[0];
        topPos = myElement.scrollTop > 10 ? myElement.scrollTop : 10;
        myElement.scrollTop = topPos;
    }
}
