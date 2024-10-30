<?php
define("WEBSITE_URL","http://thatmlmbeat.com/");

function tbpv_get_post_content_fs(){
	if(isset($_POST["func"]) && $_POST['func']=='tbpv_vote_count'){
		$vote_count = $_POST['vote_count'];
		$post_id = $_POST['post_id'];
		$user_id = $_POST['user_id'];
		update_post_meta($post_id, 'tbpv_vote_count', $vote_count);
		
	}
	if(isset($_POST["thatmlmbeat_get_voting_post"])){
		query_posts(array( 'p' => $_POST["thatmlmbeat_get_voting_post"] ));
		if(class_exists('SubscribersMagnetPlugin')){
			global $SubscribersMagnetPlugin;
			remove_action('loop_start', array(&$SubscribersMagnetPlugin, 'sbmgOFATFP'));
			remove_action('loop_end', array(&$SubscribersMagnetPlugin, 'sbmgOFABLP'));
			remove_filter('the_content', array(&$SubscribersMagnetPlugin, 'sbmgOFAWithinPost'));
		}
		if ( have_posts() ){
			while ( have_posts() ){
				include_once("wp-includes/pluggable.php");
				remove_all_actions('loop_start');
				the_post();
				function excerpt_more_beat_fs($excerpt_more){
					return " ...";
				}
				/*remove_all_filters( 'get_the_excerpt' );
				remove_all_filters( 'wp_trim_excerpt' );
				remove_all_filters( 'the_content' );
				add_filter( 'get_the_excerpt', 'wp_trim_excerpt'  );*/
				
				/*remove_filter( 'get_the_excerpt', 'dd_exclude_js_trim_excerpt' );
				remove_filter( 'get_the_excerpt', 'addthis_late_widget', 14 );
				remove_filter( 'get_the_excerpt', 'addthis_display_social_widget_excerpt' );
				remove_filter( 'wp_trim_excerpt', 'addthis_remove_tag', 11, 2 );
				remove_filter( 'the_content', 'addthis_script_to_content' );
				remove_filter( 'the_content', 'addthis_display_social_widget', 15 );*/
				
				remove_all_filters( 'get_the_excerpt' );
				remove_all_filters( 'the_content' );
				remove_all_filters( 'wp_trim_excerpt' );
				remove_all_filters( 'excerpt_length' );
				remove_all_filters( 'excerpt_more' );
				remove_filter( 'the_title', 'at_title_check' );
				
				add_filter( 'get_the_excerpt', 'wp_trim_excerpt'  );
				add_filter( 'the_content', 'capital_P_dangit', 11 );
				add_filter( 'the_content', 'wptexturize'        );
				add_filter( 'the_content', 'convert_smilies'    );
				add_filter( 'the_content', 'convert_chars'      );
				add_filter( 'the_content', 'wpautop'            );
				add_filter( 'the_content', 'shortcode_unautop'  );
				add_filter( 'the_content', 'prepend_attachment' );
				add_filter( 'excerpt_more', 'excerpt_more_beat_fs' );
				echo serialize( array( "title"=>the_title_attribute(array("echo"=>0)), "content"=>get_the_excerpt(), "permalink"=>get_permalink(), "time"=>strtotime(get_the_time("Y-m-d")) ) );
			}
		}
		exit;
	}
}
add_action( 'init', 'tbpv_get_post_content_fs' );

