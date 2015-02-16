
$(".menu_scroll").click(function(event){
    event.preventDefault();
    $("html,body").animate({scrollTop:$(this.hash).offset().top - 100}, 500);
});

var rememberAboveHeight;

$(".fold-header").click(function() { 
    if($(this).height() <= 100) {
        $("html,body").animate({scrollTop:0}, 350);
        $(this).animate({height:"500px"},500);
        
        if(rememberAboveHeight == undefined) { rememberAboveHeight = $(".above-header").height(); }
        $(".above-header").animate({marginTop:0-rememberAboveHeight},500);
        
        //$(".yellow_line_top").animate({marginTop:"293px"},500);
        $(".arrow_div").animate({marginTop:"270px"},500);
        $('.yellow_line_top_container').css('position','relative');
        $('.yellow_line_top_container').css('top','0px');
        $('#topchevron').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
    } else {
        $(this).animate({height:"30px"},500);
        $(".above-header").animate({marginTop:"0px"},500);
        //$(".yellow_line_top").animate({marginTop:"23px"},500);
        $(".arrow_div").animate({marginTop:"0px"},500);
        $('.yellow_line_top_container').css('position','');
        $('.yellow_line_top_container').css('top','');
        $('#topchevron').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
    }
});


$(".logo-header").click(function(event) {
    event.stopPropagation(event);
    $(".sidebar").animate({left:"0px"},250);
    $(".overlay").show();
    $(".overlay").animate({opacity:"0.74902"},250);
});

$('html').click(function() {
    $(".sidebar").animate({left:"-220px"},250);
    setTimeout(function() {
           $(".overlay").animate({opacity:"0"},250);
           $(".overlay").hide();
        }, 250);
});

$(".contact-form button").click(function() {
    $(".contact-form").addClass('animated').addClass('bounceOutLeft');
    setTimeout(function() {
        $(".contact-form").hide();
            $(".contact-form-2").removeClass('hidden').addClass('animated').addClass('bounceInRight');
        }, 350);
});

//;

var s = skrollr.init({forceHeight: false});

function turningOnOffParallax() {
    
    var heroBottom = $('.bolshaya-main-content').offset().top;
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    if (docViewBottom >= heroBottom && docViewTop <= heroBottom) {
        $('.intro').css('position', 'fixed');
        $('.intro').css('width', '100%');
        $('.bolshaya-main-content').css('position', 'relative');
        $('.bolshaya-main-content').css('top', heroBottom);
    } else {
        var s = skrollr.init({forceHeight: false}).destroy();
        $('.intro').removeAttr('style').removeAttr('data-top').removeAttr('data-top-bottom');
        $('.intro').removeClass('skrollable').removeClass('skrollable-after');   
        var s = skrollr.init({forceHeight: false});
    }
}

$(window).ready(turningOnOffParallax);
//$(window).resize(turningOnOffParallax);


