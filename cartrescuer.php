<?php
/*
Plugin Name: CartRescuer
Plugin URI: http://cartrescuer.com/app/sci/wordpress.php
Description: This plugin allows you to automatically integrate CartRescuer into your Wordpress site.
Author: CartRescuer
Version: 1.0
Author URI: http://cartrescuers.com/
*/

define('CR_PLUGIN_PATH', plugin_basename(__FILE__));

if (!class_exists('crInsertTrackingCode')) {
	
	class crInsertTrackingCode {
		
		function crInsertTrackingCode() {
			add_action('admin_init', array(&$this, 'crSettings'));
			add_action('admin_menu', array(&$this, 'crAdminMenu'));
			add_action('wp_head', array(&$this, 'crOutputTracking'));
			add_filter('plugin_action_links_'.CR_PLUGIN_PATH, array(&$this, 'crPluginAction'), 10, 2); // WORK ON THIS
		}
		
		function crSettings() {
			register_setting('cr-settings', 'cr-tracking', 'trim');
		}
		
		function crPluginAction($links, $file) {
			if ($file == CR_PLUGIN_PATH) {
				$settings_link = '<a href="' . get_admin_url() . 'options-general.php?page='.CR_PLUGIN_PATH.'">Settings</a>';
				array_unshift($links, $settings_link);
			}
			return $links;
		}
	
		function crAdminMenu() {
			add_submenu_page('options-general.php', 'CartRescuer', 'CartRescuer', 'manage_options', __FILE__, array( &$this, 'crOptions' ));
		}
			
		function crOutputTracking() {
			if (!is_feed() && !is_trackback() && !is_admin() && !is_robots()) {
				$tracking = get_option('cr-tracking', '');
				if ($tracking != '') {
					echo $tracking;
				}
			}
		}
	
		function crOptions() { ?>
        	<style>
				.widefat td, .widefat th {
					padding: 15px;
				}
				.postbox h3 {
					cursor: default !important;
				}
			</style>
        	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        	<script type="text/javascript">
				$(document).ready(function() {
					
					var trackCodeInput = $('#cr-tracking');
					var placeHolder = "\n"+'Example: '+"\n\n";
					placeHolder += '<script type="text/javascript">'+"\n\t";
					placeHolder += 'var cartrescuer_websitecode = \'12345\';'+"\n\t";
					placeHolder += 'var cartrescuer_websitedomain = \'yoursite.com\';'+"\n\t";
					placeHolder += 'var cartrescuer_loginusernamekey = \'username\';'+"\n\t";
                    placeHolder += "<"+'/script>'+"\n";
					placeHolder += '<script type="text/javascript" src="https://tracker-cdn.cartrescuer.com/js/tracking.js">'+"<"+'/script>';
					
					if (trackCodeInput.val()=="" || (trackCodeInput.val().indexOf('Example:') > -1)) {
						trackCodeInput.val(placeHolder).css("color", "#999");
					}
					
					trackCodeInput.focus(function() {
						if (($(this).val() == placeHolder) || ($(this).val().indexOf('Example:') > -1)) {
							trackCodeInput.val("").css("color", "#000");
						}
					});
					
					trackCodeInput.blur(function() {
						if ($(this).val() == "") {
							trackCodeInput.val(placeHolder).css("color", "#999");
						}
					});
				});
			</script>
			<div class="wrap">
            	<?php screen_icon(); ?>
            	<h2>CartRescuer - Settings</h2><br>
            	<div class="postbox">
                    <h3 style="padding:10px;">CartRescuer Settings</h3>
                    <form name="dofollow" action="options.php" method="post">
                    <?php settings_fields('cr-settings'); ?>
                    
                    <table class="widefat">
                    	<tr>
                        	<td colspan="2">
                            	<strong>Don't have a CartRescuer account yet? <a href="http://cartrescuer.com/plan/" target="_blank">Sign Up Now</a></strong>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="cr-tracking">Tracking Code: </label></th>
                            <td><textarea rows="10" cols="110" id="cr-tracking" name="cr-tracking"><?php echo esc_html(get_option('cr-tracking')); ?></textarea><br /><br />Find your tracking code in the Implementation section after <a href="http://cartrescuer.com/loginpage/" target="_blank">logging-in to your CartRescuer account</a>.<br /><br />
                            </td>
                        </tr>
                    </table>
                    <input type="submit" style="margin:10px;" name="Submit" value="Save Settings" />
                    </form>
                </div>
            </div>
        <?php
		}
	}
	$beginCartRescuer = new crInsertTrackingCode();
}
?>