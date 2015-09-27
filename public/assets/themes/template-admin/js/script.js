var timeout;
var timeoutLock;

function updateLockAndAutosave(type, selector) {
    if (timeoutLock) {
        clearTimeout(timeoutLock);
        timeoutLock = null;
    }

    timeoutLock = setTimeout(function () {
        $.ajax({
            type: 'GET',
            url: '../admin/worker',
            data: 'worker-lock=true&entity=' + type + '&' + window.location.search.substring(1),
            success: function (results) {
                console.log('updated');
            },
        });

        autosaveTxt(type + '&' + window.location.search.substring(1) + '&' + selector, $('.' + selector).val());

        setRestoreLink(type, selector);

    }, 5000);
}

function autosaveTxt(key, value) {
    if (typeof (Storage) !== "undefined") {
        localStorage.setItem(key, value);
        localStorage.setItem(key + '-time', new Date());
    }
}

function setRestoreLink(type, selector) {
    var k = type + '&' + window.location.search.substring(1) + '&' + selector;
    var lastd = (localStorage.getItem(k + '-time'));
    if (lastd)
        $('.' + selector + '-saved').html('<br/>saved <a class="' + selector + '-restore">' + lastd + '</a>');
}

function suggestions(selector, id) {

    var d = selector.val();
    var separator = selector.attr('data-separator');
    if (separator == undefined) {
        separator = ',';
    }
    var darr = d.split(separator);
    var latest = darr[darr.length - 1];
    var type = selector.attr('data-type');

    if (latest.length > 1 || (type == 'image' && latest.length > 0)) {

        if (timeout) {
            clearTimeout(timeout);
            timeout = null;
        }

        timeout = setTimeout(function () {
            $.ajax({
                type: 'POST',
                url: '../api/lists/' + type,
                data: 'whole=' + d + '&needle=' + latest + '&separator=' + separator + '&selectorId=' + id,
                success: function (results) {
                    $('#loadedSuggestions-' + type + id).html(results);
                    /*console.log('#loadedSuggestions-' + type + id);*/
                },
            });
        }, 400);
    }
}

function textStats(data, selector) {
    var l1 = $(data).val();
    var l11 = l1.length;
    var l12 = (l1.split(/[^\s\.!\?]+[\s\.!\?]+/g).length) - 1;
    //var l13 = l1.match( /[^\.!\?]+[\.!\?]+/g );
    var l13 = l1.replace(/[^\.!\?]+[\.!\?]+/g, "$1|").split("|");
    //var l14 = l13.length;
    var l14 = l13.length;

    /* stats */
    // average word length
    var l15 = Math.round(l11 / l12);
    // average words per sentence
    var l16 = Math.round(l12 / l14);
    // current words in sentence
    var l17 = ((l13[l14 - 1]).split(/[^\s\.!\?]+[\s\.!\?]+/g).length) - 1;

    $(selector + ' .statistics-chars').html(l11);
    $(selector + ' .statistics-words').html(l12);
    $(selector + ' .statistics-sent').html(l14 - 1);
    $(selector + ' .statistics-avg-word').html(l15);
    $(selector + ' .statistics-avg-sent').html(l16);
    $(selector + ' .statistics-current-sent').html(l17);
}

function overlayLoad() {
    $(".overlay").show();
    $(".overlay").animate({opacity:"0.24902"},250);
}

function overlayHide() {
    $(".overlay").animate({opacity:"0"},250);
    $(".overlay").hide();
}

function getNotificationMessages() {
    $.ajax({
        type: 'GET',
        url: '../admin/worker',
        data: 'get-messages=true',
        success: function(results) { 
            if(results != '') { 
                //console.log(results);
                $('.events-veer-message-center').remove();
                $('<div class="events-veer-message-center">' + results + "</div>").insertAfter('.overlay');
                $(".events-veer-message-center").show().addClass('animated').addClass('flipInX');

                 setTimeout(function() {
                  $(".events-veer-message-center").removeClass('flipInX').addClass('flipOutX');
                 }, 5000);
            }
        },
    }); 
}

function sortableLoad() {
    $('.sortable').sortable().bind('sortupdate', function(e, ui) {
        $.ajax({
            type: 'POST',
            data: { 'action': 'sort', 'oldindex' : ui.oldindex, 
                    'newindex': ui.item.index(), 'parentid' : $(this).attr('data-parentid'),
                    '_method' : 'PUT' },
            success: function(results) { 
                $('.testajax').html(results); 
            },
        });
    });
}

