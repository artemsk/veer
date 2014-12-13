    $(document).ready(function() { 
        if ($(".events-veer-message-center").show().length > 0) {
            $(".events-veer-message-center").addClass('animated').addClass('flipInX');
            
            setTimeout(function() {
             $(".events-veer-message-center").removeClass('flipInX').addClass('flipOutX');
            }, 3000);
        }

    });
    
    $(function() {
        $('[data-toggle="popover"]').popover()
    })
    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    
    $(".input-files-enhance").fileinput({'showUpload':false, 'previewFileType':'any'});
    
    $('.input-daterange, .date-container').datepicker({
    weekStart: 1,
    todayBtn: "linked"
    });
    
    $(".page-checkboxes").bootstrapSwitch({'labelWidth':0});
    
    $("input, textarea").change(function() {
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

        if(type == 'save' || type == 'new') {
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
       $('.newkey').val(key);
       $('.newval').val(val);
       $('.newcard').addClass('animated').addClass('flipInX'); 
       setTimeout(function() {
                $('.newcard').removeClass('animated').removeClass('flipInX');
            }, 1000);  
    });
    
    