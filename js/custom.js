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
  
	$("#nav-individual-tab").click(function(){
		$("input.userType").val("1");
	});
	$("#nav-business-customer-tab").click(function(){
		$("input.userType").val("0");
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
  	  resolveHeader();
  });
  
  resolveHeader();
  
  function resolveHeader(){
    var yPos = ( $(window).scrollTop() );
    if(yPos > 50) {
      $("header.header").fadeOut(200);
      $("header.fixed").fadeIn(200);
    } else {
      $("header.header").fadeIn(200);
      $("header.fixed").fadeOut(200);
    }
  }

  Chocolat(document.querySelectorAll('.chocolat-parent .chocolat-image'), {})

  $("#date input,#date-from input,#date-to input").datetimepicker({
      pickTime: false,
      minView: 2,
      language:'cs',
      minuteStep: 30,
      format: 'dd/mm/yyyy',
      autoclose: true,
      startDate: '+0d',
      todayHighlight: true,
  });


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

  $('.services-carousel').slick({
    centerMode: true,
    slidesToShow: 3,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          arrows: true,
          centerMode: true,
          centerPadding: '20px',
          slidesToShow: 3
        }
      },
      {
        breakpoint: 580,
        settings: {
          arrows: true,
          centerMode: true,
          slidesToShow: 2
        }
      },
      {
        breakpoint: 390,
        settings: {
          arrows: true,
          centerMode: true,
          slidesToShow: 1
        }
      }
    ]
  });

  $("button#open-menu").click(function(){
    $("div.mobile-navigation-open").fadeToggle("fast", "linear");
  });

  $("button#close-menu").click(function(){
    $("div.mobile-navigation-open").fadeOut("fast", "linear");
  });

});