function reloadContent(url, selector) {
    $.ajax({
        type: 'GET',
        //data: '_json=true',
        url: url,
        success: function(results) {
            var content = $('<div />').append(results).find(selector).html();
            $(selector).html(content);
            NProgress.done();            
            //overlayHide();
            setupPlugins();
        },
    });
}

function setupPlugins() {
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();
    sortableLoad();
    $(".input-files-enhance").fileinput({'showUpload':false, 'previewFileType':'any'});
    $(".input-files-enhance-upload").fileinput({'previewFileType':'any'});
    $('.input-daterange, .date-container').datepicker({
        weekStart: 1,
        todayBtn: "linked"
    });
    $(".page-checkboxes").bootstrapSwitch({'labelWidth':0});
}

var widthNew;
var removedClass = false;
function updateWidth() {
    widthNew = $(window).width();
    if (widthNew < 768) {
        $('.dynamic-input-group').removeClass('input-group');
        $('.dynamic-input-group-btn').removeClass('input-group-btn');
        $('.dynamic-input-group-addon').removeClass('input-group-addon');
        $('.dynamic-input-group-input').addClass('limited-size-input-100');
        removedClass = true;
    } else {
        if (removedClass == true) {
            $('.dynamic-input-group').addClass('input-group');
            $('.dynamic-input-group-btn').addClass('input-group-btn');
            $('.dynamic-input-group-addon').addClass('input-group-addon');
            $('.dynamic-input-group-input').removeClass('limited-size-input-100');
            removedClass = false;
        }
    }
}

/* common */

$(document).on('keyup', '.show-list-of-items', {}, function() {
    suggestions($(this), '');
});

$(document).on('click', 'button', {}, function() {
  $("button", $(this).parents("form")).removeAttr("clicked");
    $(this).attr("clicked", "true");
});

$('.logo-site').hover(function () {
    $('.top-veer-line').addClass('top-veer-line-hover');
}, function () {
    $('.top-veer-line').removeClass('top-veer-line-hover');
});

var identifier = window.location.hash;
if ($(identifier).length > 0) {
    $("html,body").animate({scrollTop: $(identifier).offset().top - 60}, 350);
}

$(window).ready(updateWidth);
$(window).resize(updateWidth); 
    
/* --- */    
    
/* setup */
setupPlugins();

if ($(".events-veer-message-center").show().length > 0) {
    $(".events-veer-message-center").addClass('animated').addClass('flipInX');

    setTimeout(function () {
        $(".events-veer-message-center").removeClass('flipInX').addClass('flipOutX');
    }, 10000);
}

$(document).on('change', 'input, textarea, select', {}, function () {
    if ($(this).attr('id') != 'SearchField') {
        $(".action-hover-box").show().addClass('animated').addClass('flipInY');

        setTimeout(function () {
            $(".action-hover-box").removeClass('animated').removeClass('flipInY');
        }, 2000);
    }
});

$(document).on('click', '.bootstrap-switch-container', {}, function () {
    $(".action-hover-box").show().addClass('animated').addClass('flipInY');

    setTimeout(function () {
        $(".action-hover-box").removeClass('animated').removeClass('flipInY');
    }, 2000);
});
    
$(document).on('submit', '.veer-form-submit-configuration', {}, function () {
    var siteid = $("button[type=submit][clicked=true]").attr('data-siteid');
    var intheme = $("button[type=submit][clicked=true]").attr('data-intheme');
    var name = $("button[type=submit][clicked=true]").attr('name');
    var id = name.slice(5, -1);
    var type = name.slice(0, 4);
    var data = $(this).serialize() + '&siteid=' + siteid + '&' + type + '=' + id;

    //console.log(siteid + ' ' + intheme + ' ' + name + ' ' + id + ' ' + type);
    if (id == 'new') {
        id = id + siteid + intheme;
        type = 'new';
    }

    if (type == 'save' || type == 'new' || type == '_run' || type == 'paus') {
        $('#card' + id).addClass('animated').addClass('flipInY');
    }

    if (type == 'dele') {
        $('#card' + id).addClass('animated').addClass('flipOutY');
    }

    var url = $(this).attr('action');

    if (type == 'TURNOFF') { /* temp. turn off */
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function (results) {
                $('#cardstock' + siteid).html(results);
            },
        });

        setTimeout(function () {
            $('#card' + id).removeClass('animated').removeClass('flipInY');
        }, 1000);
    }
});
  
