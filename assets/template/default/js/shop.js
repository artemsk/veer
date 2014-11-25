
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