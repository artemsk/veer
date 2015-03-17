    $(document).ready(function() { 
        if ($(".events-veer-message-center").show().length > 0) {
            $(".events-veer-message-center").addClass('animated').addClass('flipInX');
            
            setTimeout(function() {
             $(".events-veer-message-center").removeClass('flipInX').addClass('flipOutX');
            }, 10000);
        }

    });
    
    $(function() {
        $('[data-toggle="popover"]').popover()
    })
    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    
    $(".input-files-enhance").fileinput({'showUpload':false, 'previewFileType':'any'});
    $(".input-files-enhance-upload").fileinput({'previewFileType':'any'});
    
    $('.input-daterange, .date-container').datepicker({
    weekStart: 1,
    todayBtn: "linked"
    });
    
    $(".page-checkboxes").bootstrapSwitch({'labelWidth':0});
    
    $("input, textarea, select").change(function() {
       if($(this).attr('id') != 'SearchField') {
        $(".action-hover-box").show().addClass('animated').addClass('flipInY'); 

        setTimeout(function() {
             $(".action-hover-box").removeClass('animated').removeClass('flipInY');
         }, 2000);
        }
    });
    
    $(".bootstrap-switch-container").click(function() {
       $(".action-hover-box").show().addClass('animated').addClass('flipInY'); 

       setTimeout(function() {
            $(".action-hover-box").removeClass('animated').removeClass('flipInY');
        }, 2000);
    });
    
    $('button').click(function() {      
      $("button", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });
    
        
    $(".veer-form-submit-configuration").on("submit",  function(event) {

        var siteid = $("button[type=submit][clicked=true]").attr('data-siteid');
        var name = $("button[type=submit][clicked=true]").attr('name');
        var id = name.slice(5,-1);
        var type = name.slice(0,4);  
        var data = $(this).serialize()+ '&siteid=' + siteid + '&' + type + '=' + id;
        
        if(id == 'new') { id = id + siteid; type = 'new'; }

        if(type == 'save' || type == 'new' || type == '_run' || type == 'paus') {
        $('#card' + id).addClass('animated').addClass('flipInY');
        }
        
        if(type == 'dele') {
        $('#card' + id).addClass('animated').addClass('flipOutY');    
        }
        
        var url = $(this).attr('action');    

        if(type == 'new') {
            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(results) { 
                    $('#cardstock' + siteid).html(results); 
                },
              }); 
              
           setTimeout(function() {
                $('#card' + id).removeClass('animated').removeClass('flipInY');
            }, 1000);  
        }
          
    });
    
    $('.copybutton').click(function() {
       var key = $(this).attr('data-confkey');
       var val = $(this).attr('data-confval');
       var name = $(this).attr('data-confname');
       var type = $(this).attr('data-conftype');
       var src = $(this).attr('data-confsrc');
       $('.newkey').val(key);
       $('.newval').val(val);
       $('.newname').val(name);
       $('.newtype').val(type);
       $('.newsrc').val(src);      
       $('.newcard').addClass('animated').addClass('flipInX'); 
       setTimeout(function() {
                $('.newcard').removeClass('animated').removeClass('flipInX');
            }, 1000);  
    });
           
    $(".category-add").on("submit", function(event) {
        event.preventDefault();
        
        var siteid = $(this).attr('data-siteid');
        
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize() + '&action=add',
            success: function(results) { 
                $('.categories-list-' +siteid).addClass('animated').addClass('bounce').html(results);
                setTimeout(function() {
                $('.categories-list-' +siteid).removeClass('animated').removeClass('bounce');
                }, 1000);                
            },
        });
        
    });
    
 $(function(){
	var widthNew;	
        var removedClass = false;
	function updateWidth(){
		widthNew = $(window).width();
                if(widthNew < 768)
                {
                    $('.dynamic-input-group').removeClass('input-group');
                    $('.dynamic-input-group-btn').removeClass('input-group-btn');
                    $('.dynamic-input-group-addon').removeClass('input-group-addon');
                    $('.dynamic-input-group-input').addClass('limited-size-input-100');
                    removedClass = true;
                } else {
                    if(removedClass == true) {
                        $('.dynamic-input-group').addClass('input-group');
                        $('.dynamic-input-group-btn').addClass('input-group-btn');
                        $('.dynamic-input-group-addon').addClass('input-group-addon');
                        $('.dynamic-input-group-input').removeClass('limited-size-input-100');                        
                        removedClass = false;
                    }
                 }  
	};
	$(window).ready(updateWidth);
	$(window).resize(updateWidth); 
}); 



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
    
   var timeout;
    
   $('.show-list-of-items').keyup(function() {
      
      var d = $(this).val();
      var separator = $(this).attr('data-separator');
      if(separator == undefined) { separator = ','; }
      var darr = d.split(separator);
      var latest = darr[darr.length-1];
      var type = $(this).attr('data-type');
      
      if(latest.length >1 || (type == 'image' && latest.length>0)) {
          
       if(timeout) {
            clearTimeout(timeout);
            timeout = null;
        }
   
      timeout = setTimeout(function() {
      $.ajax({
            type: 'POST',
            url: '../api/lists/' + type,
            data: 'whole=' + d + '&needle=' + latest + '&separator=' + separator,
            success: function(results) { 
                $('#loadedSuggestions-' + type).html(results);
            },
          }); 
        },400);
       }      
   });
   
    /*  $('.sortableImages').sortable().bind('sortupdate', function(e, ui) {
         console.log(ui.oldindex + " " + ui.item.index());
            
        });
        */
       
   
    function textStats(data, selector) {
        var l1 = $(data).val();
        var l11 = l1.length;
        var l12 = (l1.split(/[^\s\.!\?]+[\s\.!\?]+/g).length)-1;
        //var l13 = l1.match( /[^\.!\?]+[\.!\?]+/g );
        var l13 = l1.replace( /[^\.!\?]+[\.!\?]+/g, "$1|").split("|");
        //var l14 = l13.length;
        var l14 = l13.length;

        /* stats */
        // average word length
        var l15 = Math.round(l11/l12);
        // average words per sentence
        var l16 = Math.round(l12/l14);
        // current words in sentence
        var l17 = ((l13[l14-1]).split(/[^\s\.!\?]+[\s\.!\?]+/g).length)-1;

        $(selector + ' .statistics-chars').html(l11);
        $(selector + ' .statistics-words').html(l12);
        $(selector + ' .statistics-sent').html(l14-1);
        $(selector + ' .statistics-avg-word').html(l15);
        $(selector + ' .statistics-avg-sent').html(l16);
        $(selector + ' .statistics-current-sent').html(l17);
    }
    
    var timeoutLock;
    
    function updateLockAndAutosave(type, selector)
    {
        if(timeoutLock) {
            clearTimeout(timeoutLock);
            timeoutLock = null;
        }
   
      timeoutLock = setTimeout(function() {
      $.ajax({
            type: 'GET',
            url: '../admin/worker',
            data: 'worker-lock=true&entity=' + type + '&' + window.location.search.substring(1),
            success: function(results) { 
               console.log('updated');
            },
          });          
          
        autosaveTxt(type + '&' + window.location.search.substring(1) + '&' + selector, $('.' + selector).val()); 
        
        setRestoreLink(type, selector);
        
        },5000);
            
    }
    
    function autosaveTxt(key, value)
    {
        if(typeof(Storage) !== "undefined") {
            localStorage.setItem(key,value);
            localStorage.setItem(key + '-time', new Date());
        }
    }
    
    function setRestoreLink(type, selector)
    {
        var k = type + '&' + window.location.search.substring(1) + '&' + selector;         
        var lastd = (localStorage.getItem(k + '-time'));        
        if(lastd) $('.' + selector + '-saved').html('<br/>saved <a class="' + selector + '-restore">' + lastd + '</a>');
    }
   
  $(document).ready(function() {
     if($('.page-small-txt').length) {
         
         textStats($('.page-small-txt'), '.page-small-txt-statistics');
         textStats($('.page-main-txt'), '.page-main-txt-statistics');
         
         setRestoreLink('pages', 'page-small-txt');
         setRestoreLink('pages', 'page-main-txt');
         
         $('.page-small-txt').keyup(function() {
            textStats($(this), '.page-small-txt-statistics');
            
            updateLockAndAutosave('pages', 'page-small-txt');
         });
                 
         $('.page-main-txt').keyup(function() {
            textStats($(this), '.page-main-txt-statistics');
            
            updateLockAndAutosave('pages', 'page-main-txt');
         });
         
         $('.page-small-txt-saved').on('click', function() {   
             $('.page-small-txt').val(localStorage.getItem('pages&' + window.location.search.substring(1) + '&page-small-txt'));
         });
                 
        $('.page-main-txt-saved').on('click', function() {   
             $('.page-main-txt').val(localStorage.getItem('pages&' + window.location.search.substring(1) + '&page-main-txt'));
         });         
     } 
  });   