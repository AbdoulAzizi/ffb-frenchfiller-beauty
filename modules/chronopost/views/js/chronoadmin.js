$( document ).ready(function() {

    $("body[class*='chrono']").on('blur change', 'input[type=number]', function (e) {
        var t = $(this);
        if (this.hasAttribute('min')) {
            var minValue = parseInt(t.attr('min'));
            if (t.val() < minValue) {
                t.val(minValue);
            }
        }
    });

    if ('undefined' !== typeof chronopost_delayed_errors && Array.isArray(chronopost_delayed_errors) && chronopost_delayed_errors.length > 0) {
        var message = '';
        for (var i = 0; i < chronopost_delayed_errors.length; i++) {
            message += chronopost_delayed_errors[i] + '\n';
        }
        alert(message);
    }

});
