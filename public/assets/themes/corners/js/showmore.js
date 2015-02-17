$('.pagination-button').click(function(event) {
        event.preventDefault();
        
        var currentPage = $(this).attr('data-page');        
        var insertDataDiv = '#showMoreData' + currentPage;

        $.ajax({
            type: 'GET',
            url: $(this).attr('href'),
            success: function(results) { 
               $(insertDataDiv).html(results);                
            },
        });  
});