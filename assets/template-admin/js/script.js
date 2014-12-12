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