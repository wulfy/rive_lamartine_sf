
  /**
  MANAGE PAGE SCROLLING WITH ANCHOR

  @author:ludovic
  **/
  var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
  var is_safari = navigator.userAgent.toLowerCase().indexOf('safari') > -1;

  function scrollToId(id,callback)
  {
      $('html').animate({scrollTop: $(id).offset().top},1000,null,callback);
  }

(function($){

   $( document ).ready(function() {

        $('.action').click(function() {scrollToId($(this).data("target"))});
         $('#scrolltop').click(function() {scrollToId("#top")});
        $('.action').hover(
              function() {
                  $(this).parent().find('.bouton').addClass("move");
                  $(this).parent().find('.hvr-sweep-to-right').addClass("animate");
     
                });
        $('.action').mouseout(
              function() {
                  $(this).parent().find('.bouton').removeClass("move");
                  $(this).parent().find('.hvr-sweep-to-right').removeClass("animate");
                });

        $(document).on( 'scroll', function(){
   
          if ($(window).scrollTop() > 100) {
            $('.scroll-top-wrapper').addClass('show');
          } else {
            $('.scroll-top-wrapper').removeClass('show');
          }
        });
  });

})(jQuery); 
        