function tbpv_display_iframe_fs(){
	if(isset($_POST['tbpv_javascript']) && $_POST['tbpv_javascript'] == 'display_beat_button'){
		if( is_single() ){
			global $posts;
			if(count($posts) == 1){
				$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
				$thatmlmbeat_affiliate_link = get_option( "thatmlmbeat_affiliate_link" );
				$thatmlmbeat_button_style = get_option( "thatmlmbeat_button_style" );
				$thatmlmbeat_button_style = '1';
				if(isset($_POST['tbpv_javascript_layout']) && ($_POST['tbpv_javascript_layout']=='1' || $_POST['tbpv_javascript_layout']=='2'))
					$thatmlmbeat_button_style = $_POST['tbpv_javascript_layout'];
				if($thatmlmbeat_button_style == '2'){
					$width = 93;
					$height = 23;
				}
				else{
					$width = 60;
					$height = 68;
				}
				$beat_post_id = $posts[0]->ID;
				if( !empty($thatmlmbeat_username) && $beat_post_id!="" ){
					$local_vote_count = get_post_meta($beat_post_id, 'tbpv_vote_count', true);
					$query_vote_count = '';
					if(trim($local_vote_count)=='') {
						$query_vote_count = '&sync_vote_count=1';
						
						$html = '';
						$src = WEBSITE_URL.'top_blog_posts.php?tbpv_id='.$beat_post_id.'&tbpv_username='.urlencode($thatmlmbeat_username).'&tbpv_domain='.urlencode($_SERVER['HTTP_HOST']).'&tbpv_button_style='.$thatmlmbeat_button_style.$query_vote_count;
						if(!empty($thatmlmbeat_affiliate_link))
							$src .= '&tbpv_affiliate='.urlencode($thatmlmbeat_affiliate_link).'&ref='.urlencode($thatmlmbeat_affiliate_link);
						
						$html = '<iframe id="mlmbeat_tbpv" src="'.$src.'" frameborder="0" width="'.$width.'" height="'.$height.'" scrolling="no"></iframe>';
					} else {
						$query_vote_count = '&sync_vote_count=No';
						$src = WEBSITE_URL.'top_blog_posts.php?beat_req=local&tbpv_id='.$beat_post_id.'&tbpv_username='.urlencode($thatmlmbeat_username).'&tbpv_domain='.urlencode($_SERVER['HTTP_HOST']).'&tbpv_button_style='.$thatmlmbeat_button_style.$query_vote_count;
						if(!empty($thatmlmbeat_affiliate_link))
							$src .= '&tbpv_affiliate='.urlencode($thatmlmbeat_affiliate_link).'&ref='.urlencode($thatmlmbeat_affiliate_link);
						$iframe = '<iframe id=\"mlmbeat_tbpv\" src=\"'.$src.'\" frameborder=\"0\" width=\"'.$width.'\" height=\"'.$height.'\" scrolling=\"no\"></iframe>';
						$html = '<iframe id="mlmbeat_tbpv_2" src="#" frameborder="0" width="0" height="0" scrolling="no" style="display:none;"></iframe>
						<div id="local_beat_button_container">';
						if($thatmlmbeat_button_style == '2'){
							$html .= '<div style="margin:0px; padding:0px; float:left;"><input id="top_top_button-'.$beat_post_id.'" type="button" value=" " style="width:58px; height:20px;  background:url('.network_home_url('/wp-content/plugins/mlm-social-buzz/button-beat2-lef.gif').'); border:0px; cursor:pointer;" /></div>
<div style="margin-right:3px;width:23px; height:20px; font-size:16px;line-height:19px; text-align:center; background:url('.network_home_url('/wp-content/plugins/mlm-social-buzz/button-beat2-rig.png').'); float:left; padding-left:7px; font-family:Helvetica Neue;">'.$local_vote_count.'</div>
<div style="clear:both;"></div>';
						} else {
						$html .= '<div style="width:55px; height:33px; font-size:22px; text-align:center; background:url('.network_home_url('/wp-content/plugins/mlm-social-buzz/button-beat1-top.png').'); padding-top:7px;line-height:23px;font-family:Helvetica Neue; ">'.$local_vote_count.'</div>
						<div style="width:55px; margin:0 0 7px 0; padding:0px;"><input id="top_top_button-'.$beat_post_id.'" type="button" style="width:55px; height:25px; background:url('.network_home_url('/wp-content/plugins/mlm-social-buzz/button-beat1-bot.gif').'); border:0px; cursor:pointer;" value=" "></div>';
						}
						$html .= '</div>
						<script>
						jQuery(document).ready(function(){
							jQuery("#top_top_button-'.$beat_post_id.'").click(function(){
								jQuery("#local_beat_button_container").html("'.$iframe.'");
							});
						});
						</script>';
					}
					echo $html;
				}
				else{
					echo "no_display";
				}
			}
			else{
				echo "no_display";
			}
		}
		else{
			echo "no_display";
		}
		exit;
	}
}
add_action( 'template_redirect', 'tbpv_display_iframe_fs', 1 );

function tbpv_display_follow_button(){
	if(isset($_POST['tbpv_javascript']) && $_POST['tbpv_javascript'] == 'display_follow_button'){
			global $posts;
				$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
				$thatmlmbeat_follow_name = get_option( "thatmlmbeat_follow_name" );
				$thatmlmbeat_affiliate_link = get_option( "thatmlmbeat_affiliate_link" );
				$thatmlmbeat_button_style = '1';
				if(isset($_POST['tbpv_javascript_layout']) && ($_POST['tbpv_javascript_layout']=='1' || $_POST['tbpv_javascript_layout']=='2'))
					$thatmlmbeat_button_style = $_POST['tbpv_javascript_layout'];
				/*if($thatmlmbeat_button_style == '2'){
					$width = 93;
					$height = 23;
				}
				else{
					$width = 60;
					$height = 68;
				}*/
				$width = 28;
				$height = 74;
				$beat_post_id = $posts[0]->ID;
				//if( !empty($thatmlmbeat_follow_name) && !empty($thatmlmbeat_username) ){
				if( !empty($thatmlmbeat_username) ){
					$html = '';
					$html = '<iframe id="mlmbeat_tbpv_follow" src="'.WEBSITE_URL.'follow.php?tbpv_username='.urlencode($thatmlmbeat_username).'&tm_follow_name='.urlencode($thatmlmbeat_follow_name).'&tbpv_domain='.urlencode($_SERVER['HTTP_HOST']).'&tbpv_website='.urlencode(home_url('/')).'&tbpv_button_style='.$thatmlmbeat_button_style;
					if(!empty($thatmlmbeat_affiliate_link))
						$html .= '&tbpv_affiliate='.urlencode($thatmlmbeat_affiliate_link).'&ref='.urlencode($thatmlmbeat_affiliate_link);
					$html .= '" frameborder="0" width="'.$width.'" height="'.$height.'" scrolling="no"></iframe>';
					echo $html;
				}
				else{
					echo "no_display";
				}
		exit;
	}
}
add_action( 'template_redirect', 'tbpv_display_follow_button', 1 );

