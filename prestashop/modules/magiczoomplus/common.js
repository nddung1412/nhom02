var magiczoomplusState = '';

$(document).ready(function() {
    if(typeof(window['display']) != 'undefined') {
        window['display_original'] = window['display'];
        window['display'] = function display(view) {
            if(typeof(MagicZoomPlus) != 'undefined' && magiczoomplusState != 'stopped') {
                magiczoomplusState = 'stopped';
                MagicZoomPlus.stop();
            }
            var r = window['display_original'].apply(window, arguments);
            if(typeof(MagicZoomPlus) != 'undefined' && magiczoomplusState != 'started') {
                magiczoomplusState = 'started';
                MagicZoomPlus.start();
            }
            return r;
        }
    }
});

if($ && $.ajax) {
    (function($) {
        //NOTE: override default ajax method
        var ajax = $.ajax;
        $.ajax = function(url, options) {
            var settings = {};
            if(typeof url === 'object') {
                settings = url;
            } else {
                settings = options || {};
            }
            if(settings.type == 'GET' && settings.url == baseDir+'modules/blocklayered/blocklayered-ajax.php') {
                if(typeof(MagicZoomPlus) != 'undefined' && magiczoomplusState != 'stopped') {
                    magiczoomplusState = 'stopped';
                    MagicZoomPlus.stop();
                }
                settings.url = baseDir+'modules/magiczoomplus/blocklayered-ajax.php';
                settings.successOriginal = settings.success;
                settings.success = function(result) {
                    var r = settings.successOriginal.apply(settings, arguments);
                    if(typeof(MagicZoomPlus) != 'undefined' && magiczoomplusState != 'started') {
                        magiczoomplusState = 'started';
                        MagicZoomPlus.start();
                    }
                    return r;
                };
            }
            return ajax(url, options);
        }
    })($);
}
