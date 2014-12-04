
    $("#dropdown-cats .dropdown-menu li a").click(function() {
        $("#dropdown-cats .dropdown-toggle").html($(this).html() + ' <span class="caret"></span>');
    });

    $("#dropdown-fabric .dropdown-menu li a").click(function() {
        $("#dropdown-fabric .dropdown-toggle").html($(this).html() + ' <span class="caret"></span>');
    });

    $("#dropdown-types .dropdown-menu li a").click(function() {
        $("#dropdown-types .dropdown-toggle").html($(this).html() + ' <span class="caret"></span>');
    });
    
    $("#dropdown-types2 .dropdown-menu li a").click(function() {
        $("#dropdown-types2 .dropdown-toggle").html($(this).html() + ' <span class="caret"></span>');
    });

    $(function () {
        $('[data-toggle="popover"]').popover();
    })


    /* $(".menu_scroll").click(function(event){
        event.preventDefault();
        $("html,body").animate({scrollTop:$(this.hash).offset().top - 100}, 500);
    });*/

    $('#headeraffix').affix({       
      offset: {
        top: function() {
          return (this.top = $('.head-logo').outerHeight(true) - 5)  
        }
      }     
    });
    
    $(".block-stndrt, .block-big").hover(function() {
        $(this).toggleClass('block-hover');
    }, function() {
        $(this).removeClass('block-hover');
    });
    
    $(document).ready(function() {
        if ($(".events-veer-message-center").length > 0) {
            $(".events-veer-message-center").animate({height:"100px"},500).
                    delay(1500).animate({height:"-50px"},500);
        }   
    });    

    $(".basket-link").click(function(event) {        
        event.preventDefault();
        
        $(this).addClass('animated').addClass('bounceIn');
        $( "#to-top").addClass('animated').addClass('wobble');
        
        $.get( $(this).attr('href'), function( data ) {
            $( ".basket-div" ).html( data );
            $( "#to-top").removeClass('animated').removeClass('wobble');
        });
        
        var a = $(this);
        
        setTimeout(function() {
          a.removeClass('animated').removeClass('bounceIn');
        }, 2000);
    })