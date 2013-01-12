<?php
/*
Plugin Name: Simple Login Widget
Plugin URI: http://blog.thinkedem.hu/2009/05/02-wp-plugin-simple-login/
Description: Just a simplified login widget, with only the register and the login/logout functions
Version: 1.2.1
Author: Edem
Author URI: http://www.thinkedem.hu/
License: GPL

This software comes without any warranty, express or otherwise. Noone makes you to use it, so don't
try to blame me if it does wrong to your blog. (However, a description of the bug might help...)

*/

function widget_simple_login_register() {

	if ( !function_exists('register_sidebar_widget') )
		return;


	function widget_simple_login($args) {
		extract($args);
		$options = get_option('widget_simple_login');
		$title = apply_filters('widget_title', $options['title']); 
		switch ($options['redirectto']) {
			case "site":	$redirect = "/"; break;
			case "custom":	$redirect = $options['customredirect']; break;
			case "actual":	$redirect = $_SERVER['REQUEST_URI']; break;
			case "admin":	//default setting
			default:		$redirect = "";
		}
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "	<ul>\n		";
		wp_register();
		echo "\n		<li>";
		wp_loginout($redirect);
		echo "</li>\n	</ul>\n";
		echo $after_widget; 
	}
	
	function widget_simple_login_control() {
		$options = get_option('widget_simple_login');

		if ( isset($_POST["simple_login-submit"]) ) {
			$options['title'] = strip_tags(stripslashes($_POST["simple_login-title"]));
			$options['redirectto'] = $_POST['simple_login-redirectto'];
			$options['customredirect'] = strip_tags(stripslashes($_POST["simple_login-customredirect"]));
			update_option('widget_simple_login', $options);
		}
		if (empty($options['redirectto'])) $options['redirectto'] = 'admin';
		
		$title = attribute_escape($options['title']);
		$customredirect = attribute_escape($options['customredirect']);
?>
			<p><label for="simple_login-title"><?php _e('Title:'); ?></label> <input class="widefat" id="simple_login-title" name="simple_login-title" type="text" value="<?php echo $title; ?>" /></p>
			<p><?php _e('Redirect to:'); ?><br />
				<input class="radio" type="radio" <?php checked($options['redirectto'], 'admin') ?> id="simple_login-redirectto-admin" name="simple_login-redirectto" value="admin" />
				<label for="simple_login-redirectto-admin"><?php _e('Administration panel'); ?></label><br />
				<input class="radio" type="radio" <?php checked($options['redirectto'], 'site') ?> id="simple_login-redirectto-site" name="simple_login-redirectto" value="site" />
				<label for="simple_login-redirectto-site"><?php _e('Site main'); ?></label><br />
				<input class="radio" type="radio" <?php checked($options['redirectto'], 'actual') ?> id="simple_login-redirectto-actual" name="simple_login-redirectto" value="actual" />
				<label for="simple_login-redirectto-actual"><?php _e('Actual page'); ?></label><br />
				<input class="radio" type="radio" <?php checked($options['redirectto'], 'custom') ?> id="simple_login-redirectto-custom" name="simple_login-redirectto" value="custom" />
				<label for="simple_login-redirectto-custom">Custom:</label> <input style="margin-left: 5px;" id="simple_login-customredirect" name="simple_login-customredirect" type="text" value="<?php echo $customredirect; ?>" /><br />
			</p>
			<input type="hidden" id="simple_login-submit" name="simple_login-submit" value="1" />
<?php
	}

	register_sidebar_widget('Simple Login', 'widget_simple_login');
	
	register_widget_control('Simple Login', 'widget_simple_login_control' );
}

add_action('init', widget_simple_login_register);


