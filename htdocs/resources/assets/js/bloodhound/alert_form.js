


(function bloodHoundInitialiser (Bloodhound, $, console) {

    var BloudHoundAlertForm = function(Bloodhound, $, console){

        var es_hosts = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/es_host/alerts'
            }
        });

// passing in `null` for the `options` arguments will result in the default
// options being used
        $('#prefetch .typeahead').typeahead(null, {
            name: 'es_hosts',
            source: es_hosts,
            limit: 10
        });


        var es_indexes = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/es_index/alerts'
            }
        });

        $('#prefetch_indexes .typeahead').typeahead(null, {
            name: 'es_indexes',
            source: es_indexes,
            limit: 10,
        });

        var es_types = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/es_type/alerts'
            }
        });

        $('#prefetch_types .typeahead').typeahead(null, {
            name: 'es_types',
            source: es_types,
            limit: 10,
        });

        var es_datetime_fields = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/es_datetime_field/alerts'
            }
        });

        $('#prefetch_es_date_time_field .typeahead').typeahead(null, {
            name: 'es_datetime_fields',
            source: es_datetime_fields,
            limit: 10,
        });


        var alert_email_senders = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/alert_email_sender/alerts'
            }
        });

        $('#prefetch_alert_email_sender .typeahead').typeahead(null, {
            name: 'alert_email_senders',
            source: alert_email_senders,
            limit: 10,
        });

        var alert_email_recipients = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/alert_email_recipient/alerts'
            }
        });

        $('#prefetch_alert_email_recipient .typeahead').typeahead(null, {
            name: 'alert_email_recipients',
            source: alert_email_recipients,
            limit: 10,
        });

        console.log('Bloodhound Alert Form Initialised!');
    }



    if ($('meta[type="js-module"][name="alert-form"]').length) {
        BloudHoundAlertForm(Bloodhound, $, console);
    }

})(Bloodhound, jQuery, window.console);