
var notDisplayImage = true;
var fancyboxClickEnable = false;
var magicToolboxSelectors = [];
var updateScrollStarted = false;

//NOTE: for update image on page load
var displayImageCounter = 2;
var updateIntervalID;
var intervalCounter = 5;

var mainImageContainer = null;
var magic360Container = null;

var skipThisCall = false;

var checkID = null;
var displayImageBusy = false;

window['displayImage'] = function(jQuerySetOfAnchors) {

    if(displayImageBusy) {
        setTimeout(function() {
            displayImage(jQuerySetOfAnchors);
        }, 500);
    }
    displayImageBusy = true;

    //NOTE: for update image on page load
    if(displayImageCounter) {
        displayImageCounter--;
        if(typeof(updateIntervalID) != 'undefined' && updateIntervalID != null) {
            clearInterval(updateIntervalID);
        }
        if(typeof(updateIntervalID) == 'undefined' || updateIntervalID != null) {
            updateIntervalID = setInterval(function() {
                intervalCounter--;
                displayImage(jQuerySetOfAnchors);
                if(!intervalCounter) {
                    clearInterval(updateIntervalID);
                    updateIntervalID = null;
                }
            }, 500);
        }
    }

    if(notDisplayImage) {
        notDisplayImage = false;
        displayImageBusy = false;
        return;
    }

    if(typeof(jQuerySetOfAnchors) == 'undefined' || !jQuerySetOfAnchors.length) { return; }
    var e;
    var anchor = jQuerySetOfAnchors.get(0);

    if (document.createEvent) {
        e = document.createEvent('MouseEvents');
        e.initEvent(mEvent, true, true);
        anchor.dispatchEvent(e);
    } else {
        e = document.createEventObject();
        e.eventType = 'MouseEvents';
        anchor.fireEvent('on' + mEvent, e);
    }

    if(checkID) clearTimeout(checkID);
    checkID = setTimeout(function() {
        var selectorA = jQuerySetOfAnchors.get(0);
        var imgs = $('#MagicZoomPlusImageMainImage > img');
        var mainImg = imgs.get(0);
        if(mainImg.src != selectorA.getAttribute('rev') || mainImg.parentNode.href != selectorA.href) {
            MagicZoomPlus.update('MagicZoomPlusImageMainImage', selectorA.href, selectorA.getAttribute('rev'));
            return;
        }
    }, 500);

    displayImageBusy = false;

}

window['findCombinationOriginal'] = window['findCombination'];
window['findCombination'] = function(firstTime) {
    //NOTE: to skip double call of 'findCombination'
    if(skipThisCall) {
        skipThisCall = false;
        return;
    }

    if(typeof(firstTime) != 'undefined' && firstTime || updateScrollStarted) {
        window['findCombinationOriginal'].apply(window, arguments);
        return;
    }
    updateScroll(window['findCombinationOriginal'], arguments);
    magictoolboxWrapResetImages();
}

window['refreshProductImagesOriginal'] = window['refreshProductImages'];
window['refreshProductImages'] = function(id_product_attribute) {
    if(thumbnailLayout == 'original') {
        window['refreshProductImagesOriginal'].apply(window, arguments);
        return;
    }

    if(isPrestahop15x && !id_product_attribute && !updateScrollStarted && !displayImageCounter/*NOTE: not run on page load*/) {
        updateScroll(window['refreshProductImages'], arguments);
        magictoolboxWrapResetImages();
        return;
    }

    $('div.MagicToolboxSelectorsContainer a').addClass('hidden-selector');
    id_product_attribute = parseInt(id_product_attribute);

    if((!isPrestahop1541 || id_product_attribute > 0) &&
       typeof(combinationImages) != 'undefined' && typeof(combinationImages[id_product_attribute]) != 'undefined') {
        for (var i = 0; i < combinationImages[id_product_attribute].length; i++) {
            $('#thumb_' + parseInt(combinationImages[id_product_attribute][i])).parent().removeClass('hidden-selector');
        }
    } else if(isPrestahop1541) {
        //show all selectors
        $('div.MagicToolboxSelectorsContainer a').removeClass('hidden-selector');
        magictoolboxWrapResetImages();
        return;
    }

    if(i > 0) {

    } else {
        if(typeof(idDefaultImage) != 'undefined') {
            $('#thumb_' + idDefaultImage).parent().removeClass('hidden-selector').show();
            displayImage($('#thumb_'+ idDefaultImage).parent());
        } else {
            //show all selectors
            for(var i = 0; i < magicToolboxSelectors.length; i++) {
                $('#MagicToolboxSelectors'+id_product).append(magicToolboxSelectors[i].cloneNode(true));
            }
            MagicZoomPlus.refresh('MagicZoomPlusImageMainImage');
        }
    }
    magictoolboxWrapResetImages();
}

