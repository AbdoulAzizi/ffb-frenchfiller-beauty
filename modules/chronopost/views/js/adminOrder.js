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

$( document ).ready(function() {

    // if(lt_history) {
    // 	// If we have several skybills, add them to the link
    // 	var lt_numbers = lt_history[0];
    // 	for (var i = 1; i < lt_history.length; i++) {
    // 		lt_numbers = lt_numbers + '<br>' + lt_history[i];
    // 	}
    // 	$('span.shipping_number_show a').html(lt_numbers);
    // }

    if (lt_history_link !== false && lt_history_link != '' && lt_history_link != null) {


        $('span.shipping_number_show a').remove();
        var lt_numbers = '';
        $.each(lt_history_link, function (index, value) {
            lt_numbers += '<a href="'+value+'">'+index+'</a><br>';
        });

        $('span.shipping_number_show').html(lt_numbers);

        setInactive();
        $("#shipping_table").append("<tr><td></td><td></td><td></td><td></td><td></td>"
            +"<td colspan=\"2\"><a class=\"cancelSkybill\" href=\"\">Annuler cet envoi</a></td></tr>");

    }

    $("input[name='return']").on('click', function (e) {
        e.preventDefault();
        $(this).prop('disabled', true);
        $("#chronoSubmitButton").prop('disabled', true);
        var orderId = $("input[name='orderid']").val();
        var weight = "";
        var length = "";
        var width = "";
        var height= "";

        $('input[name="height[]"]').each(function(index){
            height += "&height["+index+"]="+$(this).val();
        });
        $('input[name="weight[]"]').each(function(index){
            weight += "&weight["+index+"]="+$(this).val();
        });
        $('input[name="length[]"]').each(function(index){
            length += "&length["+index+"]="+$(this).val();
        });
        $('input[name="width[]"]').each(function(index){
            width += "&width["+index+"]="+$(this).val();
        });

        $.ajax({
            url: path+'/async/checkColis.php?orderId='+ orderId + weight + length + width + height,
            success: function (data) {
                var result = JSON.parse(data);
                if(result['error'] !== 0){
                    var message ="<div class='alert alert-danger'><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>"+result['message']+"</div>";
                    $("#ajaxBox").html(message);
                    $("#ajaxBox").prependTo($("#content"));
                    $("#ajaxBox").show();
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    resetFormButtons();
                    return false;
                }
                $('<input />').attr('type', 'hidden')
                    .attr('name', "return")
                    .attr('value', "true")
                    .appendTo('#chrono_form')
                ;
                $("#chrono_form").submit();
                $(this).prop('disabled', true);
            }
        });
        return true;
    });

    $("#chronoSubmitButton").on('click', function(e) {

        e.preventDefault();
        setInactive();
        $(this).prop('disabled', true);
        $('input[name="return"]').prop('disabled', true);
        var orderId = $("input[name='orderid']").val();
        var weight = "";
        var length = "";
        var width = "";
        var height= "";

        $('input[name="height[]"]').each(function(index){
           height += "&height["+index+"]="+$(this).val();
        });
        $('input[name="weight[]"]').each(function(index){
            weight += "&weight["+index+"]="+$(this).val();
        });
        $('input[name="length[]"]').each(function(index){
            length += "&length["+index+"]="+$(this).val();
        });
        $('input[name="width[]"]').each(function(index){
            width += "&width["+index+"]="+$(this).val();
        });

        if(lt_history.length > 1) {
            e.preventDefault();
            var pdf_path = [];
            for (var i = 0; i < lt_history.length; i++) {

                pdf_path.push("/skybills/"+lt_history[i]+".pdf");
            }
            $.ajax({
                type:"POST",
                url:path+"/async/mergeSkybillPdf.php",
                data:{pdfs:pdf_path},
                success:function(response){
                    reEnableFormButtons();
                    window.open(path+response);
                }
            });

            return false;
        }
        else if(lt) {
            e.preventDefault();
            document.location.href=path+"/skybills/"+lt+".pdf";
            return false;
        }

        else{
            $.ajax({
                url: path+'/async/checkColis.php?orderId='+ orderId + weight + length + width + height,
                success: function (data) {
                    var result = JSON.parse(data);
                    if(result['error'] !== 0){
                        var message ="<div class='alert alert-danger'><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>"+result['message']+"</div>";
                        $("#ajaxBox").html(message);
                        $("#ajaxBox").prependTo($("#content"));
                        $("#ajaxBox").show();
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                        resetFormButtons();
                        return false;
                    }
                    $("#chrono_form").submit();
                    $(this).prop('disabled', true);
                }
            });
        }
        return true;
    });

    $("#chrono_form").on('submit', function(e) {
        setTimeout(function (){
            alert('Lettre de transport bien créée');
            window.location.reload();
        }, 5000);

    });


    $(".cancelSkybill").on('click', function(e) {
        e.preventDefault();
        if(confirm("Êtes-vous sûr de vouloir annuler cet envoi ? La lettre de transport associée sera inutilisable.")) {
            $.get(path+"/async/cancelSkybill.php", { skybill: lt, shared_secret: chronopost_secret, id_order: $("input[name=id_order]").val()}).done( function( data ) {
                alert('Lettre de transport bien annulée.');
                location.reload();
            });
        }
    });

    
    function prepareDimensions(el) {
        var total_requested = el.val();
        if (total_requested <= 0) {
            total_requested = 1;
            $("#multiOne").val(1);
        }

        // Disable insurance if multiple packages
        if ($('#advalorem').length) {
            if (total_requested > 1) {
                $('#advalorem').attr('disabled', 'disabled');
                $('#advalorem_value').attr('disabled', 'disabled');
            } else {
                $('#advalorem').removeAttr('disabled');
                $('#advalorem_value').removeAttr('disabled');
            }
        }

        var total = $('#dimensions > div').length;
        var total_needed = total_requested - total;
        //$("#dimensions").html('');
        for(var i = 0; i < total_needed; i++){
            $("#dimensions").append("<div class='dimensions-group'>" + dimensionsElement + "</div>");
        }


        if (total_needed < 0) {
            var groups = $(".dimensions-group");
            for (var j = 1; j <= total; j++) {
                if (j > total_requested) {
                    var index = parseInt(j - 1);
                    groups.get(index).remove();
                }
            }
        }
    }

    var dimensionsElement = $("#dimensions .dimensions-group").first().clone().html();

    prepareDimensions($("#multiOne"));

    $("#multiOne").on('change', function(e){
        prepareDimensions($(this));
    });

    chronopostSubmitButtonTxt = $("#chronoSubmitButton").val();
    chronopostReturnButtonTxt = $("#chrono_form input[name=return]").val();

});

var chronopostSubmitButtonTxt;
var chronopostReturnButtonTxt;

function setInactive() {
    $("#chronoSubmitButton").val("Ré-imprimer l'étiquette Chronopost");
}

function reEnableFormButtons() {
    var submitButton = $("#chronoSubmitButton");
    var returnButton = $("#chrono_form input[name=return]");
    submitButton.removeAttr('disabled');
    returnButton.removeAttr('disabled');
}

function resetFormButtons() {
    var submitButton = $("#chronoSubmitButton");
    var returnButton = $("#chrono_form input[name=return]");
    submitButton.val(chronopostSubmitButtonTxt);
    submitButton.removeAttr('disabled');
    returnButton.val(chronopostReturnButtonTxt);
    returnButton.removeAttr('disabled');
}
