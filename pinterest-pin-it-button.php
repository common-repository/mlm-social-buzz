<?php
//Set global variables

if ( ! defined( 'PIB_PLUGIN_BASENAME' ) )
	define( 'PIB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );


//Plugin install/activation
 
function pib_install() {
	
	//Deactivate plugin if WP version too low
    if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
    }
	
	//Setup default settings
	$pib_options = array(
		'display_home_page' => 1,
		'display_front_page' => 0,
		'display_posts' => 1,
		'display_pages' => 1,
		'display_archives' => 0,
		'display_above_content' => 0,
		'display_below_content' => 1,
		'display_on_post_excerpts' => 0,
		'button_style' => 'user_selects_image',
		'count_layout' => 'none',
        'always_show_count' => 0,
		'custom_css' => '',
		'remove_div' => 0		
	);

	//Save default option values
	add_option( 'pib_options', $pib_options );
	add_option( 'pib_ignore', 'false');
}

register_activation_hook( __FILE__, 'pib_install' );


//Add settings page to admin menu
//Use $page variable to load ONLY for this admin page

function pib_create_menu() {
    $page = add_menu_page( 'Pin It Button Settings', 'Pin It Button', 'manage_options', __FILE__, 'pib_create_settings_page', 
        plugins_url( '/img/pinterest-button-icon-small.png', __FILE__ ) );
    
	add_action('admin_print_styles-' . $page, 'pib_add_admin_css_js');
}

add_action( 'admin_menu', 'pib_create_menu' );


//Add Admin CSS/JS

function pib_add_admin_css_js() {
	wp_enqueue_script( 'jquery' );
	
	wp_enqueue_style( 'pinterest-pin-it-button', plugins_url( '/css/pinterest-pin-it-button-admin.css' , __FILE__ ) );
    wp_enqueue_script( 'pinterest-pin-it-button', plugins_url( '/js/pinterest-pin-it-button-admin.js', __FILE__ ), array( 'jquery' ) );	
}

//Add script and css for Pointer funtionallity

function pib_add_admin_css_js_pointer() {
	wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
	
    add_action( 'admin_print_footer_scripts', 'pib_admin_print_footer_scripts' );
}

add_action( 'admin_enqueue_scripts', 'pib_add_admin_css_js_pointer' );


//Add popup message when plugin installed

function pib_admin_print_footer_scripts() {
    $pib_pointer_content = '<h3>Ready to be Pinned?</h3>';

    $pib_pointer_content .= '<p>' . esc_attr('Congratulations. You have just installed the Pinterest "Pin It" Button Plugin. Now just configure ' .
        'your settings and start getting Pinned!') . '</p>';
     
    $url = admin_url( 'admin.php?page=' . PIB_PLUGIN_BASENAME );
    
    global $pagenow;
    $pib_ignore = get_option('pib_ignore');
    
    if ( 'plugins.php' == $pagenow && $pib_ignore == 'false' ) {
	?>

    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {
	
            $('#menu-plugins').pointer({
                content: '<?php echo $pib_pointer_content; ?>',
                buttons: function( event, t ) {
                    button = jQuery('<a id="pointer-close" class="button-secondary">' + '<?php echo "Close"; ?>' + '</a>');
                    button.bind( 'click.pointer', function() {
                        t.element.pointer('close');
                    });
                    return button;
                },
                position: 'left',
                close: function() { }
        
            }).pointer('open');
          
            jQuery('#pointer-close').after('<a id="pointer-primary" class="button-primary" style="margin-right: 5px;" href="<?php echo esc_attr($url); ?>">' + 
                '<?php echo "Pin It Button Settings"; ?>' + '</a>');
            
            jQuery('#pointer-primary').click( function() {
                <?php update_option('pblock_ignore' , 'true'); ?>
            });
		
            jQuery('#pointer-close').click( function() {
                <?php update_option('pib_ignore' , 'true'); ?>
            });
        });
        //]]>
    </script>

	<?php
	}
}


//Register settings

function pib_register_settings() {
	register_setting( 'pib-settings-group', 'pib_options' );
}

add_action( 'admin_init', 'pib_register_settings' );


//Create settings page

