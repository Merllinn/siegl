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

  $(window).scroll(function() {
    var yPos = ( $(window).scrollTop() );
    if(yPos > 104) {
      $("header.header").hide();
      $("header.fixed").fadeIn( "fast");
    } else {
      $("header.header").show();
      $("header.fixed").fadeOut( "fast" );
    }
  });

  Chocolat(document.querySelectorAll('.chocolat-parent .chocolat-image'), {})

  $(function(){
    $("#date input,#date-from input,#date-to input").datetimepicker({
      pickTime: false,
      minView: 2,
      language:'cs',
      minuteStep: 30,
      format: 'dd/mm/yyyy',
      autoclose: true,
      startDate: '+0d',
    });
  });

  /*
  $('.minus').click(function () {
    var $input = $(this).parent().find('input.amountField');
    var count = parseFloat($input.val()) - 0.5;
    count = count < 1 ? 1 : count;
    $input.val(count);
    $input.change();
    return false;
  });
  
  $('.plus').click(function () {
    var $input = $(this).parent().find('input.amountField');
    $input.val(parseFloat($input.val()) + 0.5);
    $input.change();
    return false;
  });
  */

  $('.differentDelivery').change(function() {
    if ($('.differentDelivery').prop('checked')) {
      $('#billing-information').show( "slow" );
    } else {
      $('#billing-information').hide( "slow" );
    }
  });

  $('.differentDeliveryBussiness').change(function() {
    if ($('.differentDeliveryBussiness').prop('checked')) {
      $('#business-customer-billing-information').show( "slow" );
    } else {
      $('#business-customer-billing-information').hide( "slow" );
    }
  });


});