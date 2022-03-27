$(document).ready(function(){

	tinymce.init({
    	selector: "textarea.wysiwyg",
	    theme: "modern",
	    skin: 'pepper-grinder',
     		language : 'cs',
	    plugins: [
	         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
	         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	         "save table contextmenu directionality emoticons template paste textcolor"
	   ],
	   toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
	   toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
	   image_advtab: true ,
	   external_filemanager_path:"/tisk/js/common/filemanager/",
	   filemanager_title:"Správa médií" ,
	   external_plugins: { "filemanager" : "/tisk/js/common/filemanager/plugin.min.js"},
	   content_css: "/tisk/css/bootstrap.css, /tisk/css/custom.css",
	   entity_encoding : "raw",
 	});

});
