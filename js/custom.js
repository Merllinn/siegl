$(document).ready(function() {

	$("div.submenu").hover(function(){
		$(this).parent().toggleClass('active');
	})

	AOS.init({
		once: true
	});

	var loopVideo=document.getElementById("loop");

    $('.video-container').waypoint(function() {                
      loopVideo.play(); 
      }, {
        offset: '50%',
        triggerOnce: true
  });

  // Parallax background
  var scrolled = $(window).scrollTop()
  $('section.products').each(function(index) {
    var imageSrc = $(this).data('image-src')
    var imageHeight = $(this).data('height')
    $(this).css('background-image','url(' + imageSrc + ')')
    $(this).css('height', imageHeight)

    // Adjust the background position.
    var initY = $(this).offset().top
    var height = $(this).height()
    var diff = scrolled - initY
    var ratio = Math.round((diff / height) * 100)
    $(this).css('background-position','center ' + parseInt(-(ratio * 3)) + 'px')
  })

  function isInViewport(node) {
    var rect = node.getBoundingClientRect()
    return (
      (rect.height > 0 || rect.width > 0) &&
      rect.bottom >= 0 &&
      rect.right >= 0 &&
      rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.left <= (window.innerWidth || document.documentElement.clientWidth)
    )
  }

  $(window).scroll(function() {
    var scrolled = $(window).scrollTop()
    $('section.products').each(function(index, element) {
      var initY = $(this).offset().top
      var height = $(this).height()
      var endY  = initY + $(this).height()

      var visible = isInViewport(this)
      if(visible) {
        var diff = scrolled - initY
        var ratio = Math.round((diff / height) * 100)
        $(this).css('background-position','center ' + parseInt(-(ratio * 3)) + 'px')
      }
    })
  })

  Chocolat(document.querySelectorAll('.chocolat-parent .chocolat-image'), {})

});