$().ready(function(){
    /* Volání AJAXu u všech odkazů s třídou ajax */
    $("a.ajax").live("click", function (event) {
        event.preventDefault();
        $("#load_dialog").show();
        $.get(this.href);
    });

    /* Volání AJAXu u všech odkazů s třídou tabella_ajax */
    $("a.tabella_ajax").live("click", function (event) {
		event.preventDefault();
		if($(this).hasClass("confirmPaid")){
			conf = confirm("Tato funkce je placená. Pokračovat?");
		}
		else{
			conf = true;
		}
		
		if(conf){
        	//$(this).closest("tr").find("a.tabella_ajax").toggle();
        	$.get(this.href);
		}
		else{
			return false;
		}
    });
    
    $('[data-toggle="popover"]').popover({
        container: 'body'
    });

    $("select.select2").select2({ width: "element" });


	//categories realted selects
	$("select.cat1").live("change", function(){
		$('.cat2, .cat3').attr("disabled", "disabled");
		if($(this).val()!=""){
			$.get($(this).data("link"), { "level": 2, "value": $(this).val() }, function(data) {
				$('.cat2').replaceWith(data);
			}, "html");
		}
	});
	$("select.cat2").live("change", function(){
		$('.cat3').attr("disabled", "disabled");
		if($(this).val()!=""){
			$.get($(this).data("link"), { "level": 3, "value": $(this).val() }, function(data) {
				$('.cat3').replaceWith(data);
			}, "html");
		}
	});

    // lightbox galleries and images

    $(".innerMain img").not(".auto").each(function(){
		var img = $(this);
		if(img.hasClass("gallery")){
			var cls = "gallery";
		}
		else{
			var cls = "fullscreen";
		}
		var title = img.attr("title");
		var src = img.attr("src");
		img.wrap($('<a>',{
   			class: cls,
   			href: src,
   			title: title
		}));
    });


    var lbParams = {
			imageLoading: $("body").data("basepath")+'/images/lightbox/lightbox-ico-loading.gif',
			imageBtnClose: $("body").data("basepath")+'/images/lightbox/lightbox-btn-close.gif',
			imageBtnPrev: $("body").data("basepath")+'/images/lightbox/lightbox-btn-prev.gif',
			imageBtnNext: $("body").data("basepath")+'/images/lightbox/lightbox-btn-next.gif',
			txtImage: 'Obrázek',
			txtOf: 'z'
	    };
    var lbParamsSingle = {
			imageLoading: $("body").data("basepath")+'/images/lightbox/lightbox-ico-loading.gif',
			imageBtnClose: $("body").data("basepath")+'/images/lightbox/lightbox-btn-close.gif',
			imageBtnPrev: $("body").data("basepath")+'/images/lightbox/lightbox-btn-prev.gif',
			imageBtnNext: $("body").data("basepath")+'/images/lightbox/lightbox-btn-next.gif',
			txtImage: 'Obrázek',
			txtOf: 'z',
			disableNavigation:true
	    };
    if($('a.gallery').length>0){
	    $('a.gallery').lightBox(lbParams);
    }
    if($('a.fullscreen').length>0){
	    $('a.fullscreen').lightBox(lbParamsSingle);
    }


    //flashDialog
    $("#flashDialog").live("liveEvent", function(){
	    $("#flashDialog").dialog({
	        position: { my: "top", at: "top", of: window },
	        resizable: false,
	        modal: true,
	        buttons: {
	            "OK": function() {
	                $( this ).dialog( "destroy" );
	                $("div#flashDialog").replaceWith("");
	            }
	        }
	    });
    });

    //images swapping
    $(".small-images ul li img").bind("click", function(){
		var big = $(this).data("big");
		var detail = $(this).data("detail");
		var bigRef = $(".big_image a");
		var bigRefImg = $(".big_image a img");
		bigRef.attr("href", big);
		bigRefImg.attr("src", detail);
    });

   $(".colorSwatcher").live("click", function(){
	   if($(this).hasClass("checked")){
	   	$(this).removeClass("checked");
	   }
	   else{
	   	$(this).addClass("checked");
	   }
   });


   //sortable
   $( ".sortable tbody" ).sortable({
		update : function (event, ui) {
			var sortEl = $(this);
			var action = sortEl.data("action");
			$.get(action, {
				'items': sortEl.sortable("toArray", {
					attribute:"data-id"
				})
			});
		}
   }).disableSelection();


   //sortable table
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

   $( "table.sortable tbody" ).sortable({
		helper: fixHelper,
		update : function (event, ui) {
			var sortEl = $(this);
			var action = sortEl.data("action");
			$.get(action, {
				'items': sortEl.sortable("toArray", {
					attribute:"data-id"
				})
			});
		}
   }).disableSelection();

   $(".btn-danger").on("click",function(e){
    var link = this;

    e.preventDefault();

    $("<div>Jste si jistí?</div>").dialog({
        modal: true,
        buttons: {
            "Ano": function() {
                window.location = link.href;
            },
            "Ne": function() {
                $(this).dialog("close");
            }
        }
    });
   });

});
