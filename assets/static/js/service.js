var dt;
var dt1;
var groupnow;
var allgroups = 1;

let choices = document.querySelectorAll(".choices")
let initChoice
for (let i = 0; i < choices.length; i++) {
    if (choices[i].classList.contains("multiple-remove")) {
        initChoice = new Choices(choices[i], {
            delimiter: ",",
            editItems: true,
            maxItemCount: -1,
            removeItemButton: true,
        })
    } else {
        initChoice = new Choices(choices[i], {
            noResultsText: TRAN_SELECTNORESULTS,
            noChoicesText: TRAN_SELECTNOOPTIONS,
            itemSelectText: TRAN_SELECTPRESSSELECT,
        })
    }
}

const inlineBox = document.getElementById('inline');
const SelInlineBox = new Choices(inlineBox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const musBox = document.getElementById('imptemplate_mus');
const SelmusBox = new Choices(musBox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const tfcBox = document.getElementById('imptemplate');
const SeltfcBox = new Choices(tfcBox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const afterdayBox = document.getElementById('daysaftertype');
const SelafterdaysBox = new Choices(afterdayBox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

$('#selectGroup').on('change', function (e) {

    if ($('#selectGroup').val() == 'allgroups') {
        allgroups = 1;
        dt1.ajax.reload();
    } else {
        allgroups = 0;
        groupnow = $('#selectGroup').val();
        dt1.ajax.reload();
    }
});

function removeAuto(cart) {

    Swal.fire({
        text: TRAN_REMSELAUTOFILLCART,
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: TRAN_YES,
        cancelButtonText: TRAN_NO,
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-active-light"
        }
    }).then(function (result) {
        if (result.value) {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/remautofill.php',
                data: {
                    id: cart
                },
                datatype: 'html',
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    var kod = mydata.errorcode;
                    if (fel == "false") {
                        dt.ajax.reload();

                    } else {
                        Swal.fire({
                            text: TRAN_BUG,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                }
            });

        }
    });

}

function addcarttofill(cart, service) {
    jQuery.ajax({
        type: "POST",
        async: false,
        url: HOST_URL + '/forms/addautofill.php',
        data: {
            cart: cart,
            service: service
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            dt.ajax.reload();
            $('#cart_select').modal('hide');
        }
    });
}

$("#bypass").change(function () {
    var bypass = this.value;
    if (bypass == 'Y') {
        SelInlineBox.disable();
        if ($("#imptemplate_mus").val() == 'cust') {
            $("#mustransof").removeAttr('disabled');
            $("#mustranslength").removeAttr('disabled');
            $("#mustimeof").removeAttr('disabled');
            $("#mustimelength").removeAttr('disabled');
        }
    } else {
        SelInlineBox.enable();
        $("#mustransof").prop("disabled", true);
        $("#mustranslength").prop("disabled", true);
        $("#mustimeof").prop("disabled", true);
        $("#mustimelength").prop("disabled", true);
    }
});

$("#imptemplate_mus").change(function () {
    var template = this.value;
    if (template != 'cust') {
        getTemplate(template, 2);
    } else {
        getCustom(SERVICE_NAME, 2);
    }
});

$("#imptemplate").change(function () {
    var template = this.value;
    if (template != 'cust') {
        getTemplate(template, 1);
    } else {
        getCustom(SERVICE_NAME, 1);
    }
});

$('#autodelete').click(function () {

    if ($("#autodelete").is(":checked")) {

        $("#autodeletedays").removeAttr('disabled');
        SelafterdaysBox.enable();
    } else {
        $('#autodeletedays').prop("disabled", true);
        SelafterdaysBox.disable();
    }

});

$('#purgeelr').click(function () {

    if ($("#purgeelr").is(":checked")) {

        $("#elrdays").removeAttr('disabled');
    } else {
        $('#elrdays').prop("disabled", true);
    }

});

function copyCustom(service, type) {
    var template;
    if (type == 1) {
        template = $('#imptemplate').val();
    } else {
        template = $('#imptemplate_mus').val();
    }
    Swal.fire({
        text: TRAN_COPYTHISCUSTOM,
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: TRAN_YES,
        cancelButtonText: TRAN_NO,
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-active-light"
        }
    }).then(function (result) {
        if (result.value) {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/copycustom.php',
                data: {
                    id: service,
                    type: type,
                    template: template,
                },
                datatype: 'html',
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    var kod = mydata.errorcode;
                    if (fel == "false") {
                        if (type == 1) {
                            SeltfcBox.removeActiveItems();
                            SeltfcBox.setChoiceByValue('cust');
                            getCustom(SERVICE_NAME, 1);

                        } else {
                            SelmusBox.removeActiveItems();
                            SelmusBox.setChoiceByValue('cust');
                            getCustom(SERVICE_NAME, 2);
                        }


                    } else {
                        Swal.fire({
                            text: TRAN_BUG,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                }
            });

        }
    });
}

function getCustom(service, type) {

    $.ajax({
        url: HOST_URL + '/forms/servicecustom.php',
        data: "id=" + service,
        dataType: 'json',
        success: function (data) {
            if (type == '1') {
                $("#tfccartof").removeAttr('disabled');
                $("#tfccartlength").removeAttr('disabled');
                $("#tfctitof").removeAttr('disabled');
                $("#tfctitlength").removeAttr('disabled');
                $("#tfchourof").removeAttr('disabled');
                $("#tfchourslength").removeAttr('disabled');
                $("#tfcminof").removeAttr('disabled');
                $("#tfcminlength").removeAttr('disabled');
                $("#tfcsecof").removeAttr('disabled');
                $("#tfcseclength").removeAttr('disabled');
                $("#tfclenhoof").removeAttr('disabled');
                $("#tfcleholength").removeAttr('disabled');
                $("#tfclenminof").removeAttr('disabled');
                $("#tfcleminlength").removeAttr('disabled');
                $("#tfclensecof").removeAttr('disabled');
                $("#tfcleseclength").removeAttr('disabled');
                $("#tfcdataof").removeAttr('disabled');
                $("#tfcdatalength").removeAttr('disabled');
                $("#tfceventof").removeAttr('disabled');
                $("#tfceventlength").removeAttr('disabled');
                $("#tfcanncof").removeAttr('disabled');
                $("#tfcannclength").removeAttr('disabled');
                if (data['TFC_CART_OFFSET'] != null) {
                    $('#tfccartof').val(data['TFC_CART_OFFSET']);
                } else {
                    $('#tfccartof').val('0');
                }
                if (data['TFC_CART_LENGTH'] != null) {
                    $('#tfccartlength').val(data['TFC_CART_LENGTH']);
                } else {
                    $('#tfccartlength').val('0');
                }
                if (data['TFC_TITLE_OFFSET'] != null) {
                    $('#tfctitof').val(data['TFC_TITLE_OFFSET']);
                } else {
                    $('#tfctitof').val('0');
                }
                if (data['TFC_TITLE_LENGTH'] != null) {
                    $('#tfctitlength').val(data['TFC_TITLE_LENGTH']);
                } else {
                    $('#tfctitlength').val('0');
                }
                if (data['TFC_HOURS_OFFSET'] != null) {
                    $('#tfchourof').val(data['TFC_HOURS_OFFSET']);
                } else {
                    $('#tfchourof').val('0');
                }
                if (data['TFC_HOURS_LENGTH'] != null) {
                    $('#tfchourslength').val(data['TFC_HOURS_LENGTH']);
                } else {
                    $('#tfchourslength').val('0');
                }
                if (data['TFC_MINUTES_OFFSET'] != null) {
                    $('#tfcminof').val(data['TFC_MINUTES_OFFSET']);
                } else {
                    $('#tfcminof').val('0');
                }
                if (data['TFC_MINUTES_LENGTH'] != null) {
                    $('#tfcminlength').val(data['TFC_MINUTES_LENGTH']);
                } else {
                    $('#tfcminlength').val('0');
                }
                if (data['TFC_SECONDS_OFFSET'] != null) {
                    $('#tfcsecof').val(data['TFC_SECONDS_OFFSET']);
                } else {
                    $('#tfcsecof').val('0');
                }
                if (data['TFC_SECONDS_LENGTH'] != null) {
                    $('#tfcseclength').val(data['TFC_SECONDS_LENGTH']);
                } else {
                    $('#tfcseclength').val('0');
                }
                if (data['TFC_LEN_HOURS_OFFSET'] != null) {
                    $('#tfclenhoof').val(data['TFC_LEN_HOURS_OFFSET']);
                } else {
                    $('#tfclenhoof').val('0');
                }
                if (data['TFC_LEN_HOURS_LENGTH'] != null) {
                    $('#tfcleholength').val(data['TFC_LEN_HOURS_LENGTH']);
                } else {
                    $('#tfcleholength').val('0');
                }
                if (data['TFC_LEN_MINUTES_OFFSET'] != null) {
                    $('#tfclenminof').val(data['TFC_LEN_MINUTES_OFFSET']);
                } else {
                    $('#tfclenminof').val('0');
                }
                if (data['TFC_LEN_MINUTES_LENGTH'] != null) {
                    $('#tfcleminlength').val(data['TFC_LEN_MINUTES_LENGTH']);
                } else {
                    $('#tfcleminlength').val('0');
                }
                if (data['TFC_LEN_SECONDS_OFFSET'] != null) {
                    $('#tfclensecof').val(data['TFC_LEN_SECONDS_OFFSET']);
                } else {
                    $('#tfclensecof').val('0');
                }
                if (data['TFC_LEN_SECONDS_LENGTH'] != null) {
                    $('#tfcleseclength').val(data['TFC_LEN_SECONDS_LENGTH']);
                } else {
                    $('#tfcleseclength').val('0');
                }
                if (data['TFC_DATA_OFFSET'] != null) {
                    $('#tfcdataof').val(data['TFC_DATA_OFFSET']);
                } else {
                    $('#tfcdataof').val('0');
                }
                if (data['TFC_DATA_LENGTH'] != null) {
                    $('#tfcdatalength').val(data['TFC_DATA_LENGTH']);
                } else {
                    $('#tfcdatalength').val('0');
                }
                if (data['TFC_EVENT_ID_OFFSET'] != null) {
                    $('#tfceventof').val(data['TFC_EVENT_ID_OFFSET']);
                } else {
                    $('#tfceventof').val('0');
                }
                if (data['TFC_EVENT_ID_LENGTH'] != null) {
                    $('#tfceventlength').val(data['TFC_EVENT_ID_LENGTH']);
                } else {
                    $('#tfceventlength').val('0');
                }
                if (data['TFC_ANNC_TYPE_OFFSET'] != null) {
                    $('#tfcanncof').val(data['TFC_ANNC_TYPE_OFFSET']);
                } else {
                    $('#tfcanncof').val('0');
                }
                if (data['TFC_ANNC_TYPE_LENGTH'] != null) {
                    $('#tfcannclength').val(data['TFC_ANNC_TYPE_LENGTH']);
                } else {
                    $('#tfcannclength').val('0');
                }
            } else {

                $("#muscartof").removeAttr('disabled');
                $("#muscartlength").removeAttr('disabled');
                $("#mustitof").removeAttr('disabled');
                $("#mustitlength").removeAttr('disabled');
                $("#mushourof").removeAttr('disabled');
                $("#mushourslength").removeAttr('disabled');
                $("#musminof").removeAttr('disabled');
                $("#musminlength").removeAttr('disabled');
                $("#mussecof").removeAttr('disabled');
                $("#musseclength").removeAttr('disabled');
                $("#muslenhoof").removeAttr('disabled');
                $("#musleholength").removeAttr('disabled');
                $("#muslenminof").removeAttr('disabled');
                $("#musleminlength").removeAttr('disabled');
                $("#muslensecof").removeAttr('disabled');
                $("#musleseclength").removeAttr('disabled');
                $("#musdataof").removeAttr('disabled');
                $("#musdatalength").removeAttr('disabled');
                $("#museventof").removeAttr('disabled');
                $("#museventlength").removeAttr('disabled');
                $("#musanncof").removeAttr('disabled');
                $("#musannclength").removeAttr('disabled');
                if (data['MUS_CART_OFFSET'] != null) {
                    $('#muscartof').val(data['MUS_CART_OFFSET']);
                } else {
                    $('#muscartof').val('0');
                }
                if (data['MUS_CART_LENGTH'] != null) {
                    $('#muscartlength').val(data['MUS_CART_LENGTH']);
                } else {
                    $('#muscartlength').val('0');
                }
                if (data['MUS_TITLE_OFFSET'] != null) {
                    $('#mustitof').val(data['MUS_TITLE_OFFSET']);
                } else {
                    $('#mustitof').val('0');
                }
                if (data['MUS_TITLE_LENGTH'] != null) {
                    $('#mustitlength').val(data['MUS_TITLE_LENGTH']);
                } else {
                    $('#mustitlength').val('0');
                }
                if (data['MUS_HOURS_OFFSET'] != null) {
                    $('#mushourof').val(data['MUS_HOURS_OFFSET']);
                } else {
                    $('#mushourof').val('0');
                }
                if (data['MUS_HOURS_LENGTH'] != null) {
                    $('#mushourslength').val(data['MUS_HOURS_LENGTH']);
                } else {
                    $('#mushourslength').val('0');
                }
                if (data['MUS_MINUTES_OFFSET'] != null) {
                    $('#musminof').val(data['MUS_MINUTES_OFFSET']);
                } else {
                    $('#musminof').val('0');
                }
                if (data['MUS_MINUTES_LENGTH'] != null) {
                    $('#musminlength').val(data['MUS_MINUTES_LENGTH']);
                } else {
                    $('#musminlength').val('0');
                }
                if (data['MUS_SECONDS_OFFSET'] != null) {
                    $('#mussecof').val(data['MUS_SECONDS_OFFSET']);
                } else {
                    $('#mussecof').val('0');
                }
                if (data['MUS_SECONDS_LENGTH'] != null) {
                    $('#musseclength').val(data['MUS_SECONDS_LENGTH']);
                } else {
                    $('#musseclength').val('0');
                }
                if (data['MUS_LEN_HOURS_OFFSET'] != null) {
                    $('#muslenhoof').val(data['MUS_LEN_HOURS_OFFSET']);
                } else {
                    $('#muslenhoof').val('0');
                }
                if (data['MUS_LEN_HOURS_LENGTH'] != null) {
                    $('#musleholength').val(data['MUS_LEN_HOURS_LENGTH']);
                } else {
                    $('#musleholength').val('0');
                }
                if (data['MUS_LEN_MINUTES_OFFSET'] != null) {
                    $('#muslenminof').val(data['MUS_LEN_MINUTES_OFFSET']);
                } else {
                    $('#muslenminof').val('0');
                }
                if (data['MUS_LEN_MINUTES_LENGTH'] != null) {
                    $('#musleminlength').val(data['MUS_LEN_MINUTES_LENGTH']);
                } else {
                    $('#musleminlength').val('0');
                }
                if (data['MUS_LEN_SECONDS_OFFSET'] != null) {
                    $('#muslensecof').val(data['MUS_LEN_SECONDS_OFFSET']);
                } else {
                    $('#muslensecof').val('0');
                }
                if (data['MUS_LEN_SECONDS_LENGTH'] != null) {
                    $('#musleseclength').val(data['MUS_LEN_SECONDS_LENGTH']);
                } else {
                    $('#musleseclength').val('0');
                }
                if (data['MUS_DATA_OFFSET'] != null) {
                    $('#musdataof').val(data['MUS_DATA_OFFSET']);
                } else {
                    $('#musdataof').val('0');
                }
                if (data['MUS_DATA_LENGTH'] != null) {
                    $('#musdatalength').val(data['MUS_DATA_LENGTH']);
                } else {
                    $('#musdatalength').val('0');
                }
                if (data['MUS_EVENT_ID_OFFSET'] != null) {
                    $('#museventof').val(data['MUS_EVENT_ID_OFFSET']);
                } else {
                    $('#museventof').val('0');
                }
                if (data['MUS_EVENT_ID_LENGTH'] != null) {
                    $('#museventlength').val(data['MUS_EVENT_ID_LENGTH']);
                } else {
                    $('#museventlength').val('0');
                }
                if (data['MUS_ANNC_TYPE_OFFSET'] != null) {
                    $('#musanncof').val(data['MUS_ANNC_TYPE_OFFSET']);
                } else {
                    $('#musanncof').val('0');
                }
                if (data['MUS_ANNC_TYPE_LENGTH'] != null) {
                    $('#musannclength').val(data['MUS_ANNC_TYPE_LENGTH']);
                } else {
                    $('#musannclength').val('0');
                }
                if (data['MUS_TRANS_TYPE_OFFSET'] != null) {
                    $('#mustransof').val(data['MUS_TRANS_TYPE_OFFSET']);
                } else {
                    $('#mustransof').val('0');
                }
                if (data['MUS_TRANS_TYPE_LENGTH'] != null) {
                    $('#mustranslength').val(data['MUS_TRANS_TYPE_LENGTH']);
                } else {
                    $('#mustranslength').val('0');
                }
                if (data['MUS_TIME_TYPE_OFFSET'] != null) {
                    $('#mustimeof').val(data['MUS_TIME_TYPE_OFFSET']);
                } else {
                    $('#mustimeof').val('0');
                }
                if (data['MUS_TIME_TYPE_LENGTH'] != null) {
                    $('#mustimelength').val(data['MUS_TIME_TYPE_LENGTH']);
                } else {
                    $('#mustimelength').val('0');
                }

            }

        }
    });

}

function getTemplate(temp, type) {
    $.ajax({
        url: HOST_URL + '/forms/servicetemp.php',
        data: "id=" + temp,
        dataType: 'json',
        success: function (data) {
            if (type == '1') {
                $("#tfccartof").prop("disabled", true);
                $("#tfccartlength").prop("disabled", true);
                $("#tfctitof").prop("disabled", true);
                $("#tfctitlength").prop("disabled", true);
                $("#tfchourof").prop("disabled", true);
                $("#tfchourslength").prop("disabled", true);
                $("#tfcminof").prop("disabled", true);
                $("#tfcminlength").prop("disabled", true);
                $("#tfcsecof").prop("disabled", true);
                $("#tfcseclength").prop("disabled", true);
                $("#tfclenhoof").prop("disabled", true);
                $("#tfcleholength").prop("disabled", true);
                $("#tfclenminof").prop("disabled", true);
                $("#tfcleminlength").prop("disabled", true);
                $("#tfclensecof").prop("disabled", true);
                $("#tfcleseclength").prop("disabled", true);
                $("#tfcdataof").prop("disabled", true);
                $("#tfcdatalength").prop("disabled", true);
                $("#tfceventof").prop("disabled", true);
                $("#tfceventlength").prop("disabled", true);
                $("#tfcanncof").prop("disabled", true);
                $("#tfcannclength").prop("disabled", true);

                if (data['CART_OFFSET'] != null) {
                    $('#tfccartof').val(data['CART_OFFSET']);
                } else {
                    $('#tfccartof').val('0');
                }
                if (data['CART_LENGTH'] != null) {
                    $('#tfccartlength').val(data['CART_LENGTH']);
                } else {
                    $('#tfccartlength').val('0');
                }
                if (data['TITLE_OFFSET'] != null) {
                    $('#tfctitof').val(data['TITLE_OFFSET']);
                } else {
                    $('#tfctitof').val('0');
                }
                if (data['TITLE_LENGTH'] != null) {
                    $('#tfctitlength').val(data['TITLE_LENGTH']);
                } else {
                    $('#tfctitlength').val('0');
                }
                if (data['HOURS_OFFSET'] != null) {
                    $('#tfchourof').val(data['HOURS_OFFSET']);
                } else {
                    $('#tfchourof').val('0');
                }
                if (data['HOURS_LENGTH'] != null) {
                    $('#tfchourslength').val(data['HOURS_LENGTH']);
                } else {
                    $('#tfchourslength').val('0');
                }
                if (data['MINUTES_OFFSET'] != null) {
                    $('#tfcminof').val(data['MINUTES_OFFSET']);
                } else {
                    $('#tfcminof').val('0');
                }
                if (data['MINUTES_LENGTH'] != null) {
                    $('#tfcminlength').val(data['MINUTES_LENGTH']);
                } else {
                    $('#tfcminlength').val('0');
                }
                if (data['SECONDS_OFFSET'] != null) {
                    $('#tfcsecof').val(data['SECONDS_OFFSET']);
                } else {
                    $('#tfcsecof').val('0');
                }
                if (data['SECONDS_LENGTH'] != null) {
                    $('#tfcseclength').val(data['SECONDS_LENGTH']);
                } else {
                    $('#tfcseclength').val('0');
                }
                if (data['LEN_HOURS_OFFSET'] != null) {
                    $('#tfclenhoof').val(data['LEN_HOURS_OFFSET']);
                } else {
                    $('#tfclenhoof').val('0');
                }
                if (data['LEN_HOURS_LENGTH'] != null) {
                    $('#tfcleholength').val(data['LEN_HOURS_LENGTH']);
                } else {
                    $('#tfcleholength').val('0');
                }
                if (data['LEN_MINUTES_OFFSET'] != null) {
                    $('#tfclenminof').val(data['LEN_MINUTES_OFFSET']);
                } else {
                    $('#tfclenminof').val('0');
                }
                if (data['LEN_MINUTES_LENGTH'] != null) {
                    $('#tfcleminlength').val(data['LEN_MINUTES_LENGTH']);
                } else {
                    $('#tfcleminlength').val('0');
                }
                if (data['LEN_SECONDS_OFFSET'] != null) {
                    $('#tfclensecof').val(data['LEN_SECONDS_OFFSET']);
                } else {
                    $('#tfclensecof').val('0');
                }
                if (data['LEN_SECONDS_LENGTH'] != null) {
                    $('#tfcleseclength').val(data['LEN_SECONDS_LENGTH']);
                } else {
                    $('#tfcleseclength').val('0');
                }
                if (data['DATA_OFFSET'] != null) {
                    $('#tfcdataof').val(data['DATA_OFFSET']);
                } else {
                    $('#tfcdataof').val('0');
                }
                if (data['DATA_LENGTH'] != null) {
                    $('#tfcdatalength').val(data['DATA_LENGTH']);
                } else {
                    $('#tfcdatalength').val('0');
                }
                if (data['EVENT_ID_OFFSET'] != null) {
                    $('#tfceventof').val(data['EVENT_ID_OFFSET']);
                } else {
                    $('#tfceventof').val('0');
                }
                if (data['EVENT_ID_LENGTH'] != null) {
                    $('#tfceventlength').val(data['EVENT_ID_LENGTH']);
                } else {
                    $('#tfceventlength').val('0');
                }
                if (data['ANNC_TYPE_OFFSET'] != null) {
                    $('#tfcanncof').val(data['ANNC_TYPE_OFFSET']);
                } else {
                    $('#tfcanncof').val('0');
                }
                if (data['ANNC_TYPE_LENGTH'] != null) {
                    $('#tfcannclength').val(data['ANNC_TYPE_LENGTH']);
                } else {
                    $('#tfcannclength').val('0');
                }
            } else {
                $("#muscartof").prop("disabled", true);
                $("#muscartlength").prop("disabled", true);
                $("#mustitof").prop("disabled", true);
                $("#mustitlength").prop("disabled", true);
                $("#mushourof").prop("disabled", true);
                $("#mushourslength").prop("disabled", true);
                $("#musminof").prop("disabled", true);
                $("#musminlength").prop("disabled", true);
                $("#mussecof").prop("disabled", true);
                $("#musseclength").prop("disabled", true);
                $("#muslenhoof").prop("disabled", true);
                $("#musleholength").prop("disabled", true);
                $("#muslenminof").prop("disabled", true);
                $("#musleminlength").prop("disabled", true);
                $("#muslensecof").prop("disabled", true);
                $("#musleseclength").prop("disabled", true);
                $("#musdataof").prop("disabled", true);
                $("#musdatalength").prop("disabled", true);
                $("#museventof").prop("disabled", true);
                $("#museventlength").prop("disabled", true);
                $("#musanncof").prop("disabled", true);
                $("#musannclength").prop("disabled", true);
                if (data['CART_OFFSET'] != null) {
                    $('#muscartof').val(data['CART_OFFSET']);
                } else {
                    $('#muscartof').val('0');
                }
                if (data['CART_LENGTH'] != null) {
                    $('#muscartlength').val(data['CART_LENGTH']);
                } else {
                    $('#muscartlength').val('0');
                }
                if (data['TITLE_OFFSET'] != null) {
                    $('#mustitof').val(data['TITLE_OFFSET']);
                } else {
                    $('#mustitof').val('0');
                }
                if (data['TITLE_LENGTH'] != null) {
                    $('#mustitlength').val(data['TITLE_LENGTH']);
                } else {
                    $('#mustitlength').val('0');
                }
                if (data['HOURS_OFFSET'] != null) {
                    $('#mushourof').val(data['HOURS_OFFSET']);
                } else {
                    $('#mushourof').val('0');
                }
                if (data['HOURS_LENGTH'] != null) {
                    $('#mushourslength').val(data['HOURS_LENGTH']);
                } else {
                    $('#mushourslength').val('0');
                }
                if (data['MINUTES_OFFSET'] != null) {
                    $('#musminof').val(data['MINUTES_OFFSET']);
                } else {
                    $('#musminof').val('0');
                }
                if (data['MINUTES_LENGTH'] != null) {
                    $('#musminlength').val(data['MINUTES_LENGTH']);
                } else {
                    $('#musminlength').val('0');
                }
                if (data['SECONDS_OFFSET'] != null) {
                    $('#mussecof').val(data['SECONDS_OFFSET']);
                } else {
                    $('#mussecof').val('0');
                }
                if (data['SECONDS_LENGTH'] != null) {
                    $('#musseclength').val(data['SECONDS_LENGTH']);
                } else {
                    $('#musseclength').val('0');
                }
                if (data['LEN_HOURS_OFFSET'] != null) {
                    $('#muslenhoof').val(data['LEN_HOURS_OFFSET']);
                } else {
                    $('#muslenhoof').val('0');
                }
                if (data['LEN_HOURS_LENGTH'] != null) {
                    $('#musleholength').val(data['LEN_HOURS_LENGTH']);
                } else {
                    $('#musleholength').val('0');
                }
                if (data['LEN_MINUTES_OFFSET'] != null) {
                    $('#muslenminof').val(data['LEN_MINUTES_OFFSET']);
                } else {
                    $('#muslenminof').val('0');
                }
                if (data['LEN_MINUTES_LENGTH'] != null) {
                    $('#musleminlength').val(data['LEN_MINUTES_LENGTH']);
                } else {
                    $('#musleminlength').val('0');
                }
                if (data['LEN_SECONDS_OFFSET'] != null) {
                    $('#muslensecof').val(data['LEN_SECONDS_OFFSET']);
                } else {
                    $('#muslensecof').val('0');
                }
                if (data['LEN_SECONDS_LENGTH'] != null) {
                    $('#musleseclength').val(data['LEN_SECONDS_LENGTH']);
                } else {
                    $('#musleseclength').val('0');
                }
                if (data['DATA_OFFSET'] != null) {
                    $('#musdataof').val(data['DATA_OFFSET']);
                } else {
                    $('#musdataof').val('0');
                }
                if (data['DATA_LENGTH'] != null) {
                    $('#musdatalength').val(data['DATA_LENGTH']);
                } else {
                    $('#musdatalength').val('0');
                }
                if (data['EVENT_ID_OFFSET'] != null) {
                    $('#museventof').val(data['EVENT_ID_OFFSET']);
                } else {
                    $('#museventof').val('0');
                }
                if (data['EVENT_ID_LENGTH'] != null) {
                    $('#museventlength').val(data['EVENT_ID_LENGTH']);
                } else {
                    $('#museventlength').val('0');
                }
                if (data['ANNC_TYPE_OFFSET'] != null) {
                    $('#musanncof').val(data['ANNC_TYPE_OFFSET']);
                } else {
                    $('#musanncof').val('0');
                }
                if (data['ANNC_TYPE_LENGTH'] != null) {
                    $('#musannclength').val(data['ANNC_TYPE_LENGTH']);
                } else {
                    $('#musannclength').val('0');
                }
                if (data['TRANS_TYPE_OFFSET'] != null) {
                    $('#mustransof').val(data['TRANS_TYPE_OFFSET']);
                } else {
                    $('#mustransof').val('0');
                }
                if (data['TRANS_TYPE_LENGTH'] != null) {
                    $('#mustranslength').val(data['TRANS_TYPE_LENGTH']);
                } else {
                    $('#mustranslength').val('0');
                }
                if (data['TIME_TYPE_OFFSET'] != null) {
                    $('#mustimeof').val(data['TIME_TYPE_OFFSET']);
                } else {
                    $('#mustimeof').val('0');
                }
                if (data['TIME_TYPE_LENGTH'] != null) {
                    $('#mustimelength').val(data['TIME_TYPE_LENGTH']);
                } else {
                    $('#mustimelength').val('0');
                }

            }
        }
    });
}

function getTimeFromMillis(millis) {

    var minutes = '' + Math.floor(millis / 60000);
    millis = millis % 60000;

    while (minutes.length < 2)
        minutes = '0' + minutes;

    var seconds = '' + Math.floor(millis / 1000);
    if (seconds < 0)
        seconds = seconds * -1;

    while (seconds.length < 2)
        seconds = '0' + seconds;

    millis = millis % 1000;

    var tenths = Math.floor(millis / 100);

    if (tenths < 0)
        tenths = tenths * -1;

    return minutes + ':' + seconds + '.' + tenths;

}

$('#service_form').validate({
    rules: {
        lognametemp: {
            required: true,
        },
        logdesctemp: {
            required: true,
        },
    },
    messages: {
        lognametemp: {
            required: TRAN_NOTBEEMPTY
        },
        logdesctemp: {
            required: TRAN_NOTBEEMPTY
        },
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('parsley-error');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },
    submitHandler: function () {
        var dataString = $('#service_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/updateservice.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    Swal.fire({
                        text: TRAN_SERVICESAVED,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: TRAN_OK,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                } else {
                    Swal.fire({
                        text: TRAN_BUG,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: TRAN_OK,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            }
        });
    }
});

dt1 = $("#addtofill_table").DataTable({
    searchDelay: 500,
    processing: true,
    ordering: true,
    autoWidth: false,
    order: [
        [2, 'desc']
    ],
    stateSave: true,
    serverMethod: 'post',
    ajax: {
        url: HOST_URL + "/tables/service-autolibrarytable.php",
        data: function (d) {
            d.all = allgroups;
            d.groups = groupnow;
        }
    },
    language: {
        "emptyTable": TRAN_TABLENODATA,
        "info": TRAN_TABLESHOWS + " _START_ " + TRAN_TABLETO + " _END_ " + TRAN_TABLETOTAL + " _TOTAL_ " + TRAN_TABLEROWS,
        "infoEmpty": TRAN_TABLESHOWS + " 0 " + TRAN_TABLETO + " 0 " + TRAN_TABLETOTAL + " 0 " + TRAN_TABLEROWS,
        "infoFiltered": "(" + TRAN_TABLEFILTERED + " _MAX_ " + TRAN_TABLEROWS + ")",
        "infoThousands": " ",
        "lengthMenu": TRAN_TABLESHOW + " _MENU_ " + TRAN_TABLEROWS,
        "loadingRecords": TRAN_TABLELOADING,
        "processing": TRAN_TABLEWORKING,
        "search": TRAN_TABLESEARCH,
        "zeroRecords": TRAN_TABLENORESULTS,
        "thousands": " ",
        "paginate": {
            "first": TRAN_TABLEFIRST,
            "last": TRAN_TABLELAST,
            "next": TRAN_TABLENEXT,
            "previous": TRAN_TABLEPREV
        },
        "select": {
            "rows": {
                "1": "1 " + TRAN_TABLESELECTED,
                "_": "%d " + TRAN_TABLESELECTED
            }
        },
        "aria": {
            "sortAscending": ": " + TRAN_TABLENSORTRISE,
            "sortDescending": ": " + TRAN_TABLENSORTFALL
        }
    },
    columns: [
        {
            data: 'NUMBER'
        },
        {
            data: 'GROUP_NAME'
        },
        {
            data: 'AVERAGE_LENGTH'
        },
        {
            data: 'TITLE'
        },
        {
            data: 'ARTIST'
        },
        {
            data: null
        },
    ],
    columnDefs: [

        {
            targets: 1,
            render: function (data, type, row) {
                return '<P style="color:' + row.COLOR + '">' + data + '</p>';
            }
        },

        {
            targets: 2,
            render: function (data, type, row) {
                return getTimeFromMillis(data);
            }
        },


        {
            targets: -1,
            data: null,
            orderable: false,
            className: 'text-end',
            render: function (data, type, row) {
                return `
                    <a href="javascript:;" onclick="addcarttofill('`+ row.NUMBER + `', '` + SERVICE_NAME + `')" class="btn icon btn-info"><i class="bi bi-plus-square"></i></a>`;
            }
        },
    ],
});



dt = $("#autofill_table").DataTable({
    processing: true,
    scrollY: "500px",
    scrollCollapse: true,
    paging: false,
    dom: "<'table-responsive'tr>",
    ordering: true,
    order: [
        [0, 'desc']
    ],
    stateSave: true,
    ajax: {
        url: HOST_URL + "/tables/autofill-table.php",
        data: function (d) {
            d.service = SERVICE_NAME;
        }
    },
    language: {
        "emptyTable": TRAN_TABLENODATA,
        "info": TRAN_TABLESHOWS + " _START_ " + TRAN_TABLETO + " _END_ " + TRAN_TABLETOTAL + " _TOTAL_ " + TRAN_TABLEROWS,
        "infoEmpty": TRAN_TABLESHOWS + " 0 " + TRAN_TABLETO + " 0 " + TRAN_TABLETOTAL + " 0 " + TRAN_TABLEROWS,
        "infoFiltered": "(" + TRAN_TABLEFILTERED + " _MAX_ " + TRAN_TABLEROWS + ")",
        "infoThousands": " ",
        "lengthMenu": TRAN_TABLESHOW + " _MENU_ " + TRAN_TABLEROWS,
        "loadingRecords": TRAN_TABLELOADING,
        "processing": TRAN_TABLEWORKING,
        "search": TRAN_TABLESEARCH,
        "zeroRecords": TRAN_TABLENORESULTS,
        "thousands": " ",
        "paginate": {
            "first": TRAN_TABLEFIRST,
            "last": TRAN_TABLELAST,
            "next": TRAN_TABLENEXT,
            "previous": TRAN_TABLEPREV
        },
        "select": {
            "rows": {
                "1": "1 " + TRAN_TABLESELECTED,
                "_": "%d " + TRAN_TABLESELECTED
            }
        },
        "aria": {
            "sortAscending": ": " + TRAN_TABLENSORTRISE,
            "sortDescending": ": " + TRAN_TABLENSORTFALL
        }
    },
    columns: [
        {
            data: 'NUMBER'
        },
        {
            data: 'GROUP_NAME'
        },
        {
            data: 'AVERAGE_LENGTH'
        },
        {
            data: 'TITLE'
        },
        {
            data: 'ARTIST'
        },
        {
            data: null
        },
    ],
    columnDefs: [

        {
            targets: 2,
            render: function (data, type, row) {


                return getTimeFromMillis(data);




            }
        },

        {
            targets: -1,
            data: null,
            orderable: false,
            className: 'text-end',
            render: function (data, type, row) {
                return `
                <div class="btn-group mb-3" role="group">
                <a href="javascript:;" onclick="removeAuto('`+ row.ID + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_REMAUTOFILLCART + `"><i class="bi bi-x-square"></i></a>
            </div>
                    `;
            }
        },
    ],
});

const element1 = document.getElementById('cart_select');
    const modal1 = new bootstrap.Modal(element1);

    var initSelCartModalButtons = function () { 
        const cancelButton2 = element1.querySelector('[data-kt-cartsel-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSETHEWINDOW,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: TRAN_YES,
                cancelButtonText: TRAN_NO,
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    modal1.hide();
                }
            });
        });
        const closeButton2 = element1.querySelector('[data-kt-cartsel-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSETHEWINDOW,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: TRAN_YES,
                cancelButtonText: TRAN_NO,
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    modal1.hide();

                }
            });
        });
    }

    initSelCartModalButtons();