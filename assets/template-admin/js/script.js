    $(document).ready(function() { 
        if ($(".events-veer-message-center").length > 0) {
            $(".events-veer-message-center").addClass('animated').addClass('flipInX');
        }
    });
    
    $(function() {
        $('[data-toggle="popover"]').popover()
    })
    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    
    $(".input-files-enhance").fileinput({'showUpload':false, 'previewFileType':'any'});