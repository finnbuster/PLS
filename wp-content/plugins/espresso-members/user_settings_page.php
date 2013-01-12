<?php

//============= Event Registration Members Subpage - Settings  =============== //
function event_espresso_member_config_mnu() {
	if (!empty($_POST['update_member_settings']) && $_POST['update_member_settings'] == 'update') {
		$member_options = get_option('events_member_settings');
		$member_options['login_page'] = isset($_POST['login_page']) && !empty($_POST['login_page']) ? $_POST['login_page'] : '';
		$member_options['register_page'] = isset($_POST['register_page']) && !empty($_POST['register_page']) ? $_POST['register_page'] : '';
		$member_options['member_only_all'] = isset($_POST['member_only_all']) && !empty($_POST['member_only_all']) ? $_POST['member_only_all'] : '';
		$member_options['autofilled_editable'] = isset($_POST['autofilled_editable']) && !empty($_POST['autofilled_editable']) ? $_POST['autofilled_editable'] : '';
		update_option('events_member_settings', $member_options);
		echo '<div id="message" class="updated fade"><p><strong>' . __('Member settings saved.', 'event_espresso') . '</strong></p></div>';
	}
	$member_options = get_option('events_member_settings');
	$login_page = empty($member_options['login_page']) ? '' : $member_options['login_page'];
	$register_page = empty($member_options['register_page']) ? '' : $member_options['register_page'];
	$member_only_all = empty($member_options['member_only_all']) ? 'N' : $member_options['member_only_all'];
	$autofilled_editable = empty($member_options['autofilled_editable']) ? 'N' : $member_options['autofilled_editable'];
	?>
	<div id="event_reg_theme" class="wrap">
		<div id="icon-options-event" class="icon32"></div>
		<h2><?php echo _e('Manage Member Settings', 'event_espresso') ?></h2>
		<?php ob_start(); ?>
		<div class="metabox-holder">
			<div class="postbox">
				<h3><?php _e('Member Settings', 'event_espresso'); ?></h3>
				<div class="inside">
					<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
						<ul>
							<li>
								<label><?php _e('Login page (if different from default Wordpress login page): ', 'event_espresso'); ?></label> <input type="text" name="login_page" size="25" <?php echo (isset($login_page) ? 'value="' . $login_page . '"' : "") ?>></li>

							<?php
							if (!get_option('users_can_register')) {
								echo '<li class="updated" style="width:65%">' . __('New user registration is currently closed. If you would like to set a custom user regsistration page, you must enable "Anyone can register" in your Wordpress "<a href="options-general.php">General Settings</a>" page.', 'event_espresso') . '</li>';
							} else {
								?>			<li><label><?php _e('Member registration page (if different from default Wordpress register page): ', 'event_espresso'); ?></label> <input name="register_page" size="25" <?php echo (isset($register_page) ? 'value="' . $register_page . '"' : "") ?>></li>
							<?php } ?>
							<li>
								<label><?php _e('Require login for all events? ', 'event_espresso'); ?></label>
								<?php
								$values = array(
										array('id' => 'N', 'text' => __('No', 'event_espresso')),
										array('id' => 'Y', 'text' => __('Yes', 'event_espresso')));
								echo select_input('member_only_all', $values, $member_only_all);
								?>
							</li>
							<li>
								<label><?php _e('Make autofilled fields editable? ', 'event_espresso'); ?></label>
								<?php
								$values = array(
										array('id' => 'N', 'text' => __('No', 'event_espresso')),
										array('id' => 'Y', 'text' => __('Yes', 'event_espresso')));
								echo select_input('autofilled_editable', $values, $autofilled_editable);
								?>
							</li>
							<li>
								<input type="hidden" name="update_member_settings" value="update">
								<p>
									<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Settings', 'event_espresso'); ?>" id="save_member_settings" />
								</p>
							</li>
						</ul>

					</form>

				</div>
			</div>
		</div>
		<?php
		$main_post_content = ob_get_clean();
		espresso_choose_layout($main_post_content, event_espresso_display_right_column());
		?>
	</div>
	<?php
//============= End Event Registration Members Subpage - Settings  =============== //
}