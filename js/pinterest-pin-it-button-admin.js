
//jQuery doc ready
//See http://digwp.com/2011/09/using-instead-of-jquery-in-wordpress/
jQuery(document).ready(function($) {

	//Enable collapse/expand toggle of admin boxes (like WP dashboard)
	//Don't use 'dashboard' and 'postbox' scripts built-into WP like WordPress SEO
	//plugin as they conflict on other pages
	
	$(".pib-hndle").toggle(function() {
		$(this).next(".inside").slideToggle("fast");
	}, function () {
		$(this).next(".inside").slideToggle("fast");
	});

	$(".pib-handlediv").toggle(function() {
		$(this).next(".pib-hndle").next(".inside").slideToggle("fast");
	}, function() {
		$(this).next(".pib-hndle").next(".inside").slideToggle("fast");
	});

	//Submit mailchimp form with link button
	$("#mc-embedded-subscribe-link").click(function(event) {
		event.preventDefault();
		$("#mc-embedded-subscribe-form").submit();
	});
});
