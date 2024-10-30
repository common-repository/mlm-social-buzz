function show_login_form(id, location, affiliate){
	location = location.split("?",1);
	jQuery.colorbox({href:"http://thatmlmbeat.com/wp-voting-login.php?redirect_to="+location[0]+"&tbpv_affiliate="+affiliate, width:"90%", height:"90%", iframe:true});
}

function show_login_form_follow(location,affiliate){
	location = location.split("?",1);
	jQuery.colorbox({href:"http://thatmlmbeat.com/wp-voting-login.php?redirect_to="+location[0]+"&tbpv_affiliate="+affiliate, width:"90%", height:"90%", iframe:true});
}