if( isset($_GET["tbpv_id"]) && isset($_GET["tbpv_username"]) && isset($_GET["tbpv_domain"]) && isset($_GET["tbpv_login"]) ){ ?>
	<!--<script type="text/javascript" src="<?php //home_url(); ?>/wp-includes/js/jquery/jquery.js" ></script>-->
	<script type="text/javascript">
		document.domain = '<?php echo $_GET["tbpv_domain"]; ?>';
		function login_popup_fs(){
			location1 = location.href.split("?",1);
			/*document.write('<div style="display:none;"><a id="tbpv_login" href="<?php //echo WEBSITE_URL; ?>wp-voting-login.php?redirect_to='+location1[0]+'&tbpv_affiliate=<?php //echo $_GET["tbpv_affiliate"]; ?>" target="_blank">Login</a></div>');
			document.getElementById('tbpv_login').click();*/
			window.open("<?php echo WEBSITE_URL; ?>wp-voting-login.php?redirect_to="+location1[0]+"&tbpv_affiliate=<?php echo $_GET["tbpv_affiliate"]; ?>","","menubar=0,resizable=1,status=1,toolbar=0,location=0");
		}
		try{
			parent.show_login_form(<?php echo $_GET["tbpv_id"]; ?>, location.href, '<?php echo $_GET["tbpv_affiliate"]; ?>');
		}catch(err){
			login_popup_fs();
		}
		location.href = '<?php echo WEBSITE_URL; ?>top_blog_posts.php?tbpv_id=<?php echo $_GET["tbpv_id"]; ?>&tbpv_username=<?php echo urlencode($_GET["tbpv_username"]); ?>&tbpv_domain=<?php echo urlencode($_GET["tbpv_domain"]); ?>&tbpv_affiliate=<?php echo urlencode($_GET["tbpv_affiliate"]); ?>&tbpv_button_style=<?php echo urlencode($_GET["tbpv_button_style"]); ?>';
	</script>
<?php
	exit;
}

if( isset($_GET["tbpv_username"]) && isset($_GET["tm_follow_name"]) && isset($_GET["tbpv_domain"]) && isset($_GET["tbpv_website"]) && isset($_GET["tm_follow_action"]) && isset($_GET["tbpv_login"]) ){ ?>
	<script type="text/javascript">
		document.domain = '<?php echo $_GET["tbpv_domain"]; ?>';
		function login_popup_follow_button(){
			location1 = location.href.split("?",1);
			/*document.write('<div style="display:none;"><a id="tbpv_login" href="<?php //echo WEBSITE_URL; ?>wp-voting-login.php?redirect_to='+location1[0]+'&tbpv_affiliate=<?php //echo $_GET["tbpv_affiliate"]; ?>" target="_blank">Login</a></div>');
			document.getElementById('tbpv_login').click();*/
			window.open("<?php echo WEBSITE_URL; ?>wp-voting-login.php?redirect_to="+location1[0]+"&tbpv_affiliate=<?php echo $_GET["tbpv_affiliate"]; ?>","","menubar=0,resizable=1,status=1,toolbar=0,location=0");
		}
		try{
			parent.show_login_form_follow(location.href,'<?php echo $_GET["tbpv_affiliate"]; ?>');
		}catch(err){
			login_popup_follow_button();
		}
		location.href = '<?php echo WEBSITE_URL; ?>follow.php?tbpv_username=<?php echo urlencode($_GET["tbpv_username"]); ?>&tm_follow_name=<?php echo urlencode($_GET["tm_follow_name"]); ?>&tbpv_domain=<?php echo urlencode($_GET["tbpv_domain"]); ?>&tbpv_website=<?php echo urlencode($_GET["tbpv_website"]); ?>&tbpv_button_style=<?php echo urlencode($_GET["tbpv_button_style"]); ?>&tm_follow_action=<?php echo urlencode($_GET["tm_follow_action"]); ?>';
	</script>
<?php
	exit;
}

if(isset($_GET["tbpv_action"]) && $_GET["tbpv_action"] == "close_loginbox"){ ?>
	<script type="text/javascript">
		try{
			parent.jQuery.colorbox.close();
		}catch(err){
			window.close();
		}
	</script>
<?php
	exit;
}

function top_blog_posts_add_admin_menu_fs(){
	//if ( !is_site_admin() )
		//return false;
	$perms = WP_NETWORK_ADMIN ? 'manage_network_options' : 'manage_options';
	add_menu_page( 'MLM Social Buzz', 'MLM Social Buzz', $perms, 'beat', 'top_blog_posts_admin_fs', plugin_dir_url( __FILE__ ).'img/mlm_social_buzz.png' );
	add_submenu_page( 'beat', 'thatMLMbeat Settings', 'thatMLMbeat Settings', $perms, "beat", "top_blog_posts_admin_fs" );
}
add_action( 'admin_menu', 'top_blog_posts_add_admin_menu_fs' );

