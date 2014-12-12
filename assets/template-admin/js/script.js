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
    
        
    $(".veer-form-submit-configuration").on("click",  function(event) {
        
        //event.preventDefault();

        var siteid = $("button[type=submit][clicked=true]").attr('data-siteid');
        var name = $("button[type=submit][clicked=true]").attr('name');
        var id = name.slice(5,-1);
        var type = name.slice(0,4);
        var url = $(this).attr('action');      
        var data = $(this).serialize()+ '&siteid=' + siteid + '&' + type + '=' + id;
        
        if(id == 'new') { id = id + siteid; type = 'new'; }

        if(type == 'save' || type == 'new') {
        $('#card' + id).addClass('animated').addClass('flipInY');
        }
        
        if(type == 'dele') {
        $('#card' + id).addClass('animated').addClass('flipOutY');    
        }
        
        /*$.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function(results) { 
                if(type != 'dele') { $('#card' + id).html(results); }
            },
          }); */
          
          
    });
    