function pib_create_settings_page() {
	//Load options array
	$pib_options = get_option( 'pib_options' );
    $custom_css = trim( $pib_options['custom_css'] );

	?>
        <div class="wrap">
			
            <a href="http://pinterestplugin.com/" target="_blank"><div id="pinterest-button-icon-32" class="icon32"
                style="background: url(<?php echo plugins_url( '/img/pinterest-button-icon-med.png', __FILE__ ); ?>) no-repeat;"><br /></div></a>
            <h2>Pinterest "Pin It" Button Settings</h2>
            
            <div class="metabox-holder">
                <div class="pib-settings postbox-container">
					<div class="meta-box-sortables ui-sortable">
						<?php settings_errors(); //Display status messages after action ("settings saved", errors) ?>
					
						<form method="post" action="options.php">
							<?php settings_fields( 'pib-settings-group' ); ?>							
							
							<div id="button-type" class="postbox pib-postbox">
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>								
								<h3 class="hndle pib-hndle">What style of button would you like to use?</h3>								
								<?php
								
								if ( !isset($pib_options['button_style']) ) {
								 	$pib_options = get_option( 'pib_options' );
									$pib_options['button_style'] = 'user_selects_image';
									update_option( 'pib_options', $pib_options );
								}
								
								?>								 
								<table class="form-table inside">
									<tr valign="top">
										<td>
											<input type="radio" id="user_selects_image"  value="user_selects_image" name="pib_options[button_style]" 
									<?php if ($pib_options['button_style'] == 'user_selects_image' || empty($pib_options['button_style'])) 
									echo 'checked="checked"'; ?> />
											<label for="display_pinit_button"><strong>User selects image</strong> from popup</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input type="radio" id="image_selected" value="image_selected" name="pib_options[button_style]" 
												<?php if ( $pib_options['button_style'] == 'image_selected' ) echo 'checked="checked"'; ?> />
											<label for="display_pinit_count_button">Image is <strong>pre-selected</strong></label>
										</td>
									</tr>
									<?php if ( !isset($pib_options['count_layout']) ) {
										$pib_options = get_option( 'pib_options' );
									 $pib_options['count_layout'] = 'none';
									  update_option( 'pib_options', $pib_options );
									 } ?>
								 
									<tr valign="top">
										<td class="pib-pad-cell-top">
											<label for="pib_pin_count" class="pib-plain-label"><?php _e('Pin Count:'); ?></label>
											<select id="count_layout" name="pib_options[count_layout]">
												<option value="none" <?php if($pib_options['count_layout'] == 'none' || empty($pib_options['count_layout']) ) { echo'selected';} ?>><?php _e('No Count'); ?></option>
												<option value="horizontal" <?php if($pib_options['count_layout'] == 'horizontal') { echo'selected';} ?>><?php _e('Horizontal'); ?></option>
												<option value="vertical" <?php if($pib_options['count_layout'] == 'vertical') { echo'selected';} ?>><?php _e('Vertical'); ?></option>
											</select>
										</td>
									</tr>
									
									<tr valign="top">
										<td>
											<input id="always_show_count" name="pib_options[always_show_count]" type="checkbox" <?php if ( $pib_options['always_show_count'] ) echo 'checked="checked"'; ?> />
											<label for="always_show_count">Always show pin count (even when zero)</label>
										</td>
									</tr>
									<tr valign="top">
										<td class="pib-pad-cell-top">
											To specify the URL to pin, image to pin and/or pin description (other than the defaults), go to the edit screen for those posts and pages
											and scroll to the bottom.
										</td>
									</tr>
									<tr valign="top">
										<td>
                                            The button style setting applies to <strong>all</strong> "Pin It" buttons displayed on the website (widgets and shortcodes included).
										</td>
									</tr>                                
								</table>
								
							</div>
							
							<div id="pib-options" class="postbox pib-postbox">
								 <!--Collapsable-->
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>						
								<h3 class="hndle pib-hndle">What types of pages should the button appear on?</h3>
								
								<table class="form-table inside">
									<tr valign="top">
										<td>
											<input id="display_home_page" name="pib_options[display_home_page]" type="checkbox" 
												<?php if ( $pib_options['display_home_page'] ) echo 'checked="checked"'; ?> />
											<label for="display_home_page">Blog Home Page (or Latest Posts Page)</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_front_page" name="pib_options[display_front_page]" type="checkbox" 
												<?php if ( $pib_options['display_front_page'] ) echo 'checked="checked"'; ?> />
											<label for="display_front_page">Front Page (different from Home Page only if set in Settings > Reading)</label>
										</td>
									</tr>					
									<tr valign="top">
										<td>
											<input id="display_posts" name="pib_options[display_posts]" type="checkbox" 
												<?php if ( $pib_options['display_posts'] ) echo 'checked="checked"'; ?> />
											<label for="display_posts">Individual Posts</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_pages" name="pib_options[display_pages]" type="checkbox" 
												<?php if ( $pib_options['display_pages'] ) echo 'checked="checked"'; ?> />
											<label for="display_pages">WordPress Static "Pages"</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_archives" name="pib_options[display_archives]" type="checkbox" 
												<?php if ( $pib_options['display_archives'] ) echo 'checked="checked"'; ?> />
											<label for="display_archives">Archives (includes Category, Tag, Author and date-based pages)</label>
										</td>
									</tr>
									<tr valign="top">
										<td class="pib-pad-cell-top">
											To hide the "Pin It" button for a specific post, page or category, go to the edit screen for that post, page or category,
											scroll down to the bottom, and uncheck the "Show" checkbox.
										</td>
									</tr>
									<tr valign="top">
										<td>
											Head over to  <a href="<?php echo admin_url( 'widgets.php' ); ?>">Widgets</a> to add a "Pin It" button to your sidebar.
										</td>
									</tr>
								</table>
							</div>
								
							<div id="button-show" class="postbox pib-postbox">
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>								
								<h3 class="hndle pib-hndle">Where on each page should the button appear?</h3>
								
								<table class="form-table inside">
									<tr valign="top">
										<td>
											<input id="display_above_content" name="pib_options[display_above_content]" type="checkbox" <?php if ( $pib_options['display_above_content'] ) echo 'checked="checked"'; ?> />
											<label for="display_above_content">Above Content</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_below_content" name="pib_options[display_below_content]" type="checkbox" <?php if ( $pib_options['display_below_content'] ) echo 'checked="checked"'; ?> />
											<label for="display_below_content">Below Content</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_on_post_excerpts" name="pib_options[display_on_post_excerpts]" type="checkbox" <?php if ( $pib_options['display_on_post_excerpts'] ) echo 'checked="checked"'; ?> />
											<label for="display_on_post_excerpts">On Post Excerpts</label>
										</td>
									</tr>
								</table>
							</div>
							
							<div class="submit">
								<input name="Submit" type="submit" value="Save All Settings" class="button-primary" />
                            </div>							
								
							<div id="style-options" class="postbox pib-postbox">                         
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>								
								<h3 class="hndle pib-hndle">Other CSS and Styling Options</h3>
								
								<table class="form-table inside">
									<tr valign="top">
										<td>
											<label for="custom_css">Custom CSS</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<textarea id="pib-custom-css" name="pib_options[custom_css]" rows="6"><?php echo $custom_css; ?></textarea>
									   </td>
									</tr>
									
									<?php if ( !isset($pib_options['remove_div']) ) {
										$pib_options = get_option( 'pib_options' );
										$pib_options['remove_div'] = 0;
										update_option( 'pib_options', $pib_options );
									 } ?>
									<tr valign="top">
										<td>
											<input id="remove_div" name="pib_options[remove_div]" type="checkbox" <?php if ( $pib_options['remove_div']) echo 'checked="checked"'; ?> />
											<label for="remove_div">Remove div tag surrounding regular button: <code><?php echo htmlentities('<div class="pin-it-btn-wrapper"></div>'); ?></code></label>
										</td>
									</tr>
									
									<tr valign="top">
										<td>
											<a href="http://pinterestplugin.com/pin-it-button-custom-css/" target="_blank">See custom CSS examples</a> aligning the button
											with other social sharing plugins.
										</td>
									</tr>
								</table>
							</div>
							
                            <div class="postbox pib-postbox">
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
								<h3 class="hndle pib-hndle">Shortcode Instructions</h3>
                                
                                <div class="inside">
                                    <p>
                                        Use the shortcode <code>[pinit]</code> to display the button within your content.
                                    </p>
                                    <p>
                                        Use the function <code><?php echo htmlentities('<?php echo do_shortcode(\'[pinit]\'); ?>'); ?></code>
										to display within template or theme files.
                                    </p>
                                    <p><strong>Shortcode parameters</strong></p>
                                    <p>
                                        - count: none (default), horizontal, vertical<br/>
                                        - url: URL of the web page to be pinned (defaults to current post/page URL)<br/>
                                        - image_url: URL of the image to be pinned (defaults to first image in post)<br/>
                                        - description: description of the pin (defaults to post title)<br/>
                                        - float: none (default), left, right<br/>
                                        - remove_div: false (default), true -- if true removes surrounding div tag, which also removes float setting<br/>
                                        - always_show_count: false (default), true -- if true will show count even if zero
                                    </p>
                                    <p><strong>Examples</strong></p>
                                    <p>
                                        <code>[pinit count="horizontal"]</code><br/>
                                        <code>[pinit count="vertical" url="http://www.mysite.com" image_url="http://www.mysite.com/myimage.jpg" 
                                            description="My favorite image!" float="right"]</code><br/>
                                    </p>
                                </div>
                            </div>
							
							<div class="submit">
								<input name="Submit" type="submit" value="Save All Settings" class="button-primary" />
                            </div>							
                            
						</form>
					</div>
                </div>
                
	            <div class="pib-right-column postbox-container">
					<div class="meta-box-sortables ui-sortable">
                        <div id="email-signup">
                            <h4>Like This Plugin?</h4>
                            
                            <p class="large-text">
                                Join the mailing list to be notified when new features and plugins are released.
                            </p>
                            
                            <!-- Begin MailChimp Signup Form -->
                            <div id="mc_embed_signup">
                                <form action="http://pinterestplugin.us1.list-manage.com/subscribe/post?u=bfa8cc8ba2614b0796d33a238&amp;id=80e1043ae4" method="post"
                                    id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
                                    
                                    <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
                                    
                                    <div class="clear">
                                        <input type="submit" value="Sign Up!" name="subscribe" id="mc-embedded-subscribe" class="awesome large red">
                                    </div>
                                </form>
                            </div>
                            <!--End mc_embed_signup-->
                            
                            <p>
                                No spam. Unsubscribe anytime.
                            </p>
                        </div>
						
						<div id="promo-buttons" class="postbox pib-postbox">
							<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle pib-hndle">Spread the Word!</h3>
							
							<div class="inside">
                                <p>Could you do me a <strong>huge</strong> favor and share this plugin with others. Thank you so much!</p>
								
								<table>
									<tr>
										<td><?php echo pib_share_facebook(); ?></td>
									</tr>
									<tr>
										<td><?php echo pib_share_twitter(); ?></td>
									</tr>
									<tr>
										<td><?php echo pib_share_google_plus(); ?></td>
									</tr>
									<tr>
										<td><?php echo pib_share_pinterest(); ?></td>
									</tr>
                                    <tr>
                                        <td>(or <a href="http://wordpress.org/extend/plugins/pinterest-pin-it-button/" target="_blank">rate it on WordPress</a>)</td>
                                    </tr>
								</table>
							</div>
						</div>

						<div class="postbox pib-postbox">
							<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle pib-hndle">Other Links</h3>
							
							<div class="inside">
                                <ul>
                                    <li><a href="http://pinterestplugin.com/" target="_blank">Pinterest Plugin Updates</a> (Official Site)</li>
                                    <li><a href="http://wordpress.org/extend/plugins/pinterest-pin-it-button/faq/" target="_blank">Frequently Asked Questions</a></li>
                                    <li><a href="http://pinterestplugin.com/user-support" target="_blank">User Support &amp; Feature Requests</a></li>
                                </ul>
							</div>
						</div>
						<div class="postbox pib-postbox">
							<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle pib-hndle">More Pinterest Plugins</h3>
							
							<div class="inside">
                                <ul>
                                    <li><a href="http://wordpress.org/extend/plugins/pinterest-follow-button/" target="_blank">Pinterest "Follow" Button</a></li>
                                    <li><a href="http://wordpress.org/extend/plugins/pinterest-block/" target="_blank">Pinterest Block</a></li>
                                </ul>
							</div>
						</div>
                        <!--
						<div id="news-links" class="postbox pib-postbox">
							<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle pib-hndle">News from PinterestPlugin.com</h3>
							
							<div class="inside">
								<? //echo pib_rss_news(); ?>
							</div>
						</div>
                        -->
                    </div>
                </div>
                
			</div>
        </div>
    <?php
}