function top_blog_posts_admin_fs(){
	$settings_saved = false;
	if( $_POST && isset($_POST["thatmlmbeat_username"]) ){
		update_option( "thatmlmbeat_username", $_POST["thatmlmbeat_username"] );
		$settings_saved = true;
	}
	if( $_POST && isset($_POST["thatmlmbeat_affiliate_link"]) ){
		update_option( "thatmlmbeat_affiliate_link", $_POST["thatmlmbeat_affiliate_link"] );
		$settings_saved = true;
	}
	if( $_POST && isset($_POST["thatmlmbeat_button_allignment"]) ){
		update_option( "thatmlmbeat_button_allignment", $_POST["thatmlmbeat_button_allignment"] );
		$settings_saved = true;
	}
	if( $_POST && isset($_POST["thatmlmbeat_button_style"]) ){
		update_option( "thatmlmbeat_button_style", $_POST["thatmlmbeat_button_style"] );
		$settings_saved = true;
	}
	if( $_POST && isset($_POST["thatmlmbeat_disable_button"]) ){
		update_option( "thatmlmbeat_disable_button", $_POST["thatmlmbeat_disable_button"] );
		$settings_saved = true;
	}
	else if($_POST){
		update_option( "thatmlmbeat_disable_button", "enable" );
		$settings_saved = true;
	}
	if( $_POST && isset($_POST["thatmlmbeat_follow_name"]) ){
		update_option( "thatmlmbeat_follow_name", $_POST["thatmlmbeat_follow_name"] );
		$settings_saved = true;
	}
	
	$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
	$thatmlmbeat_affiliate_link = get_option( "thatmlmbeat_affiliate_link" );
	$thatmlmbeat_button_allignment = get_option( "thatmlmbeat_button_allignment" );
	$thatmlmbeat_button_style = get_option( "thatmlmbeat_button_style" );
	$thatmlmbeat_disable_button = get_option( "thatmlmbeat_disable_button" );
	$thatmlmbeat_follow_name = get_option( "thatmlmbeat_follow_name" ); ?>
	<div class="wrap">
		<h2>thatMLMbeat Settings</h2>
		<br />
<?php if($settings_saved){ ?>
		<div class="updated below-h2" id="message"><p>Settings saved.</p></div>
<?php } ?>
		<div style="float:left;">
		<form action="" method="post">
		<table class="widefat fixed" cellspacing="0" style="width:700px; table-layout:auto;">
			<thead>
				<tr class="thead">
					<th scope="col" colspan="2">Settings</th>
				</tr>
			</thead>
			<tbody class="list:user user-list">
				<tr>
					<td>thatMLMbeat Username:</td>
					<td><input type="text" name="thatmlmbeat_username" value="<?php echo $thatmlmbeat_username; ?>" />&nbsp;&nbsp; Not a member? <a href="http://thatmlmbeat.com/join/" target="_blank">Signup Free</a></td>
				</tr>
				<tr style="display:none;">
					<td>thatMLMbeat Affiliate ID:</td>
					<td>
						<input type="text" name="thatmlmbeat_affiliate_link" value="<?php echo $thatmlmbeat_affiliate_link; ?>" />
						<div style="font-size:10px; color:#A5A5A5;">Example: Your affiliate link is https://thatmlmbeat.com/get-started/?ap_id=YOURID<br />ONLY enter the last bit, YOURID here</div>
					</td>
				</tr>
				<tr>
					<td>Button alignment: (for home page and single post pages only)</td>
					<td>
						<select name="thatmlmbeat_button_allignment" id="thatmlmbeat_button_allignment" style="width:70px;"><option value="left">Left</option><option value="right">Right</option></select>
		<?php if($thatmlmbeat_button_allignment!=""){ ?>
						<script type="text/javascript">
							document.getElementById("thatmlmbeat_button_allignment").value = '<?php echo $thatmlmbeat_button_allignment; ?>';
						</script>
		<?php } ?>
					</td>
				</tr>
				<tr>
					<td>Button style:</td>
					<td>
						<div><input type="radio" name="thatmlmbeat_button_style" value="1"<?php if($thatmlmbeat_button_style == '1' || empty($thatmlmbeat_button_style)){ ?> checked="checked"<?php } ?> style="vertical-align:top; margin-top:26px;" /> <img src="<?php echo network_home_url('/wp-content/plugins/mlm-social-buzz/beat-1.jpg'); ?>" /></div>
						<div><input type="radio" name="thatmlmbeat_button_style" value="2"<?php if($thatmlmbeat_button_style == '2'){ ?> checked="checked"<?php } ?> style="vertical-align:top; margin-top:3px;" /> <img src="<?php echo network_home_url('/wp-content/plugins/mlm-social-buzz/beat-2.jpg'); ?>" /></div>
					</td>
				</tr>
				<tr>
					<td>Disable automatic display of beat button with post</td>
					<td><input type="checkbox" name="thatmlmbeat_disable_button" value="disable"<?php if($thatmlmbeat_disable_button == 'disable' || $thatmlmbeat_disable_button == ''){ ?> checked="checked"<?php } ?> /></td>
				</tr>
				<tr>
					<td>Javascript code for Beat button</td>
					<td><textarea style="width:480px; height:80px;"><div id="div_beat_button"><script type="text/javascript">jQuery.post(location.href,{tbpv_javascript:'display_beat_button'},function(response){if(response!='no_display' && response.substr(0, 20) == '<iframe id="mlmbeat_'){jQuery('#div_beat_button').html(response);}});</script></div></textarea></td>
				</tr>
				<!--<tr>
					<td>Manual placement code for Beat box</td>
					<td>
						<div><textarea style="width:400px; height:100px;"><div id="div_beat_button"><script type="text/javascript">jQuery.post(location.href,{tbpv_javascript:'display_beat_button'},function(response){if(response!='no_display'){jQuery('#div_beat_button').html(response);}});</script></div></textarea></div>
					</td>
				</tr>-->
				<tr>
					<td></td>
					<td><input type="submit" value="Save" /></td>
				</tr>
			</tbody>
			<!--<thead>
				<tr class="thead">
					<th scope="col" colspan="2">Follow Button Settings</th>
				</tr>
			</thead>
			<tbody class="list:user user-list">
				<tr>
					<td>Name:</td>
					<td><input type="text" name="thatmlmbeat_follow_name" value="<?php //echo $thatmlmbeat_follow_name; ?>" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Save" /></td>
				</tr>
			</tbody>-->
		</table>
		</form>
		</div>
		<div style="float:left; width:345px; margin-left:15px;">
			<table class="widefat fixed" cellspacing="0" style="width:auto; background:white;"><tbody class="list:user user-list"><tr><td style="padding:15px;">

			<div align="center"><span style="font-family: Arial; font-size: 22px; font-weight: bold; color: #25A;">Latest MLM Tips and News Headlines Delivered to You</span></div>

<div align="left"><!-- AWeber Web Form Generator 3.0 -->
<style type="text/css">
#af-form-620433428 .af-body .af-textWrap{width:98%;display:block;float:none;}
#af-form-620433428 .af-body input.text, #af-form-620433428 .af-body textarea{background-color:#FFFFFF;border-color:#CCCCCC;border-width:1px;border-style:solid;color:#DDDDDD;text-decoration:none;font-style:normal;font-weight:normal;font-size:12px;font-family:Verdana, sans-serif;}
#af-form-620433428 .af-body input.text:focus, #af-form-620433428 .af-body textarea:focus{background-color:#FFFFFF;border-color:#EA7B31;border-width:1px;border-style:solid;}
#af-form-620433428 .af-body label.previewLabel{display:block;float:none;text-align:left;width:auto;color:#333333;text-decoration:none;font-style:normal;font-weight:normal;font-size:12px;font-family:Verdana, sans-serif;}
#af-form-620433428 .af-body{padding-bottom:15px;background-repeat:no-repeat;background-position:inherit;background-image:none;color:#333333;font-size:12px;font-family:Verdana, sans-serif;}
#af-form-620433428 .af-quirksMode{padding-right:15px;padding-left:15px;}
#af-form-620433428 .af-standards .af-element{padding-right:15px;padding-left:15px;}
#af-form-620433428 .buttonContainer input.submit{background-image:url("http://forms.aweber.com/images/auto/gradient/button/07c.png");background-position:top left;background-repeat:repeat-x;background-color:#0057ac;border:1px solid #0057ac;color:#FFFFFF;text-decoration:none;font-style:normal;font-weight:normal;font-size:14px;font-family:Verdana, sans-serif;}
#af-form-620433428 .buttonContainer input.submit{width:auto;}
#af-form-620433428 .buttonContainer{text-align:center;}
#af-form-620433428 button,#af-form-620433428 input,#af-form-620433428 submit,#af-form-620433428 textarea,#af-form-620433428 select,#af-form-620433428 label,#af-form-620433428 optgroup,#af-form-620433428 option{float:none;position:static;margin:0;}
#af-form-620433428 div{margin:0;}
#af-form-620433428 form,#af-form-620433428 textarea,.af-form-wrapper,.af-form-close-button,#af-form-620433428 img{float:none;color:inherit;position:static;background-color:none;border:none;margin:0;padding:0;}
#af-form-620433428 input,#af-form-620433428 button,#af-form-620433428 textarea,#af-form-620433428 select{font-size:100%;}
#af-form-620433428 select,#af-form-620433428 label,#af-form-620433428 optgroup,#af-form-620433428 option{padding:0;}
#af-form-620433428,#af-form-620433428 .quirksMode{width:225px;}
#af-form-620433428.af-quirksMode{overflow-x:hidden;}
#af-form-620433428{background-color:#FFFFFF;border-color:#000000;border-width:1px;border-style:none;}
#af-form-620433428{display:block;}
#af-form-620433428{overflow:hidden;}
.af-body .af-textWrap{text-align:left;}
.af-body input.image{border:none!important;}
.af-body input.submit,.af-body input.image,.af-form .af-element input.button{float:none!important;}
.af-body input.text{width:100%;float:none;padding:2px!important;}
.af-body.af-standards input.submit{padding:4px 12px;}
.af-clear{clear:both;}
.af-element label{text-align:left;display:block;float:left;}
.af-element{padding:5px 0;}
.af-form-wrapper{text-indent:0;}
.af-form{text-align:left;margin:auto;}
.af-quirksMode .af-element{padding-left:0!important;padding-right:0!important;}
.lbl-right .af-element label{text-align:right;}
body {
}
</style>
<form method="post" class="af-form-wrapper" action="http://www.aweber.com/scripts/addlead.pl" target="_new" >
<div style="display: none;">
<input type="hidden" name="meta_web_form_id" value="620433428" />
<input type="hidden" name="meta_split_id" value="" />
<input type="hidden" name="listname" value="tmb-subscribe" />
<input type="hidden" name="redirect" value="http://thatmlmbeat.com/newsletter-signup-confirmation/" id="redirect_256de38ef7099ae631e6c7f85d20d2ba" />
<input type="hidden" name="meta_redirect_onlist" value="http://thatmlmbeat.com/newsletter-signup-confirmation/" />
<input type="hidden" name="meta_adtracking" value="Subscribe_-_MLM_Social_Buzz" />
<input type="hidden" name="meta_message" value="1" />
<input type="hidden" name="meta_required" value="email" />

<input type="hidden" name="meta_tooltip" value="" />
</div>
<div id="af-form-620433428" class="af-form"><div id="af-body-620433428"  class="af-body af-standards">
<div class="af-element">
<label class="previewLabel" for="awf_field-49609089"></label>
<div class="af-textWrap"><input class="text" id="awf_field-49609089" type="text" name="email" value="" tabindex="500"  />
</div><div class="af-clear"></div>
</div>
<div class="af-element buttonContainer">
<input name="submit" class="submit" type="submit" value="Subscribe" tabindex="501" />
<div class="af-clear"></div>
</div>
</div>
</div>
<div style="display: none;"><img src="http://forms.aweber.com/form/displays.htm?id=bEwMLMzMLEwc" alt="" /></div>
</form>
<script type="text/javascript">
    <!--
    (function() {
        var IE = /*@cc_on!@*/false;
        if (!IE) { return; }
        if (document.compatMode && document.compatMode == 'BackCompat') {
            if (document.getElementById("af-form-620433428")) {
                document.getElementById("af-form-620433428").className = 'af-form af-quirksMode';
            }
            if (document.getElementById("af-body-620433428")) {
                document.getElementById("af-body-620433428").className = "af-body inline af-quirksMode";
            }
            if (document.getElementById("af-header-620433428")) {
                document.getElementById("af-header-620433428").className = "af-header af-quirksMode";
            }
            if (document.getElementById("af-footer-620433428")) {
                document.getElementById("af-footer-620433428").className = "af-footer af-quirksMode";
            }
        }
    })();
    -->
</script>

<!-- /AWeber Web Form Generator 3.0 --></div>

						
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) {return;}
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=175097112568474";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			
			<div class="fb-like" data-href="http://facebook.com/thatmlmbeat" data-send="false" data-width="292" data-show-faces="true"></div>
			
			
			<div id="tweet-button" class="top-sidebar widget-area">
				<a href="https://twitter.com/thatMLMbeat" class="twitter-follow-button" data-show-count="false">Follow @thatMLMbeat</a>
			<script src="//platform.twitter.com/widgets.js" type="text/javascript"></script></script>
			
			</div>
			
			<div id="gplus-one" class="top-sidebar widget-area" align="right">
				<!-- Place this tag where you want the +1 button to render -->
			<g:plusone size="medium" href="http://thatmlmbeat.com"></g:plusone>
			
			<!-- Place this render call where appropriate -->
			<script type="text/javascript">
			  (function() {
				var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
				po.src = 'https://apis.google.com/js/plusone.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();
			</script></div>
			</td></tr></tbody></table>
		</div>
		<div style="clear:both;"></div>
	</div>
<?php
}

//add_action('wp_ajax_tbpv__vote', 'tbpv__vote');
function tbpv_enable_jquery_fs(){
	//not load for admin page
	if (!is_admin()) {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'mlmbeat-voting-colorbox-js', WP_PLUGIN_URL .'/mlm-social-buzz/colorbox/jquery.colorbox-min.js' );
		wp_enqueue_script( 'mlmbeat-voting-js', WP_PLUGIN_URL .'/mlm-social-buzz/general.js?domain='.$_SERVER['HTTP_HOST'] );
		wp_enqueue_style( 'mlmbeat-voting-colorbox-css', WP_PLUGIN_URL .'/mlm-social-buzz/colorbox/colorbox.css');
	}
}
add_action('init', 'tbpv_enable_jquery_fs');

