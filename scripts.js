$(document).ready(function(){


$('.viewDiv').hover( function(){
	var that = this;                                                                                   
    timeout = setTimeout(function () {
        $(that).addClass('viewActive');
    }, 1000);
}
   
,function(){
	clearTimeout(timeout);
    $('.viewDiv').removeClass('viewActive');
});

(function($) {

  var allPanelsL = $('.accordionL > dd').hide();

  $('.accordionL > dt > a').click(function() {
    allPanelsL.slideUp();
    $(this).parent().next().slideDown();
    return false;
  });

var allPanelsR = $('.accordionR > dd').hide();

  $('.accordionR > dt > a').click(function() {
    allPanelsR.slideUp();
    $(this).parent().next().slideDown();
    return false;
  });

})(jQuery);
})
