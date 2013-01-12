<?php

//Social Media Settings
function espresso_add_social_to_admin_menu($submenu_page_sections, $espresso_manager) {
	global $espresso_premium;
	$submenu_page_sections['social'] = array(
						($espresso_premium),
						'events',
						__('Event Espresso - Social Media Settings', 'event_espresso'),
						__('Social Media', 'event_espresso'),
						apply_filters('filter_hook_espresso_management_capability', 'administrator', $espresso_manager['espresso_manager_social']),
						'espresso_social',
						'espresso_social_config_mnu'
				);
	return $submenu_page_sections;
}

add_filter('filter_hook_espresso_submenus_management_section', 'espresso_add_social_to_admin_menu', 30, 2);

add_action('admin_head', 'espresso_add_social_meta_boxes');

function espresso_add_social_meta_boxes() {
	global $espresso_premium;
	$screen = get_current_screen();
	$screen_id = $screen->id;
	if ($screen_id == 'event-espresso_page_espresso_social') {
		add_meta_box('espresso_news_post_box', __('New @ Event Espresso', 'event_espresso'), 'espresso_news_post_box', $screen_id, 'side');
		add_meta_box('espresso_links_post_box', __('Helpful Plugin Links', 'event_espresso'), 'espresso_links_post_box', $screen_id, 'side');
		if (!$espresso_premium)
			add_meta_box('espresso_sponsors_post_box', __('Sponsors', 'event_espresso'), 'espresso_sponsors_post_box', $screen_id, 'side');
		add_meta_box('espresso_social_facebook_metabox', __('Facebook Settings', 'event_espresso'), 'espresso_social_facebook_metabox', $screen_id, 'normal');
		add_meta_box('espresso_social_twitter_metabox', __('Twitter Settings', 'event_espresso'), 'espresso_social_twitter_metabox', $screen_id, 'normal');
		add_meta_box('espresso_social_google_metabox', __('Google+1  Settings', 'event_espresso'), 'espresso_social_google_metabox', $screen_id, 'normal');
		add_action('admin_footer', 'espresso_admin_page_footer');
	}
}

function espresso_social_config_mnu() {
	global $espresso_twitter, $espresso_facebook, $espresso_google;
	$espresso_social = array();
	/* Facebok */

	function espresso_facebook_updated() {
		echo '<div class="updated fade"><p>' . __('Facebook details saved.', 'event_espresso') . '</p></div>';
	}

	if (isset($_POST['update_facebook'])) {
		$espresso_facebook['espresso_facebook_layout'] = $_POST['espresso_facebook_layout'];
		$espresso_facebook['espresso_facebook_faces'] = $_POST['espresso_facebook_faces'];
		$espresso_facebook['espresso_facebook_action'] = $_POST['espresso_facebook_action'];
		$espresso_facebook['espresso_facebook_font'] = $_POST['espresso_facebook_font'];
		$espresso_facebook['espresso_facebook_colorscheme'] = $_POST['espresso_facebook_colorscheme'];
		$espresso_facebook['espresso_facebook_height'] = $_POST['espresso_facebook_height'];
		$espresso_facebook['espresso_facebook_width'] = $_POST['espresso_facebook_width'];

		update_option('espresso_facebook_settings', $espresso_facebook);
		add_action('admin_notices', 'espresso_facebook_updated');
	}
	$espresso_facebook = get_option('espresso_facebook_settings');
	$espresso_social['facebook'] = $espresso_facebook;

	/* Twitter */

	function espresso_twitter_updated() {
		echo '<div class="updated fade"><p>' . __('Twitter details saved.', 'event_espresso') . '</p></div>';
	}

	if (isset($_POST['update_twitter'])) {
		$espresso_twitter['espresso_twitter_text'] = stripslashes_deep($_POST['espresso_twitter_text']);
		$espresso_twitter['espresso_twitter_username'] = $_POST['espresso_twitter_username'];
		$espresso_twitter['espresso_twitter_count_box'] = $_POST['espresso_twitter_count_box'];
		$espresso_twitter['espresso_twitter_lang'] = $_POST['espresso_twitter_lang'];

		update_option('espresso_twitter_settings', $espresso_twitter);
		add_action('admin_notices', 'espresso_twitter_updated');
	}

	$espresso_twitter = get_option('espresso_twitter_settings');
	$espresso_social['twitter'] = $espresso_twitter;

	/* Google */

	function espresso_google_updated() {
		echo '<div class="updated fade"><p>' . __('Google details saved.', 'event_espresso') . '</p></div>';
	}

	if (isset($_POST['update_google'])) {
		$espresso_google['espresso_google_button_size'] = $_POST['espresso_google_button_size'];
		$espresso_google['espresso_google_url'] = $_POST['espresso_google_url'];
		$espresso_google['espresso_google_annotation'] = $_POST['espresso_google_annotation'];

		update_option('espresso_google_settings', $espresso_google);
		add_action('admin_notices', 'espresso_google_updated');
	}

	$espresso_google = get_option('espresso_google_settings');
	$espresso_social['google'] = $espresso_google;

	ob_start();
	do_meta_boxes('event-espresso_page_espresso_social', 'side', null);
	$sidebar_content = ob_get_clean();
	ob_start();
	do_meta_boxes('event-espresso_page_espresso_social', 'normal', $espresso_social);
	$main_post_content = ob_get_clean();
	?>

	<div id="configure_organization_form" class="wrap meta-box-sortables ui-sortable clearfix">

		<div id="icon-options-event" class="icon32"> </div>
		<h2>
			<?php _e('Event Espresso - Social Media Settings', 'event_espresso'); ?>
		</h2>
		<?php
		if (!espresso_choose_layout($main_post_content, $sidebar_content))
			return FALSE;
		?>
	</div><!-- / #wrap -->
	<?php
}