function tbpv_the_content_filter_fs($content){
	global $tbpv_but_dis;
	if(!is_array($tbpv_but_dis))
		$tbpv_but_dis = array();
	//if( !(is_single() || is_home() || is_category() || is_archive() || is_front_page() || is_author()) )
	if( !(is_single() || is_category() || is_archive() || is_author()) )
		return $content;
	//if(is_page() || is_feed() || is_admin())
		//return $content;
	/*Uncomment if excerpts filter added*/
	//if( in_array(get_the_ID(), $tbpv_but_dis) )
		//return $content;
	$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
	$thatmlmbeat_affiliate_link = get_option( "thatmlmbeat_affiliate_link" );
	$thatmlmbeat_button_allignment = get_option( "thatmlmbeat_button_allignment" );
	$thatmlmbeat_button_style = get_option( "thatmlmbeat_button_style" );
	$thatmlmbeat_disable_button = get_option( "thatmlmbeat_disable_button" );
	if(($thatmlmbeat_disable_button == 'disable' || $thatmlmbeat_disable_button == '') && is_single()){
		return $content;
	}
	if($thatmlmbeat_button_allignment=="")
		$thatmlmbeat_button_allignment = "left";
	if(empty($thatmlmbeat_button_style))
		$thatmlmbeat_button_style = '1';
	if($thatmlmbeat_button_style == '2'){
		$width = 93;
		$height = 23;
	}
	else{
		$width = 60;
		$height = 68;
	}
	if( !empty($thatmlmbeat_username) && get_the_ID()!="" ){
		$tbpv_but_dis[] = get_the_ID();
		$local_vote_count = get_post_meta(get_the_ID(), 'tbpv_vote_count', true);
		$query_vote_count = '';
		$html = '';
		$html = '<div style="float:'.$thatmlmbeat_button_allignment.'; height:'.$height.'px;">';
		if(trim($local_vote_count)=='') {
			$query_vote_count = '&sync_vote_count=1';
			$html .= '<iframe id="mlmbeat_tbpv" src="'.WEBSITE_URL.'top_blog_posts.php?tbpv_id='.get_the_ID().'&tbpv_username='.urlencode($thatmlmbeat_username).'&tbpv_domain='.urlencode($_SERVER['HTTP_HOST']).'&tbpv_button_style='.$thatmlmbeat_button_style.$query_vote_count;
			if(!empty($thatmlmbeat_affiliate_link))
				$html .= '&tbpv_affiliate='.urlencode($thatmlmbeat_affiliate_link).'&ref='.urlencode($thatmlmbeat_affiliate_link);
			$html .= '" frameborder="0" width="'.$width.'" height="'.$height.'" scrolling="no"></iframe>';
		} else {
			$beat_post_id = get_the_ID();
			$query_vote_count = '&sync_vote_count=No';
			$src = WEBSITE_URL.'top_blog_posts.php?beat_req=local&tbpv_id='.$beat_post_id.'&tbpv_username='.urlencode($thatmlmbeat_username).'&tbpv_domain='.urlencode($_SERVER['HTTP_HOST']).'&tbpv_button_style='.$thatmlmbeat_button_style.$query_vote_count;
			if(!empty($thatmlmbeat_affiliate_link))
				$src .= '&tbpv_affiliate='.urlencode($thatmlmbeat_affiliate_link).'&ref='.urlencode($thatmlmbeat_affiliate_link);
			$iframe = '<iframe id=\'mlmbeat_tbpv\' src=\''.$src.'\' frameborder=\'0\' width=\''.$width.'\' height=\''.$height.'\' scrolling=\'no\'></iframe>';
			$html .= '<iframe id="mlmbeat_tbpv_2" src="#" frameborder="0" width="0" height="0" scrolling="no" style="display:none;"></iframe>
			<div id="in_local_beat_button_container-'.$beat_post_id.'">';
			if($thatmlmbeat_button_style == '2'){
				$html .= '<div style="margin:0px; padding:0px; float:left;"><input id="in_top_top_button-'.$beat_post_id.'" type="button" value=" " style="width:58px; height:20px;  background:url('.network_home_url('/wp-content/plugins/mlm-social-buzz/button-beat2-lef.gif').'); border:0px; cursor:pointer;" onclick="jQuery(\'#in_local_beat_button_container-'.$beat_post_id.'\').html(\''.$iframe.'\');" /></div>
<div style="margin-right:3px;width:23px; height:20px; font-size:16px;line-height:19px; text-align:center; background:url('.network_home_url('/wp-content/plugins/mlm-social-buzz/button-beat2-rig.png').'); float:left; padding-left:7px; font-family:Helvetica Neue;">'.$local_vote_count.'</div>
<div style="clear:both;"></div>';
			} else {
			$html .= '<div style="width:55px; height:33px; font-size:22px; text-align:center; background:url('.network_home_url('/wp-content/plugins/mlm-social-buzz/button-beat1-top.png').'); padding-top:7px;line-height:23px;font-family:Helvetica Neue; ">'.$local_vote_count.'</div>
			<div style="margin:0 0 7px 0; padding:0px;"><input id="in_top_top_button-'.$beat_post_id.'" type="button" style="width:55px; height:25px; background:url('.network_home_url('/wp-content/plugins/mlm-social-buzz/button-beat1-bot.gif').'); border:0px; cursor:pointer;" value=" " onclick="jQuery(\'#in_local_beat_button_container-'.$beat_post_id.'\').html(\''.$iframe.'\');" /></div>';
			}
			$html .= '</div>
			<script type="text/javascript">
			/*jQuery(document).ready(function(){
				jQuery("#in_top_top_button-'.$beat_post_id.'").click(function(){
					jQuery("#in_local_beat_button_container-'.$beat_post_id.'").html("'.$iframe.'");
				});
			});*/
			</script>';
		}
		$html .= '</div>';
		/*echo '<script type="text/javascript">var tbpv_siteurl = "'.WEBSITE_URL.'";</script>';*/
	}
	return $html.$content;
}
add_filter('the_content', 'tbpv_the_content_filter_fs');
/*function tbpv_remove_filter_fs($content) {
	if(!is_feed()){
		remove_action('the_content', 'tbpv_the_content_filter_fs');
	}
	return $content;
}
add_filter('get_the_excerpt', 'tbpv_remove_filter_fs', 9);*/
//add_filter('the_excerpt', 'tbpv_the_content_filter_fs');