$(document).on('click', '.copybutton', {}, function () {
    var key = $(this).attr('data-confkey');
    var val = $(this).attr('data-confval');
    var name = $(this).attr('data-confname');
    var type = $(this).attr('data-conftype');
    var src = $(this).attr('data-confsrc');
    var siteid = $(this).attr('data-confsiteid');
    var thm = $(this).attr('data-conftheme');
    $('.newkey').val(key);
    $('.newval').val(val);
    $('.newname').val(name);
    $('.newtype').val(type);
    $('.newsrc').val(src);
    $('.newtheme').val(thm);

    $("html,body").animate({scrollTop: $('#cardnew' + siteid + thm).offset().top - 60}, 350);
    $('.newcard').addClass('animated').addClass('flipInX');
    setTimeout(function () {
        $('.newcard').removeClass('animated').removeClass('flipInX');
    }, 1000);
});
           
if ($('.page-small-txt').length) {
    textStats($('.page-small-txt'), '.page-small-txt-statistics');
    textStats($('.page-main-txt'), '.page-main-txt-statistics');
    setRestoreLink('pages', 'page-small-txt');
    setRestoreLink('pages', 'page-main-txt');

    $('.page-small-txt').keyup(function () {
        textStats($(this), '.page-small-txt-statistics');
        updateLockAndAutosave('pages', 'page-small-txt');
    });

    $('.page-main-txt').keyup(function () {
        textStats($(this), '.page-main-txt-statistics');
        updateLockAndAutosave('pages', 'page-main-txt');
    });

    $('.page-small-txt-saved').on('click', function () {
        $('.page-small-txt').val(localStorage.getItem('pages&' + window.location.search.substring(1) + '&page-small-txt'));
    });

    $('.page-main-txt-saved').on('click', function () {
        $('.page-main-txt').val(localStorage.getItem('pages&' + window.location.search.substring(1) + '&page-main-txt'));
    });
}
  
var attributes = 1;  
$(document).on('click', '.add-more-attributes', {}, function () {
    var d = $('.new-attribute-block').html();
    d = d.replace(/new/g, 'new' + attributes);
    d = d.replace(/attributes-suggestions-id/g, 'attributes-suggestions-id' + attributes);
    d = d.replace(/loadedSuggestions-attribute/g, 'loadedSuggestions-attribute' + attributes);
    d = d.replace(/suggestions-attribute/g, 'suggestions-attribute' + attributes);

    $('.new-attributes-added').append(d);

    $('#attributes-suggestions-id' + attributes).ready(function () {
        var rem = attributes;
        $('#attributes-suggestions-id' + attributes).keyup(function () {
            suggestions($(this), rem);
        });
    });
    attributes = attributes + 1;
});
    
$(document).on('click', '.category-delete', {}, function(event) {
    event.preventDefault();
    var categoryid = $(this).attr('data-categoryid');
    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: {
            'deletecategoryid' : categoryid,
            'action' : 'delete',
            '_method' : 'PUT' },
        success: function(results) { 
            $('.category-item-' + categoryid).addClass('animated').addClass('bounceOutUp');

            setTimeout(function() {
            $('.category-item-' + categoryid).hide();
            }, 1000);
            getNotificationMessages(); 
        },
    });
});

$(document).on('submit', '.category-add', {}, function(event) {
    event.preventDefault();
    var siteid = $(this).attr('data-siteid');
    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: $(this).serialize() + '&action=add',
        success: function(results) { 
            $('.categories-list-' +siteid + ' ul').addClass('animated').addClass('bounce').html(results);
            setTimeout(function() {
            $('.categories-list-' +siteid + ' ul').removeClass('animated').removeClass('bounce');
            }, 1000);
            $('.sortable').sortable();
            getNotificationMessages(); 
        },
    });
}); 

// TODO: unite
$(".ajaxFormSubmit").on("submit",  function(event) {
    event.preventDefault();
    var resultdivid = $("button[type=submit][clicked=true]").attr('data-resultdiv');
    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: $(this).serialize() + '&button=' + $("button[type=submit][clicked=true]").val(),
        success: function(results) { 
            $(resultdivid).html(results); 
        },
      }); 
}); 
  
$(document).on('submit', '.ajax-form-submit form', {}, function(event) {
    NProgress.start();
    //overlayLoad();
    var clickedButton = $("button[type=submit][clicked=true]");    
    if(!clickedButton.hasClass('submit-skip-ajax')) {
        var selector = $(this).parents('.ajax-form-submit').attr('data-replace-div');
        var formdata = new FormData($(this).get(0));

        event.preventDefault();
        var url = $(this).attr('action');

        console.log(clickedButton.val());
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            //data: $(this).serialize() + '&' + clickedButton.attr('name') + '=' + clickedButton.val(),
            data: formdata,
            processData: false,
            contentType: false,  
            success: function(results) {
                reloadContent(url, selector);
                getNotificationMessages();            
            }
        });
    }
});
