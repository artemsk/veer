$(function(){
	var heightNew;	
	function updateHeight(){
		heightNew = $(window).height();
		$('.slide').css('height',heightNew);
	};
	$(window).ready(updateHeight);
	$(window).resize(updateHeight); 
});