//Render rss items from pinterestplugin.com
//http://codex.wordpress.org/Function_Reference/fetch_feed

function pib_rss_news() {
	// Get RSS Feed(s)
	include_once(ABSPATH . WPINC . '/feed.php');

	// Get a SimplePie feed object from the specified feed source.
	$rss = fetch_feed('http://pinterestplugin.com/feed/');
	
	if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
		// Figure out how many total items there are, but limit it to 5. 
		$maxitems = $rss->get_item_quantity(5); 

		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items(0, $maxitems); 
	endif;
	?>

	<ul>
		<?php if ($maxitems == 0) echo '<li>No items.</li>';
		else
		// Loop through each feed item and display each item as a hyperlink.
		foreach ( $rss_items as $item ) : ?>
		<li>
			<a href='<?php echo esc_url( $item->get_permalink() ); ?>'
			title='<?php echo 'Posted ' . $item->get_date('j F Y | g:i a'); ?>'
			target="_blank">
			<?php echo esc_html( $item->get_title() ); ?></a>
		</li>
		<?php endforeach; ?>
	</ul>
	
	<?php
}


//Render Facebook Share button
//http://developers.facebook.com/docs/share/
function pib_share_facebook() {
	?>
	
	<a name="fb_share" type="button" share_url="http://pinterestplugin.com/" alt="Share on Facebook"></a> 
	<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
	
	<?php
}