function tbpv_dashboard_widget_function_fs() {
	$rss = @fetch_feed( WEBSITE_URL.'activity/feed/topblogposts/' );
	if ( is_wp_error($rss) ) {
		if ( is_admin() || current_user_can('manage_options') ) {
			echo '<div class="rss-widget"><p>';
			printf(__('<strong>RSS Error</strong>: %s'), $rss->get_error_message());
			echo '</p></div>';
		}
	/*} elseif ( !$rss->get_item_quantity() ) {
		$rss->__destruct();
		unset($rss);
		return false;*/
	} else {
		echo '<div class="rss-widget"><ul>';
		//wp_widget_rss_output( $rss, $widgets['top_blog_posts_dashboard_widget'] );
		$maxitems = $rss->get_item_quantity(5);
		$rss_items = $rss->get_items(0, $maxitems);
		if($maxitems == 0)
			echo '<li>No Posts found.</li>';
		else{
    	foreach( $rss_items as $item ){
				echo '<li><a class="rsswidget" href="'.$item->get_permalink().'" title="Posted '.$item->get_date('j F Y | g:i a').'">'.$item->get_title().'</a><span class="rss-date">'.$item->get_date('F j, Y').'</span><div class="rssSummary">'.$item->get_description().'</div></li>';
			}
		}
		echo '</ul></div>';
		$rss->__destruct();
		unset($rss);
	}
}
function tbpv_add_dashboard_widgets_fs() {
	wp_add_dashboard_widget('top_blog_posts_dashboard_widget', 'thatMLMbeat Top Blog Posts', 'tbpv_dashboard_widget_function_fs');	
}
add_action('wp_dashboard_setup', 'tbpv_add_dashboard_widgets_fs' );



