jQuery(document).ready(function($) {

  $('ul.tabs__caption').on('click', 'li:not(.active)', function() {
    $(this)
      .addClass('active').siblings().removeClass('active')
      .closest('div.tabs').find('div.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
  });

  if ($('a').is('.horisontal')) {
  $('.tabs__gallery').slick({
    dots: true,
    infinite: true,
  slidesToShow: 2,
  slidesToScroll: 2
  });}
  else {
    $('.tabs__gallery').slick({
    dots: true,
    infinite: true,
  slidesToShow: 5,
  slidesToScroll: 5
  });
  }
  
});