//Render Twitter button
//https://twitter.com/about/resources/buttons
function pib_share_twitter() {
	?>
	
	<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://pinterestplugin.com/" data-text="Check out the Pinterest &quot;Pin It&quot; Button Plugin for WordPress" alt="Tweet this!">Tweet</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>	
	
	<?php
}


//Render Google +1 button
//http://www.google.com/intl/en/webmasters/+1/button/index.html
function pib_share_google_plus() {
	?>
	
	<!-- Place this tag where you want the +1 button to render -->
	<div class="g-plusone" data-size="medium" data-href="http://pinterestplugin.com/"></div>

	<!-- Place this render call where appropriate -->
	<script type="text/javascript">
	  (function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>
	
	<?php
}


//Render Pin It button
//Render in iFrame otherwise it messes up the WP admin left menu

function pib_share_pinterest() {
	?>
	
    <iframe src="<?php echo plugins_url( '/inc/admin-pin-it-button-iframe.html' , __FILE__ ) ?>" scrolling="no" frameborder="0" allowtransparency="true" 
        style="border:none; overflow:hidden; width:90px; height:20px"></iframe>
    
	<?php
}

//Render Facebook Like button
//http://developers.facebook.com/docs/reference/plugins/like/
//TODO Not using since correct image isn't coming up for now
function pib_like_facebook() {
	?>
	
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=144056775628952";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	
	<div class="fb-like" data-href="http://pinterestplugin.com/" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>	
	
	<?php
}


//Add a link to the settings page on the plugins list entry

function pib_plugin_action_links( $links, $file ) {
	if ( $file != PIB_PLUGIN_BASENAME )
		return $links;

	$url = admin_url( 'admin.php?page=' . PIB_PLUGIN_BASENAME );
	$settings_link = '<a href="' . esc_attr( $url ) . '">' . esc_html( __( 'Settings') ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links', 'pib_plugin_action_links', 10, 2 );


//Adds a meta box to the main column on the Post and Page edit screens 

function pib_sharing_add_meta_box() {
	add_meta_box( 'pib_sharing_meta','"Pin It" Button Settings', 'pib_sharing_meta_box_content', 'page', 'advanced', 'high' );
	add_meta_box( 'pib_sharing_meta','"Pin It" Button Settings', 'pib_sharing_meta_box_content', 'post', 'advanced', 'high' );
}

add_action( 'admin_init', 'pib_sharing_add_meta_box' );


//Renders the post/page meta box checkbox html

function pib_sharing_meta_box_content( $post ) {
	$pib_options = get_option( 'pib_options' );
	$pib_sharing_checked = get_post_meta( $post->ID, 'pib_sharing_disabled', 1 );

	if ( empty( $pib_sharing_checked ) || $pib_sharing_checked === false )
		$pib_sharing_checked = ' checked="checked"';
	else
		$pib_sharing_checked = '';		
		
	$pib_url_of_webpage = get_post_meta( $post->ID, 'pib_url_of_webpage', true);
	$pib_url_of_img = get_post_meta( $post->ID, 'pib_url_of_img', true);
	$pib_description = get_post_meta( $post->ID, 'pib_description', true);
	
	$pib_button_style = ( $pib_options['button_style'] == 'user_selects_image' ) ? 'User selects image' : 'Image pre-selected';
	?>

    <p>
        <em>Button style is inherited from setting saved in <a href='<?php echo 'admin.php?page=' . PIB_PLUGIN_BASENAME ?>'>"Pin It" Button Settings</a>.
		Current style: <strong><?php echo $pib_button_style; ?></strong></em>
    </p>
	<p>
		<em>These 3 text fields will be used only if the button style is: <strong>Image pre-selected</strong></em>
	</p>
	<p>
		<table class="form-table inside">
			<tr valign="top">
				<td>
					<label for="pib_url_of_webpage">URL of the web page to be pinned (defaults to current post/page URL):</label><br/>
					<input type="text" id="pib_url_of_webpage"  name="pib_url_of_webpage" value="<?php echo  $pib_url_of_webpage; ?>" class="pib-full-width-textbox"/>
				</td>
			</tr>
			<tr valign="top">
				<td>
					<label for="pib_url_of_img">URL of the image to be pinned (defaults to first image in post):</label><br/>
					<input type="text" id="pib_url_of_img"  name="pib_url_of_img" value="<?php echo  $pib_url_of_img; ?>" class="pib-full-width-textbox"/>
				</td>
			</tr>
			<tr valign="top">
				<td>
					<label for="pib_description">Description of the pin (defaults to post title):</label><br/>
					<input type="text" id="pib_description"  name="pib_description" value="<?php echo  $pib_description; ?>" class="pib-full-width-textbox"/>
				</td>
			</tr>
		</table>
		<input type="hidden" name="pib_count_status_hidden" value="1" />
	</p>	
	
	<p>
		<input name="pib_enable_post_sharing" id="pib_enable_post_sharing" value="1" <?php echo $pib_sharing_checked; ?> type="checkbox" />
		<label for="pib_enable_post_sharing">Show "Pin It" button on this post/page.</label>
		<p class="description">
			If checked displays the button for this post/page (if <strong>Individual Posts</strong> (for posts) or <strong>WordPress Static "Pages"</strong> 
			(for pages) is also checked in <a href='<?php echo 'admin.php?page=' . PIB_PLUGIN_BASENAME ?>'>"Pin It" Button Settings</a>).
            If unchecked the button will <strong>always</strong> be hidden for this post/page.
		</p>
		<input type="hidden" name="pib_sharing_status_hidden" value="1" />
	</p>
		
	<?php	
}


//Saves display option for individual post/page

function pib_sharing_meta_box_save( $post_id ) {
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_id;

	// Record sharing disable
	if ( isset( $_POST['post_type'] ) && ( 'post' == $_POST['post_type'] || 'page' == $_POST['post_type'] ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['pib_sharing_status_hidden'] ) ) {
				if ( !isset( $_POST['pib_enable_post_sharing'] ) ) {
					update_post_meta( $post_id, 'pib_sharing_disabled', 1 );
				}
				else {
					delete_post_meta( $post_id, 'pib_sharing_disabled' );
				}
					
				if ( isset( $_POST['pib_url_of_webpage'] ) && isset( $_POST['pib_url_of_img'] ) && isset( $_POST['pib_description'] )) {
					update_post_meta( $post_id, 'pib_url_of_webpage', $_POST['pib_url_of_webpage'] );
					update_post_meta( $post_id, 'pib_url_of_img', $_POST['pib_url_of_img'] );
					update_post_meta( $post_id, 'pib_description', $_POST['pib_description'] );
				}					
				else {
					delete_post_meta( $post_id, 'pib_url_of_webpage' );
					delete_post_meta( $post_id, 'pib_url_of_img' );
					delete_post_meta( $post_id, 'pib_description' );
				}
			}
		}
	}

	return $post_id;
}

add_action( 'save_post', 'pib_sharing_meta_box_save' );


//Add Public CSS/JS (to Header)

function pib_add_public_css_js() {
	wp_enqueue_style( 'pinterest_pin_it_button', plugins_url( '/css/pinterest-pin-it-button.css' , __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'pib_add_public_css_js' );


//Add Public JS (to Footer)

function pib_add_public_js_footer() {
	$pib_options = get_option( 'pib_options' );
	
	// If option #1 selected (user selects image)
	if ( $pib_options['button_style'] == 'user_selects_image' ) {
        //Save iFrame URL to JS variable
        echo '<script type="text/javascript">' .
            'var iFrameBtnUrl = "' . plugins_url( '/inc/pin-it-button-user-selects-image-iframe.html', __FILE__ ) . '"; ' .
            '</script>' . "\n";
        
        echo '<script type="text/javascript" src="' . plugins_url( '/js/pin-it-button-user-selects-image.js', __FILE__ ) . '"></script>' . "\n";
        echo '<script type="text/javascript" src="' . plugins_url( '/js/pin-it-button-user-selects-image-assets.js', __FILE__ ) . '"></script>' . "\n";
    }
	// If option #2 selected (image pre-selected)
	elseif ( $pib_options['button_style'] == 'image_selected' ) {
        echo '<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>' . "\n";
	}
}

add_action( 'wp_footer', 'pib_add_public_js_footer' );


//Add Custom CSS

function pib_add_custom_css() {
	$pib_options = get_option( 'pib_options' );
    $custom_css = trim( $pib_options['custom_css'] );
    
	if ( $custom_css != '' ) {
        echo "\n" . '<style type="text/css">' . "\n" . $custom_css . "\n" . '</style>' . "\n";
	}
}

add_action( 'wp_head', 'pib_add_custom_css' );


//Function for rendering "Pin It" button base html

function pib_button_base( $postUrl, $imageUrl, $description, $countLayout, $alwaysShowCount ) {
    //Change css class for "user selects image" button style to avoid conflicts
    $pib_options = get_option( 'pib_options' );
    $buttonClass = 'pin-it-button';
    
	if ( $pib_options['button_style'] == 'user_selects_image' ) {
        $buttonClass = 'pin-it-button2';
    }
    
    //HTML from Pinterest Goodies 3/19/2012
    //<a href="http://pinterest.com/pin/create/button/?url=PAGE&media=IMG&description=DESC" class="pin-it-button" count-layout="horizontal">
    //<img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
    
    $btn = '<a href="http://pinterest.com/pin/create/button/?url=' . urlencode($postUrl) . 
        '&media=' . urlencode($imageUrl) . '&description='. urlencode($description) . '" ' .
        'count-layout="' . $countLayout . '" class="' . $buttonClass . '" ' . 
        ( $alwaysShowCount ? 'always-show-count="true"' : '' ) .
        '><img border="0" style="border:0;" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
    
    return $btn;
}


//Button html to render

function pib_button_html($postID) {	
	$pib_url_of_webpage = get_post_meta( $postID, 'pib_url_of_webpage', true);
	$pib_url_of_img = get_post_meta( $postID, 'pib_url_of_img', true );
	$pib_description = get_post_meta( $postID, 'pib_description', true );
	
	$pib_options = get_option( 'pib_options' );
	$countLayout = $pib_options['count_layout'];
	$permalink = get_permalink($postID);
	$title = get_the_title($postID);
	global $post;
	$first_img = '';
	 
	//Get url of img and compare width and height
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];

	if (get_post_meta($postID,'pib_sharing_disabled', 1)) {			
		return "";
	}
	else {	
		//if url_of_webpage, url_of_img or description are not set through pinit admin setting page then set default to post/page URL for the attribute url
        $pib_url_of_webpage = ( empty( $pib_url_of_webpage ) ? $permalink : $pib_url_of_webpage );
        $pib_url_of_img = ( empty( $pib_url_of_img ) ? $first_img : $pib_url_of_img );
        $pib_description = ( empty( $pib_description ) ? $title : $pib_description );
        $pib_always_show_count = (bool)$pib_options['always_show_count'];
        
        $baseBtn = pib_button_base($pib_url_of_webpage, $pib_url_of_img, $pib_description, $countLayout, $pib_always_show_count);
        
		if ( $pib_options['remove_div'] ) {
            return $baseBtn;
        }
		else {
            //Surround with div tag
            return '<div class="pin-it-btn-wrapper">' . $baseBtn . '</div>';
		}
    }
}

//Register shortcode: [pinit url="" image_url="" description="" float="none" remove_div="false" always_show_count="false"]

function pib_button_shortcode_html($attr) {
	$pib_options = get_option( 'pib_options' );
	global $post;
	$permalink = get_permalink($post->ID);
	$title = get_the_title($post->ID);
	$first_img = '';
	 
	//Get url of img and compare width and height
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];
	 
	$attr['url'] = ( empty( $attr['url'] ) ? $permalink : $attr['url'] );
	$attr['image_url'] = ( empty( $attr['image_url']) ? $first_img : $attr['image_url'] );
	$attr['description'] = ( empty( $attr['description'] ) ? $title : $attr['description'] );
	$attr['count'] = ( empty( $attr['count'] ) ? 'none' : $attr['count'] );
	$remove_div_bool = ( $attr['remove_div'] == 'true' );
	$always_show_count_bool = ( $attr['always_show_count'] == 'true' );

	$baseBtn = pib_button_base($attr['url'], $attr['image_url'], $attr['description'], $attr['count'], $always_show_count_bool);

	if ( $remove_div_bool ) {
		return $baseBtn;
	}
	else {
		//Surround with div tag
		$float_class = '';
		
		if ( $attr['float'] == 'left' ) {
			$float_class = 'pib-float-left';
		}
		elseif ( $attr['float'] == 'right' ) {
			$float_class = 'pib-float-right';
		}
	
		return '<div class="pin-it-btn-wrapper-shortcode ' . $float_class . '">' . $baseBtn . '</div>';
	}
}

add_shortcode( 'pinit', 'pib_button_shortcode_html' );


//Render button on pages with regular content

function pib_render_btn( $content ) {
	//Load options array
	$pib_options = get_option( 'pib_options' );
 	global $post;
	$postID = $post->ID;
			
    //Determine if displayed on current page
    if (
        ( is_home() && ( $pib_options['display_home_page'] ) ) ||
        ( is_front_page() && ( $pib_options['display_front_page'] ) ) ||
		( is_single() && ( $pib_options['display_posts'] ) ) ||
        ( is_page() && ( $pib_options['display_pages'] ) && !is_front_page() ) ||
        
        //archive pages besides categories (tag, author, date, etc)
        ( is_archive() && ( $pib_options['display_archives'] ) && 
            ( is_tag() || is_author() || is_date() || is_search() ) 
        )
       ) {
        if ( $pib_options['display_above_content'] ) {
            $content = pib_button_html($postID) . $content;
        }
        if ( $pib_options['display_below_content'] ) {
            $content .= pib_button_html($postID);
        }
    }	
	 	
	//Determine if displayed on Category on the base of category edit Screen Option
	if ( is_archive() && ( $pib_options['display_archives'] ) ) {
		$tag_extra_fields = get_option(PIB_CATEGORY_FIELDS);
		$category_ids = get_all_category_ids();
		foreach($category_ids as $term_id) {
					 
			if($tag_extra_fields[$term_id]['checkbox'] != true) {
						
				if(is_category($term_id)) {	
					if ( $pib_options['display_above_content'] ) {
						$content = pib_button_html($postID) . $content;
					}
							
					if ( $pib_options['display_below_content'] ) {
						$content .= pib_button_html($postID);
					}
				}
			}				
		}
	}
		
	return $content;
}

add_filter( 'the_content', 'pib_render_btn' );


//Render button on pages with excerpts if option checked

function pib_render_btn_excerpt( $content ) {
	//Load options array
	$pib_options = get_option( 'pib_options' );
    global $post;
	$postID = $post->ID;
	
    if ( $pib_options['display_on_post_excerpts'] ) {
        if (
            ( is_home() && ( $pib_options['display_home_page'] ) ) ||
            ( is_front_page() && ( $pib_options['display_front_page'] ) ) 
           
           ) {
            if ( $pib_options['display_above_content'] ) {
                $content = pib_button_html($postID) . $content;
            }
            if ( $pib_options['display_below_content'] ) {
                $content .= pib_button_html($postID);
            }
        }
   
	
		//Determine if displayed on Category on the base of category edit Screen Option
		if( is_archive() && ( $pib_options['display_archives'] ) ) {
				
            $tag_extra_fields = get_option(PIB_CATEGORY_FIELDS);
            $category_ids = get_all_category_ids();
            foreach($category_ids as $term_id) {
                     
                if($tag_extra_fields[$term_id]['checkbox'] != true) {
                            
                    
                    if(is_category($term_id)) {	
                        if ( $pib_options['display_above_content'] ) {
                            $content = pib_button_html($postID) . $content;
                        }
                                
                        if ( $pib_options['display_below_content'] ) {
                            $content .= pib_button_html($postID);
                        }
                    }
                }
            }
		}
	}
	return $content;
}

add_filter( 'the_excerpt', 'pib_render_btn_excerpt' );


//Option name

define('PIB_CATEGORY_FIELDS', 'pib_category_fields_option');


//Add Checkbox to Category Edit Screen 

add_action('edit_category_form_fields', 'pib_category_fields');

function pib_category_fields($tag) {
	$t_id = $tag->term_id;
    $tag_extra_fields = get_option(PIB_CATEGORY_FIELDS);
	
	 	if ( $tag_extra_fields[$t_id]['checkbox'] == true)
			$pib_category_checked = '';
		else
			$pib_category_checked = 'checked="checked"';

    ?>
		
    <table class="form-table">
        <tr class="form-field">
            <th scope="row" valign="top">
            <h3>"Pin It" Button Settings</h3>
            </th>
        </tr>
        <tbody>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="pib_category_field">Show "Pin It" Button</label>
                </th>
                <td>
                    <input name="pib_category_field" id="pib_category_field" type="checkbox" value="true" <?php echo $pib_category_checked; ?>>
                    <p class="description">
                        If checked displays the button for this category (if <strong>Archives</strong> also checked in
                        <a href='<?php echo 'admin.php?page=' . PIB_PLUGIN_BASENAME ?>'>"Pin It" Button Settings</a>).
                        If unchecked the button will <strong>always</strong> be hidden for this category.
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    
    <?php
}


// when the form gets submitted, and the category gets updated (in your case the option will get updated with the values of your custom fields above

add_action('edited_category', 'update_pib_category_fields');

function update_pib_category_fields($term_id) {
    if ( $_POST['taxonomy'] == 'category' ) {
        $tag_extra_fields = get_option(PIB_CATEGORY_FIELDS);
        $tag_extra_fields[$term_id]['checkbox'] = strip_tags($_POST['pib_category_field']);

        if ( $_POST['pib_category_field'] != true ) {
            $tag_extra_fields[$term_id]['checkbox'] = true;
            update_option( PIB_CATEGORY_FIELDS, $tag_extra_fields );
        }
        if ( $_POST['pib_category_field'] == true ) {
            $tag_extra_fields[$term_id]['checkbox'] = "";
            update_option( PIB_CATEGORY_FIELDS, $tag_extra_fields );
        }
    }
}


//Add Pinterest Pin It Button widget

class Pib_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'pib-clearfix', 'description' => __( 'Add a Pinterest "Pin It" button to your sidebar with this widget.') );
		$control_ops = array('width' => 400);  //doesn't use height
		parent::__construct('pib_button', __('Pinterest "Pin It" Button'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);		
		$pib_url_of_webpage_widget = $instance['pib_url_of_webpage_widget'];
		$pib_url_of_img_widget = $instance['pib_url_of_img_widget'];		
		$pib_description_widget = $instance['pib_description_widget'];
		$count_layout = empty( $instance['count_layout'] ) ? 'none' : $instance['count_layout'];
		$float = empty( $instance['float'] ) ? 'none' : $instance['float'];
        $pib_remove_div = (bool)$instance['remove_div'];
        $pib_always_show_count = (bool)$instance['always_show_count'];
        
		$baseBtn = pib_button_base( $pib_url_of_webpage_widget, $pib_url_of_img_widget, $pib_description_widget, $count_layout, $pib_always_show_count );
		
		echo $before_widget;
        
		if ( $title ) {
			echo $before_title . $title . $after_title;
        }
		
		if ( $pib_remove_div ) {
			echo $baseBtn;
		}
		else {
			//Surround with div tag
			$float_class = '';
			
			if ( $float == 'left' ) {
				$float_class = 'pib-float-left';
			}
			elseif ( $float == 'right' ) {
				$float_class = 'pib-float-right';
			}
		
			echo '<div class="pin-it-btn-wrapper-widget ' . $float_class . '">' . $baseBtn . '</div>';
		}
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'count_layout' => 'none', 'title' => '', 
			'pib_count_button_radio' => 'user_selects_image', 'float' => 'none') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['pib_url_of_webpage_widget'] = strip_tags($new_instance['pib_url_of_webpage_widget']);
		$instance['pib_url_of_img_widget'] = strip_tags($new_instance['pib_url_of_img_widget']);
		$instance['pib_description_widget'] = strip_tags($new_instance['pib_description_widget']);		
		$instance['count_layout'] = $new_instance['count_layout'];
		$instance['float'] = $new_instance['float'];
        $instance['remove_div'] = ( $new_instance['remove_div'] ? 1 : 0 );
        $instance['always_show_count'] = ( $new_instance['always_show_count'] ? 1 : 0 );
        
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'count_layout' => 'none', 'title' => '', 
		'pib_count_button_radio' => 'user_selects_image', 'float' => 'none') );
		$title = strip_tags($instance['title']);
		$pib_url_of_webpage_widget = strip_tags($instance['pib_url_of_webpage_widget']);
		$pib_url_of_img_widget = strip_tags($instance['pib_url_of_img_widget']);
		$pib_description_widget = strip_tags($instance['pib_description_widget']);
		$pib_options = get_option('pib_options');
		$pib_button_style_widget = ( $pib_options['button_style'] == 'user_selects_image' ) ? 'User selects image' : 'Image pre-selected';		
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('count_layout'); ?>"><?php _e('Pin Count:'); ?></label> 
			<select name="<?php echo $this->get_field_name('count_layout'); ?>" id="<?php echo $this->get_field_id('count_layout'); ?>">
				<option value="none"<?php selected( $instance['count_layout'], 'none' ); ?>><?php _e('No Count'); ?></option>
				<option value="horizontal"<?php selected( $instance['count_layout'], 'horizontal' ); ?>><?php _e('Horizontal'); ?></option>
				<option value="vertical"<?php selected( $instance['count_layout'], 'vertical' ); ?>><?php _e('Vertical'); ?></option>
			</select>
		</p>
		<p>
			<input class="checkbox" <?php checked($instance['always_show_count'], true) ?> id="<?php echo $this->get_field_id('always_show_count'); ?>" name="<?php echo $this->get_field_name('always_show_count'); ?>" type="checkbox"/>
			<label for="<?php echo $this->get_field_id('always_show_count'); ?>">Always show pin count (even when zero)</label>
		</p>
		<div class="pib-widget-text-fields">
            <p>
                <em>Button style is inherited from setting saved in <a href='<?php echo 'admin.php?page=' . PIB_PLUGIN_BASENAME ?>'>"Pin It" Button Settings</a>.
				Current style: <strong><?php echo $pib_button_style_widget; ?></strong></em>
            </p>
			<p>
				<em>These 3 text fields will be used only if the button style is <strong>"Image pre-selected"</strong></em>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('pib_url_of_webpage_widget'); ?>"><?php _e('URL of the web page to be pinned (required):'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('pib_url_of_webpage_widget'); ?>" name="<?php echo $this->get_field_name('pib_url_of_webpage_widget'); ?>" type="text" value="<?php echo esc_attr($pib_url_of_webpage_widget); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('pib_url_of_img_widget'); ?>"><?php _e('URL of the image to be pinned (required):'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('pib_url_of_img_widget'); ?>" name="<?php echo $this->get_field_name('pib_url_of_img_widget'); ?>" type="text" value="<?php echo esc_attr($pib_url_of_img_widget); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('pib_description_widget'); ?>"><?php _e('Description of the pin (optional):'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('pib_description_widget'); ?>" name="<?php echo $this->get_field_name('pib_description_widget'); ?>" type="text" value="<?php echo esc_attr($pib_description_widget); ?>" />
			</p>
		</div>
		
		<p>
			<label for="<?php echo $this->get_field_id('float'); ?>"><?php _e('Align (float):'); ?></label> 
			<select name="<?php echo $this->get_field_name('float'); ?>" id="<?php echo $this->get_field_id('float'); ?>">
				<option value="none"<?php selected( $instance['float'], 'none' ); ?>><?php _e('none (default)'); ?></option>
				<option value="left"<?php selected( $instance['float'], 'left' ); ?>><?php _e('left'); ?></option>
				<option value="right"<?php selected( $instance['float'], 'right' ); ?>><?php _e('right'); ?></option>
			</select>
		</p>
		<p>
			<input class="checkbox" <?php checked($instance['remove_div'], true) ?> id="<?php echo $this->get_field_id('remove_div'); ?>" name="<?php echo $this->get_field_name('remove_div'); ?>" type="checkbox"/>
			<label for="<?php echo $this->get_field_id('remove_div'); ?>">Remove div tag surrounding this widget button (also removes <strong>float</strong> setting)</label>
		</p>
        <?php
	}
}


// Function that register Pin It Button widget. 

function pib_load_widgets() {
	register_widget( 'Pib_Widget' );
}

//Add function to the widgets_init hook. 
add_action( 'widgets_init', 'pib_load_widgets' );


// JavaScript alert debug function (quick & dirty)

function debugToJs($phpVar) {
	echo '<script type="text/javascript"> (function() { alert("' . $phpVar . '"); })() </script>' . "\n";
	//echo '<script type="text/javascript"> (function() { alert(escape("' . $phpVar . '")); })() </script>' . "\n";
    //echo '<script type="text/javascript"> (function() { console.log("' . esc_attr($phpVar) . '"); })() </script>' . "\n";
}

?>