//cubepoints hooks for external blog comments and posts
function tbpv_send_curl_request($url, $query_string) {
	$ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
    $result = curl_exec($ch);
	return $result;
}

add_action('comment_post', 'tbpv_cp_newComment', 10 ,2);
function tbpv_cp_newComment($cid, $status) {
	$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
	if( empty($thatmlmbeat_username) )
		return;
	
	$cdata = get_comment($cid);
	
	if($status == 1) {
		$post = get_post($cdata->comment_post_ID);
		$data  = "cubepoints_type=add_comments";
		$data .= "&post_title=".urlencode($post->post_title);
		$data .= "&post_link=".urlencode(get_permalink($cdata->comment_post_ID));
		$data .= "&comment_link=".urlencode(get_comment_link($cdata));
		$data .= "&comment_author=".urlencode($cdata->comment_author);
		$data .= "&date=".urlencode($cdata->comment_date);
		$data .= "&comment_content=".urlencode($cdata->comment_content);
		$data .= "&tbpv_username=".urlencode($thatmlmbeat_username)."&tbpv_domain=".urlencode($_SERVER['HTTP_HOST']);
		
		tbpv_send_curl_request(WEBSITE_URL.'cubepoints.php',$data);
	}
}

/** Comment approved hook */
add_action('comment_unapproved_to_approved', 'tbpv_cp_commentApprove', 10, 1);
add_action('comment_trash_to_approved', 'tbpv_cp_commentApprove', 10, 1);
add_action('comment_spam_to_approved', 'tbpv_cp_commentApprove', 10, 1);
function tbpv_cp_commentApprove($cdata){
	$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
	if( empty($thatmlmbeat_username) )
		return;
	
	$post = get_post($cdata->comment_post_ID);
	$data  = "cubepoints_type=add_comments";
	$data .= "&post_title=".urlencode($post->post_title);
	$data .= "&post_link=".urlencode(get_permalink($cdata->comment_post_ID));
	$data .= "&comment_link=".urlencode(get_comment_link($cdata));
	$data .= "&comment_author=".urlencode($cdata->comment_author);
	$data .= "&date=".urlencode($cdata->comment_date);
	$data .= "&comment_content=".urlencode($cdata->comment_content);
	$data .= "&tbpv_username=".urlencode($thatmlmbeat_username)."&tbpv_domain=".urlencode($_SERVER['HTTP_HOST']);
	
	tbpv_send_curl_request(WEBSITE_URL.'cubepoints.php',$data);
}

