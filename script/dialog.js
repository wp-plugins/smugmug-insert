var loading_text = "<div class='loader'>&nbsp;</div>";
jQuery(function(){
	jQuery('#navigation').data('selected_user','');
	jQuery('#navigation').data('selected_album','');
});

function getAlbumsByUser(userid){
	jQuery('#navigation').data('selected_user',userid);
	jQuery('#navigation').data('selected_album',''); 
	jQuery('#accounts').hide();
	jQuery.getJSON(jQuery('#accounts').data('feedUrl')+'?action=list&username='+userid,
	  function(data) {
		jQuery('#images').html('');
		jQuery.each(data, function(i,value){
		  jQuery('#images').append(drow_album_image(userid,value));
		});
	  });
}

function drow_album_image(userid,value){
	var onClick = "getImagesByAlbum(\'"+userid+"\', \'"+value.albumId+"\')";
	var markup = ""+
		"<div class='album_item'>"+
		"<div class='container'>"+
		"<div class='thumb'>"+
		"<a href='javascript:void(0);' onClick=\""+onClick+"\"><img src='"+value.thumbUrl+"' /></a>"+
		"</div>"+
		"<div class='description'>"+
		"<span class='name'><strong>Name:</strong> <span>"+value.albumName+"</span></span>"+
		"<span class='created'><strong>Created:</strong> <span>"+value.date+"</span></span>"+
		"<input type='button' class='button-secondary' value='Open' onClick=\""+onClick+"\" />"+
		"</div>"+
		"</div>"+
		"</div>";
	return markup;
}

function getImagesByAlbum(userid, albumid, paging){
	jQuery('#navigation').data('selected_user',userid);
	jQuery('#navigation').data('selected_album',albumid);
	jQuery('#images').html(loading_text);
	var url = jQuery('#accounts').data('feedUrl')+'?action=album&albumid='+albumid+'&start='+paging;
	url += '&medium='+jQuery('#accounts').data('fullSize')+'&thumb='+jQuery('#accounts').data('thumbSize');
	
	draw_navigation_bar();
	if (!paging) {
		paging = "";
	}
	jQuery.getJSON(url,
	  function(data) {
		jQuery('#images').html('');
		jQuery.each(data.items, function(i,value){
		  jQuery('#images').append(drow_image(value));
		});
		if (data.previous!=null) {
			jQuery('#navigation').append("<input type='button' class='button-primary' value='Previous' onclick=\"getImagesByAlbum('"+userid+"', '"+albumid+"', '"+data.previous+"')\" />");
		}
		if (data.next!=null) {
			jQuery('#navigation').append("<input type='button' class='button-primary' value='Next' onclick=\"getImagesByAlbum('"+userid+"', '"+albumid+"', '"+data.next+"')\" />");
		}
	  });
}

function drow_image(data){
	var markup = ""+
	"<div class='image_item'>"+
	"<a href='javascript:void(0);' onclick=\"add_photo(\'"+data.id+"\',\'"+data.thumbUrl+"\',\'"+data.mediumUrl+"\')\">"+
	"<img id='img_"+data.id+"' class='insert_image' src='"+data.thumbUrl+"' />"+
	"</a>"+
	"</div>";
	return markup;
}

function draw_navigation_bar(){
	jQuery('#navigation').html('');
	var result = '';
	var selected_user = jQuery('#navigation').data('selected_user');
	var selected_album = jQuery('#navigation').data('selected_album');
	if (selected_user.length){
		result += "<input type='button' class='button-primary' value='Change user' onclick=\"change_user()\"/>";
	}
	if (selected_album.length){
		result += "<input type='button' class='button-secondary' value='Back to "+selected_user+"' onclick=\"getAlbumsByUser(\'"+selected_user+"\')\"/>";
	}

	if (result.length){
		jQuery('#navigation').append(result);
		jQuery('#navigation').show();
	}
	else{
		jQuery('#navigation').hide();
	}
}


function add_photo(imageid,thumbnail_src,full_src){
	var insert_text = '<a href="' + full_src + '">' +
	  '<img src="' + thumbnail_src + '" class="'+jQuery('#accounts').data('cssClass')+'"/></a>';
	
	jQuery('#img_'+imageid).fadeTo("slow", 0.33); 
	jQuery('#img_'+imageid).css("border-color", '#AF2D00');
	insert_in_editor(insert_text);
}

function insert_in_editor(insert_text){
	var win = window.opener ? window.opener : window.dialogArguments;
	if (!win)
		win = top;
	tinyMCE = win.tinyMCE;
	if (typeof tinyMCE != 'undefined' && tinyMCE.getInstanceById('content')) {
		tinyMCE.selectedInstance.getWin().focus();
		tinyMCE.execCommand('mceInsertContent', false, insert_text);
	} else {
		win.edInsertContent(win.edCanvas, insert_text);
	}
	return false;
}

function change_user(){
	jQuery('#navigation').data('selected_user','');
	jQuery('#navigation').data('selected_album','');
	draw_navigation_bar();
	jQuery('#images').empty();
	jQuery('#accounts').show();
}