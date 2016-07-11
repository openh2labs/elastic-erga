

/**
 *
 * @type {Bloodhound}
 *
 * Uris
 */
(function bloodHoundInitialiser (Bloodhound, $, console) {

    var BloudHoundLibratoForm = function(Bloodhound, $, console) {

        var uris = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/uri/librato'
            }
        });

// passing in `null` for the `options` arguments will result in the default
// options being used
        $('#prefetch_uris .typeahead').typeahead(null, {
            name: 'uris',
            source: uris,
            limit: 10
        });

        var usernames = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/username/librato'
            }
        });

        $('#prefetch_usernames .typeahead').typeahead(null, {
            name: 'usernames',
            source: usernames,
            limit: 10,
        });

        var api_keys = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/api_key/librato'
            }
        });

        $('#prefetch_api_keys .typeahead').typeahead(null, {
            name: 'api_keys',
            source: api_keys,
            limit: 10,
        });

        var sources = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/source/librato'
            }
        });

        $('#prefetch_sources .typeahead').typeahead(null, {
            name: 'sources',
            source: sources,
            limit: 10,
        });
    }


    if ($('meta[type="js-module"][name="librato-form"]').length) {
        BloudHoundLibratoForm(Bloodhound, $, console);
    }

})(Bloodhound, jQuery, window.console);