window['updateColorSelectOriginal'] = window['updateColorSelect'];
window['updateColorSelect'] = function(id_attribute) {
    updateScroll(window['updateColorSelectOriginal'], arguments);
    magictoolboxWrapResetImages();
}

window['updateScroll'] = function(originalFunction, args) {
    updateScrollStarted = true;
    if(!scrollThumbnails) {
        originalFunction.apply(window, args);
        updateScrollStarted = false;
        return;
    }
    MagicScroll.stop('MagicToolboxSelectors'+id_product);
    if(!magicToolboxSelectors.length) {

        //NOTE: remove <div style="clear: both"></div> because it is placed between the selector
        $('#MagicToolboxSelectors'+id_product+' div').remove();
        //NOTE: remove spaces
        $('#MagicToolboxSelectors'+id_product).contents().filter(function() {
            return this.nodeType == 3;
        }).remove();

        //NOTE: sucks in IE
        //var container = document.getElementById('MagicToolboxSelectors'+id_product);
        //if(container) magicToolboxSelectors = container.cloneNode(true).getElementsByTagName('a');

        //NOTE: without jQuery
        //var container = document.getElementById('MagicToolboxSelectors'+id_product);
        //var nodes = container.getElementsByTagName('a');
        //for(var i = 0, l = nodes.length; i < l; i++) {
        //    magicToolboxSelectors.push(nodes[i].cloneNode(true));
        //}

        //NOTE: with jQuery
        var containerClone = $('#MagicToolboxSelectors'+id_product).clone(true);
        magicToolboxSelectors = containerClone.find('a');

    } else {
        $('#MagicToolboxSelectors'+id_product+' a').remove();
        for(var i = 0; i < magicToolboxSelectors.length; i++) {
            $('#MagicToolboxSelectors'+id_product).append(magicToolboxSelectors[i].cloneNode(true));
        }
        MagicZoomPlus.refresh('MagicZoomPlusImageMainImage');
    }
    notDisplayImage = true;//to skip displayImage() because the selector can not be initialized
    originalFunction.apply(window, args);
    $('#MagicToolboxSelectors'+id_product+' a.hidden-selector').remove();

    var items = $('#MagicToolboxSelectors'+id_product+' a').length;

    //NOTE: fix of issue #46688; not to run MagicScroll on selected options when selector's count < 'items' option value
    if(items <= scrollItems) {
        $('#MagicToolboxSelectors'+id_product).removeClass('MagicScroll').css('display', 'block').css('visibility', 'visible');
        $('#MagicToolboxSelectors'+id_product+' a').css('margin-right', selectorsMargin+'px');
        magictoolboxBindSelectors();
        var delay = 200;
        //setTimeout(function() {displayImage($('#MagicToolboxSelectors'+id_product+' a:first'));}, delay);
        setTimeout(function() {displayImage($('#MagicToolboxSelectors'+id_product+' a:not(.magic360selector)').first());}, delay);
        updateScrollStarted = false;
        return;
    }

    if(items > scrollItems) {
        items = scrollItems;
    }
    MagicScroll.extraOptions['MagicToolboxSelectors'+id_product]['items'] = items;
    //display first image on load scroll
    MagicScroll.extraOptions['MagicToolboxSelectors'+id_product]['onload'] = function() {
        var delay = 200;
        if(magicJS.j21.trident && magicJS.j21.version == 900) delay = 400;
        //setTimeout(function() {displayImage($('#MagicToolboxSelectors'+id_product+' a:first'));}, delay);
        setTimeout(function() {displayImage($('#MagicToolboxSelectors'+id_product+' a:not(.magic360selector)').first());}, delay);
    };
    //MagicScroll.init();
    MagicScroll.start('MagicToolboxSelectors'+id_product);
    magictoolboxBindSelectors();
    updateScrollStarted = false;
}