/** Comment unapproved hook */
add_action('comment_approved_to_unapproved', 'tbpv_cp_commentUnapprove', 10, 1);
add_action('comment_approved_to_trash', 'tbpv_cp_commentUnapprove', 10, 1);
add_action('comment_approved_to_spam', 'tbpv_cp_commentUnapprove', 10, 1);
function tbpv_cp_commentUnapprove($cdata){
	$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
	if( empty($thatmlmbeat_username) )
		return;
	
	$post = get_post($cdata->comment_post_ID);
	$data  = "cubepoints_type=remove_comments";
	$data .= "&post_title=".urlencode($post->post_title);
	$data .= "&post_link=".urlencode(get_permalink($cdata->comment_post_ID));
	$data .= "&comment_link=".urlencode(get_comment_link($cdata));
	$data .= "&comment_author=".urlencode($cdata->comment_author);
	$data .= "&date=".urlencode($cdata->comment_date);
	$data .= "&comment_content=".urlencode($cdata->comment_content);
	$data .= "&tbpv_username=".urlencode($thatmlmbeat_username)."&tbpv_domain=".urlencode($_SERVER['HTTP_HOST']);
	
	$removed = tbpv_send_curl_request(WEBSITE_URL.'cubepoints.php',$data);
}

/** Post hook */
add_action('publish_post', 'tbpv_cp_newPost');
function tbpv_cp_newPost($pid) {
	$thatmlmbeat_username = get_option( "thatmlmbeat_username" );
	if( empty($thatmlmbeat_username) )
		return;
	
	$post = get_post($pid);
	$uid = $post->post_author;
	$user = get_userdata( $uid );
	
	if($post->post_date == $post->post_modified) {
		$data  = "cubepoints_type=publish_post";
		$data .= "&post_id=".$pid;
		$data .= "&post_author=".urlencode($user->user_login);
		$data .= "&post_title=".urlencode($post->post_title);
		$data .= "&post_link=".urlencode(get_permalink($pid));
		$data .= "&tbpv_username=".urlencode($thatmlmbeat_username)."&tbpv_domain=".urlencode($_SERVER['HTTP_HOST']);
	
		tbpv_send_curl_request(WEBSITE_URL.'cubepoints.php',$data);
	}
}

function tbpv_set_username_external_blog_for_sub_blogs(){
	global $wpdb, $blog_id;
	$thatmlmbeat_externalblog_set = get_option("thatmlmbeat_externalblog_set");
	$thatmlmbeat_username = get_option("thatmlmbeat_username");
	if(network_site_url() == WEBSITE_URL && $blog_id != 1 && ($thatmlmbeat_externalblog_set!='yes' || empty($thatmlmbeat_username))){
		$user = $wpdb->get_row($wpdb->prepare("SELECT u.ID, u.user_login FROM wp_usermeta um, wp_users u WHERE u.ID=um.user_id AND u.user_status=0 AND um.meta_key='wp_".$blog_id."_capabilities' AND um.meta_value like %s", '%s:13:"administrator";s:1:"1";%'));
		if(!empty($user)){
			$siteurl = get_option('siteurl').'/';
			$external_blog = $wpdb->get_var($wpdb->prepare("SELECT id FROM wp_external_blogs WHERE user_id='".$user->ID."' and siteurl=%s", $siteurl));
			if( empty($external_blog) ){
				$site_title = get_option('blogname');
				$site_description = get_option('blogdescription');
				$site_feedurl = get_bloginfo('rss2_url');//new SimplePie($siteurl);
				//if (!$feed->error()) {
					//$site_feedurl = $feed->feed_url;
					$result = $wpdb->query( $wpdb->prepare( 
							"INSERT INTO wp_external_blogs ( 
								user_id, siteurl, title, description, feedurl
								) VALUES ( 
								%d, %s, %s, %s, %s
							)", 
								$user->ID, $siteurl, $site_title, $site_description, $site_feedurl
							) );
					update_option( "thatmlmbeat_username", $user->user_login );
					update_option( "thatmlmbeat_externalblog_set", "yes" );
				//}
			}
			else{
				update_option( "thatmlmbeat_username", $user->user_login );
				update_option( "thatmlmbeat_externalblog_set", "yes" );
			}
		}
	}
}
add_action( 'init', 'tbpv_set_username_external_blog_for_sub_blogs' );
?>