function espresso_social_facebook_metabox($espresso_social) {
	$espresso_facebook = $espresso_social['facebook'];
	?>
	<div class="padding">
		<form class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<table id="event_espresso-facebook" class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="espresso_facebook_layout">
								<?php _e('Layout Style:', 'event_espresso'); ?>
							</label>
						</th>
						<td>
							<?php
							$values = array(
									array('id' => 'button_count', 'text' => __('Horizontal Button Count', 'event_espresso')),
									array('id' => 'standard', 'text' => __('Horizontal Standard', 'event_espresso')),
									array('id' => 'box_count', 'text' => __('Vertical', 'event_espresso'))
							);
							echo select_input('espresso_facebook_layout', $values, $espresso_facebook['espresso_facebook_layout'], 'id="espresso_facebook_layout"');
							?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="espresso_facebook_faces">
								<?php _e('Show Faces:', 'event_espresso'); ?>
							</label>
						</th>
						<td>
							<?php
							$values = array(
									array('id' => 'true', 'text' => __('Yes', 'event_espresso')),
									array('id' => 'false', 'text' => __('No', 'event_espresso'))
							);
							echo select_input('espresso_facebook_faces', $values, $espresso_facebook['espresso_facebook_faces'], 'id="espresso_facebook_faces"');
							?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="espresso_facebook_font">
								<?php _e('Font:', 'event_espresso'); ?>
							</label>
						</th>
						<td>
							<?php
							$values = array(
									array('id' => 'arial', 'text' => __('arial', 'event_espresso')),
									array('id' => 'lucida grande', 'text' => __('lucida grande', 'event_espresso')),
									array('id' => 'segoe ui', 'text' => __('segoe ui', 'event_espresso')),
									array('id' => 'tahoma', 'text' => __('tahoma', 'event_espresso')),
									array('id' => 'trebuchet ms', 'text' => __('trebuchet ms', 'event_espresso')),
									array('id' => 'verdana', 'text' => __('verdana', 'event_espresso'))
							);
							echo select_input('espresso_facebook_font', $values, $espresso_facebook['espresso_facebook_font'], 'id="espresso_facebook_font"');
							?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="espresso_facebook_colorscheme">
								<?php _e('Color Scheme:', 'event_espresso'); ?>
							</label>
						</th>
						<td>
							<?php
							$values = array(
									array('id' => 'light', 'text' => __('Light', 'event_espresso')),
									array('id' => 'dark', 'text' => __('Dark', 'event_espresso'))
							);
							echo select_input('espresso_facebook_colorscheme', $values, $espresso_facebook['espresso_facebook_colorscheme'], 'id="espresso_facebook_colorscheme"');
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<p>
				<input type="hidden" name="update_facebook" value="update" />
				<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Facebook Options', 'event_espresso'); ?>" id="save_facebook_settings" />
			</p>
		</form>

	</div><!-- / .padding -->
	<?php
}

