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

});