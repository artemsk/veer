    $(".category-delete").on("click", function(event) {
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
            },
        });
    });