function espresso_social_twitter_metabox($espresso_social) {
	$espresso_twitter = $espresso_social['twitter'];
	?>
	<div class="padding">
		<form class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<table id="event_espresso-facebook" class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="espresso_twitter_username">
								<?php _e('Twitter Username:', 'event_espresso'); ?>
							</label>
						</th>
						<td>
							<input id="espresso_twitter_username" type="text" name="espresso_twitter_username" size="30" maxlength="20" value="<?php echo $espresso_twitter['espresso_twitter_username']; ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<label for="espresso_twitter_count_box">
								<?php _e('Count Box Position:', 'event_espresso'); ?>
							</label>
						</th>
						<td>
							<?php
							$values = array(
									array('id' => 'none', 'text' => __('None', 'event_espresso')),
									array('id' => 'horizontal', 'text' => __('Horizontal', 'event_espresso')),
									array('id' => 'vertical', 'text' => __('Vertical', 'event_espresso'))
							);
							echo select_input('espresso_twitter_count_box', $values, $espresso_twitter['espresso_twitter_count_box'], 'id="espresso_twitter_count_box"');
							?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="espresso_twitter_lang">
								<?php _e('The language for the Tweet Button:', 'event_espresso'); ?>
							</label>
						</th>
						<td>
							<?php
							$values = array(
									array('id' => 'en', 'text' => __('English', 'event_espresso')),
									array('id' => 'da', 'text' => __('Danish', 'event_espresso')),
									array('id' => 'dl', 'text' => __('Dutch', 'event_espresso')),
									array('id' => 'fr', 'text' => __('French', 'event_espresso')),
									array('id' => 'de', 'text' => __('German', 'event_espresso')),
									array('id' => 'es', 'text' => __('Spanish', 'event_espresso'))
							);
							echo select_input('espresso_twitter_lang', $values, $espresso_twitter['espresso_twitter_lang'], 'id="espresso_twitter_lang"');
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<p>
				<input type="hidden" name="update_twitter" value="update" />
				<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Twitter Options', 'event_espresso'); ?>" id="save_twitter_settings" />
			</p>
		</form>

	</div><!-- / .padding -->
	<?php
}

function espresso_social_google_metabox ($espresso_social) {
	$espresso_google = $espresso_social['google'];
	?>
	<div class="padding">
		<form class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<table id="event_espresso-facebook" class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="espresso_google_button_size">
								<?php _e('Google Button size:', 'event_espresso'); ?>
							</label>
						</th>
						<td>
							<?php
							$values = array(
									array('id' => 'medium', 'text' => __('Horizontal', 'event_espresso')),
									array('id' => 'tall', 'text' => __('Vertical', 'event_espresso'))
							);
							echo select_input('espresso_google_button_size', $values, $espresso_google['espresso_google_button_size'], 'id="espresso_google_button_size"');
							?>
						</td>
					</tr>
					<tr>
						<th>
							<label for="espresso_google_annotation">
								<?php _e('Google text display:', 'event_espresso'); ?>
							</label>
						</th>
						<td>
							<?php
							$values = array(
									array('id' => 'none', 'text' => __('No Text', 'event_espresso')),
									array('id' => 'inline', 'text' => __('Inline Text', 'event_espresso')),
									array('id' => 'bubble', 'text' => __('In Speech Bubble', 'event_espresso'))
							);
							echo select_input('espresso_google_annotation', $values, $espresso_google['espresso_google_annotation'], 'id="espresso_google_annotation"');
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<p>
				<input type="hidden" name="update_google" value="update" />
				<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Google Options', 'event_espresso'); ?>" id="save_google_settings" />
			</p>
		</form>
	</div><!-- / .padding -->
	<?php
}