window['checkUrlOriginal'] = window['checkUrl'];
window['checkUrl'] = function() {
    if(!isPrestahop16x) skipThisCall = true;
    var r = window['checkUrlOriginal'].apply(window, arguments);
    skipThisCall = false;
    return r;
}

function magictoolboxWrapResetImages() {
    //NOTE: changes in 'refreshProductImages' function, Prestashop 1.5.4.1
    if(thumbnailLayout != 'original') {
        var hiddenSelectorsCount = parseInt($('#MagicToolboxSelectors'+id_product+' > a.hidden-selector').length);
        if(hiddenSelectorsCount) {
            //$('#wrapResetImages').show('slow');
            $('#wrapResetImages').show();
            return;
        }
        var selectorsCount = parseInt($('#MagicToolboxSelectors'+id_product+' > a').length);
        if(selectorsCount) {
            if(!magicToolboxSelectors.length) {
                //$('#wrapResetImages').hide('slow');    
                $('#wrapResetImages').hide();
                return;
            }
        } else {
            //$('#wrapResetImages').hide('slow');
            $('#wrapResetImages').hide();
            return;
        }
        if(selectorsCount < magicToolboxSelectors.length) {
            //$('#wrapResetImages').show('slow');
            $('#wrapResetImages').show();
        } else {
            //$('#wrapResetImages').hide('slow');
            $('#wrapResetImages').hide();
        }
    }
}

function magictoolboxBindSelectors() {
    //NOTE: for products with magic360 images
    mainImageContainer = document.getElementById('mainImageContainer');
    magic360Container = document.getElementById('magic360Container');
    if(magic360Container) {
        var magicToolboxLinks = $('.magicthickbox');
        for(var j = 0; j < magicToolboxLinks.length; j++) {
            $mjs(magicToolboxLinks[j]).je1(mEvent, function(e) {
                var objThis = e.target || e.srcElement;
                if(objThis.tagName.toLowerCase() == 'img') {
                    objThis = objThis.parentNode;
                }
                var isMagic360Visible = magic360Container.style.display != 'none';
                var isThisMagic360Selector = objThis.className.match(new RegExp('(?:\\s|^)magic360selector(?:\\s|$)'));
                if(isThisMagic360Selector && !isMagic360Visible) {
                    mainImageContainer.style.display = 'none';
                    magic360Container.style.display = 'block';
                } else if(isMagic360Visible && !isThisMagic360Selector) {
                    //NOTE: in order to have time to switch the picture
                    var delay = 250;

                    //NOTE: only on page load (first time)
                    if(mainImageContainer.style.position == 'absolute') {
                        delay = 600;
                    }

                    if(mEvent == 'mouseover') delay = delay + selectorsMouseoverDelay;
                    setTimeout(function() {

                        //NOTE: only on page load (first time)
                        if(mainImageContainer.style.position == 'absolute') {
                            mainImageContainer.style.left = 0;
                            mainImageContainer.style.position = 'static';
                        }

                        magic360Container.style.display = 'none';
                        mainImageContainer.style.display = 'block';
                    }, delay);
                }
                return false;
            });
        }
    }
}

$(document).ready(function() {
    $('#views_block li a.magicthickbox').unbind('mouseenter mouseleave').click(function(){
        $('#views_block li a.shown').removeClass('shown');
        $(this).addClass('shown');
        //for blockcart module
        $('#bigpic').attr('src', $(this).attr('rev'));
    }).slice(0, 1).addClass('shown');
    $('span#view_full_size').unbind('click').click(function(){
        if(!$.fancybox) {
            //for prestashop 1.3.x
            $('#views_block li a.shown').each(function() {
                var t = this.title || this.name || null;
                var a = this.href || this.alt;
                var g = this.rel || false;
                tb_show(t, a, g);
                this.blur();
            });
        } else {
            //for prestashop 1.4.x
            fancyboxClickEnable = true;
            $('#views_block li a.shown').click();
        }
    });
    //NOTE: initialize fancybox only when 'View full size' enabled
    if($('span#view_full_size').length)
    if($.fancybox) {
        //for prestashop 1.4.x
        $('.magicthickbox').fancybox({
            'hideOnContentClick': true,
            'transitionIn'  : 'elastic',
            'transitionOut' : 'elastic',
            'onStart' : function(){
                return fancyboxClickEnable;
            },
            'onClosed' : function(){
                fancyboxClickEnable = false;
            }
        });
    }

    magictoolboxBindSelectors();

});
