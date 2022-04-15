/**
  * MODULE PRESTASHOP OFFICIEL CHRONOPOST
  * 
  * LICENSE : All rights reserved - COPY AND REDISTRIBUTION FORBIDDEN WITHOUT PRIOR CONSENT FROM OXILEO
  * LICENCE : Tous droits réservés, le droit d'auteur s'applique - COPIE ET REDISTRIBUTION INTERDITES
* SANS ACCORD EXPRES D'OXILEO
  *
  * @author    Oxileo SAS <contact@oxileo.eu>
  * @copyright 2001-2018 Oxileo SAS
  * @license   Proprietary - no redistribution without authorization
  */

function toggleRDVpane(cust_address, codePostal, city, e) {
    if(typeof rdv_carrierIntID === "undefined" || typeof rdv_carrierID === "undefined") {
        return false;
    }
    if ($("input.delivery_option_radio:checked").val() == rdv_carrierID + "," || $("input[name=id_carrier]:checked").val() == rdv_carrierIntID) {
        if (typeof e != "undefined") {
            e.stopPropagation();
        }

        var tellMeWhere = $("input.delivery_option_radio:checked").parent().parent().parent().parent().parent();
        if (!tellMeWhere.parent().hasClass('delivery_option')) {
            tellMeWhere = tellMeWhere.parent().parent();
        }

        $('#chronordv_container').insertAfter(tellMeWhere);

        if ($('input[name="chronoRDVSlot"]:checked').length === 0) {
            $('input[name="chronoRDVSlot"]:first').click();
        }

        if ($.fn.uniform) {
            $('#chronordv_container input').uniform();
        }

        $('#chronordv_container').show();
        return false;
    }

    // Hide controls
    $('#chronordv_container').hide();
}

function associateCreneau(rank, deliveryDate, deliveryDateEnd, slotCode, tariffLevel, transactionID, fee) {
    $.ajax({
        url: path + '/async/storeCreneau.php?rank=' + rank + '&deliveryDate=' + encodeURIComponent(deliveryDate) + '&deliveryDateEnd=' + encodeURIComponent(deliveryDateEnd) + '&slotCode=' + encodeURIComponent(slotCode) + '&tariffLevel=' + tariffLevel + '&transactionID=' + encodeURIComponent(transactionID) + '&fee=' + fee + '&cartID=' + cartID
    });
}

$(document).ready(function () {
    // Listener for selection of the chronordv carrier radio button
    $('.delivery_options span.custom-radio > input[type=radio], input[name*="delivery_option"]').click(function (e) {
        toggleRDVpane(cust_address_clean, cust_codePostal, cust_city, e);

        if (typeof rdv_carrierID != 'undefined' && parseInt($(this).val()) == rdv_carrierID) {
            $('html, body').animate({
                scrollTop: $('#chronordv_container').offset().top
            }, 1500);
        }
    });

    $('input[name="chronoRDVSlot"]').change(function (e) {
        var rank = $('input[name="chronoRDVSlot"]:checked').val();
        var fee = $('input[name="chronoRDVSlot"]:checked').attr('data-fee');
        var deliveryDate = $('input[name="chronoRDVSlot"]:checked').attr('data-delivery-date');
        var deliveryDateEnd = $('input[name="chronoRDVSlot"]:checked').attr('data-delivery-date-end');
        var slotCode = $('input[name="chronoRDVSlot"]:checked').attr('data-slot-code');
        var tariffLevel = $('input[name="chronoRDVSlot"]:checked').attr('data-tariff-level');
        associateCreneau(rank, deliveryDate, deliveryDateEnd, slotCode, tariffLevel, transactionID, fee);
    });

    $('#nextWeek').on('click', function (e) {
        e.preventDefault();
        if (current_content < max_content) {
            $('#content' + current_content).hide();
            current_content++;
            $('#content' + current_content).show();
        }
    });

    $('#previousWeek').on('click', function (e) {
        e.preventDefault();
        if (current_content !== 1) {
            $('#content' + current_content).hide();
            current_content--;
            $('#content' + current_content).show();
        }
    });

    if(typeof rdv_carrierIntID === "undefined" && typeof rdv_carrierID === "undefined") {
        return;
    }

    // Init
    chronoCleanupExtraCarrierHook();
    // toggle on load
    toggleRDVpane(cust_address_clean, cust_codePostal, cust_city);
});

// Prevent compatibility issues with Common Services' modules
function chronoCleanupExtraCarrierHook() {
    if(typeof rdv_carrierIntID === "undefined" && typeof rdv_carrierID === "undefined") {
        console.log('early bail cleanup');
        return;
    }
    if ($("#chronordv_container").length > 0) {
        $('#chronordv_dummy_container').remove();
    } else {
        $('#chronordv_dummy_container').attr('id', 'chronordv_container').hide();
    }
}

$(document).ajaxSuccess(function (event, xhr, settings) {
    if(typeof rdv_carrierIntID === "undefined" && typeof rdv_carrierID === "undefined") {
        return;
    }
    if (typeof settings.data !== 'undefined' && settings.data.match(/method=updateExtraCarrier/)) {
        chronoCleanupExtraCarrierHook();
    }
});
