<?php
//Build the user admin menu
//if (get_option('events_members_active') == 'true') {
add_action('show_user_profile', 'event_espresso_show_extra_profile_fields');
add_action('edit_user_profile', 'event_espresso_show_extra_profile_fields');
//Show the user admin menu in the side menu
add_action('admin_menu', 'add_member_event_espressotration_menus');

function event_espresso_show_extra_profile_fields($user) {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	global $espresso_premium;
	if ($espresso_premium != true)
		return;
	?>

	<h3><?php _e('Events Profile Information', 'event_espresso'); ?></h3>
	<a name="event_espresso_profile" id="event_espresso_profile"></a>
	<table class="form-table">

		<tr>
			<th><label for="event_espresso_address"><?php _e('Address/Street/Number', 'event_espresso'); ?></label></th>

			<td>
				<input type="text" name="event_espresso_address" id="event_espresso_address" value="<?php echo esc_attr(get_the_author_meta('event_espresso_address', $user->ID)); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your Address/Street/Number.', 'event_espresso'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="event_espresso_address2"><?php _e('Address 2', 'event_espresso'); ?></label></th>

			<td>
				<input type="text" name="event_espresso_address2" id="event_espresso_address2" value="<?php echo esc_attr(get_the_author_meta('event_espresso_address2', $user->ID)); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Optional', 'event_espresso'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="event_espresso_city"><?php _e('City/Town/Village', 'event_espresso'); ?></label></th>

			<td>
				<input type="text" name="event_espresso_city" id="event_espresso_city" value="<?php echo esc_attr(get_the_author_meta('event_espresso_city', $user->ID)); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your City/Town/Village.', 'event_espresso'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="event_espresso_state"><?php _e('State/County/Province', 'event_espresso'); ?></label></th>

			<td>
				<input type="text" name="event_espresso_state" id="event_espresso_state" value="<?php echo esc_attr(get_the_author_meta('event_espresso_state', $user->ID)); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your State/County/Province.', 'event_espresso'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="event_espresso_zip"><?php _e('Zip/Postal Code', 'event_espresso'); ?></label></th>

			<td>
				<input type="text" name="event_espresso_zip" id="event_espresso_zip" value="<?php echo esc_attr(get_the_author_meta('event_espresso_zip', $user->ID)); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your Zip/Postal Code.', 'event_espresso'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="event_espresso_country"><?php _e('Country', 'event_espresso'); ?></label></th>

			<td>
				<input type="text" name="event_espresso_country" id="event_espresso_country" value="<?php echo esc_attr(get_the_author_meta('event_espresso_country', $user->ID)); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your Country.', 'event_espresso'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="event_espresso_phone"><?php _e('Phone Number', 'event_espresso'); ?></label></th>

			<td>
				<input type="text" name="event_espresso_phone" id="event_espresso_phone" value="<?php echo esc_attr(get_the_author_meta('event_espresso_phone', $user->ID)); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your Phone Number.', 'event_espresso'); ?></span>
			</td>
		</tr>

	</table>
	<?php
}

add_action('personal_options_update', 'event_espresso_extra_profile_fields');
add_action('edit_user_profile_update', 'event_espresso_extra_profile_fields');

function event_espresso_extra_profile_fields($user_id) {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	if (!current_user_can('edit_user', $user_id)) {
		return false;
	}
	update_user_meta($user_id, 'event_espresso_address', $_POST['event_espresso_address']);
	update_user_meta($user_id, 'event_espresso_address2', $_POST['event_espresso_address2']);
	update_user_meta($user_id, 'event_espresso_city', $_POST['event_espresso_city']);
	update_user_meta($user_id, 'event_espresso_state', $_POST['event_espresso_state']);
	update_user_meta($user_id, 'event_espresso_zip', $_POST['event_espresso_zip']);
	update_user_meta($user_id, 'event_espresso_country', $_POST['event_espresso_country']);
	update_user_meta($user_id, 'event_espresso_phone', $_POST['event_espresso_phone']);
}

//}

function espresso_members_installed() {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	return true;
}

function event_espresso_get_current_user_role() {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	global $espresso_premium;
	if ($espresso_premium != true)
		return;
	global $current_user;
	get_currentuserinfo();
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	return $user_role;
}

function add_member_event_espressotration_menus() {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	global $espresso_premium;
	if ($espresso_premium != true)
		return;
	add_users_page(__('My Events', 'event_espresso'), __('My Events', 'event_espresso'), event_espresso_get_current_user_role(), 'my-events', 'event_espresso_my_events');
}

function event_espresso_user_login() {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	global $espresso_premium;
	if ($espresso_premium != true)
		return;
	$member_options = get_option('events_member_settings');

	//Get the member login page
	if ($member_options['login_page'] != '') {
		$login_page = $member_options['login_page'];
	} else {
		$login_page = get_option('siteurl') . '/wp-login.php';
	}

	//Get the member regsitration page
	if ($member_options['register_page'] != '') {
		$register_page = $member_options['register_page'];
	} else {
		$register_page = get_option('siteurl') . '/wp-login.php?action=register';
	}
	echo '<h3>' . __('You are not logged in.', 'event_espresso') . '</h3>';
	echo '<p>' . __('Before you can reserve a spot, you must register.', 'event_espresso') . '</p>';
	echo '<p>If you are a returning user please <a href="' . $login_page . '?redirect_to=' . urlencode(event_espresso_cur_pageURL()) . '">' . __('Login', 'event_espresso') . '</a></p>';
	if (get_option('users_can_register')) {
		echo '<p>' . __('New users please', 'event_espresso') . ' <a href="' . $register_page . '">' . __('Register', 'event_espresso') . '</a></p>';
	} else {
		_e('Member registration is closed for this site. Please contact the site owner.', 'event_espresso');
	}
}

function event_espresso_member_only($member_only = 'N') {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	global $espresso_premium;
	if ($espresso_premium != true)
		return;
	?>
	<p><?php
	_e('Member only event? ', 'event_espresso');
	$values = array(
			array('id' => 'N', 'text' => __('No', 'event_espresso')),
			array('id' => 'Y', 'text' => __('Yes', 'event_espresso')));
	echo select_input('member_only', $values, $member_only);
	?></p>
	<?php
}

function event_espresso_user_login_link() {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	global $espresso_premium;
	if ($espresso_premium != true)
		return;
	//Get the member login page
	if ($member_options['login_page'] != '') {
		$login_page = $member_options['login_page'];
	} else {
		$login_page = get_option('siteurl') . '/wp-login.php';
	}
	echo '<a href="' . $login_page . '?redirect_to=' . urlencode(event_espresso_cur_pageURL()) . '">' . __('Login', 'event_espresso') . '</a>';
}

//Add the ids of the event, user, and attendee to the db
function event_espresso_add_user_to_event($event_id, $userid, $attendee_id) {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	global $espresso_premium;
	if ($espresso_premium != true)
		return;
	global $wpdb;
	global $bp;
	$user_role = event_espresso_get_current_user_role();
	$sql = "INSERT INTO " . EVENTS_MEMBER_REL_TABLE . "(event_id, user_id, attendee_id, user_role) VALUES ('" . $event_id . "', '" . $userid . "', '" . $attendee_id . "', '" . $user_role . "')";
	$wpdb->query( $sql );

}

/*
  Returns the price of an event for members
 *
 * @params string $date
 */
if (!function_exists('event_espresso_get_price')) {

	function event_espresso_get_price($event_id) {
		do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
		global $espresso_premium;
		if ($espresso_premium != true)
			return;
		$org_options = get_option('events_organization_settings');
		global $wpdb;
		if (is_user_logged_in()) {
			$prices = $wpdb->get_results("SELECT event_cost, member_price FROM " . EVENTS_PRICES_TABLE . " WHERE event_id='" . $event_id . "' ORDER BY id ASC LIMIT 1");
		} else {
			$prices = $wpdb->get_results("SELECT event_cost FROM " . EVENTS_PRICES_TABLE . " WHERE event_id='" . $event_id . "' ORDER BY id ASC LIMIT 1");
		}
		foreach ($prices as $price) {
			if ($wpdb->num_rows == 1) {
				$member_price = empty($price->member_price) ? $price->event_cost : $price->member_price;
				if (empty($member_price)) {
					$event_cost = __('Free Event', 'event_espresso');
				} else {
					$event_cost = $org_options['currency_symbol'] . $member_price;
					$event_cost .= '<input type="hidden"name="event_cost" value="' . $member_price . '">';
				}
			} else if ($wpdb->num_rows == 0) {
				$event_cost = __('Free Event', 'event_espresso');
			}
		}

		return $event_cost;
	}

}

function event_espresso_member_only_pricing($event_id = 'NULL') {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	global $espresso_premium;
	if ($espresso_premium != true)
		return;
	?>
	<fieldset id="members-pricing">
		<legend><?php _e('Member Pricing', 'event_espresso'); ?></legend>
		<?php
		if ($event_id == 0) {
			event_espresso_member_pricing_new();
		} else {
			event_espresso_member_pricing_update($event_id);
		}
		?>
		<p><input class="button" type="button" value="<?php _e('Add A Member Price', 'event_espresso'); ?>" onclick="addMemberPriceInput('dynamicMemberPriceInput');"></p>
	</fieldset>
	<!--</td> <<<---  REMOVED FROM member_functions.php line 254 becuase there was no opening td tag-->
	<?php
}

if (!function_exists('event_espresso_member_pricing_update')) {

	function event_espresso_member_pricing_update($event_id) {
		do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
		global $espresso_premium;
		if ($espresso_premium != true)
			return;
		$org_options = get_option('events_organization_settings');
		global $wpdb;
		$member_price_counter = 1;
		?>
		<ul id="dynamicMemberPriceInput">
			<?php
			$member_prices = $wpdb->get_results("SELECT member_price, member_price_type FROM " . EVENTS_PRICES_TABLE . " WHERE event_id = '" . $event_id . "' ORDER BY id");
			foreach ($member_prices as $member_price) {
				echo '<li><label for="add-member-name-"' . $member_price_counter++ . '">' . __('Name', 'event_espresso') . ' ' . $member_price_counter++ . ':</label><input id="add-member-name-' . $member_price_counter++ . '" size="10"  type="text" name="member_price_type[]" value="' . $member_price->member_price_type . '"> ';
				echo '<label for="add-member-price-' . $member_price_counter++ . '">' . __('Price', 'event_espresso') . ': ' . $org_options['currency_symbol'] . '</label><input id="add-member-price-' . $member_price_counter++ . '" size="5"  type="text" name="member_price[]" value="' . $member_price->member_price . '">';
				echo '<img class="remove-item" title="' . __('Remove this Attendee', 'event_espresso') . '" onclick="this.parentNode.parentNode.removeChild(this.parentNode);" src="' . EVENT_ESPRESSO_PLUGINFULLURL . 'images/icons/remove.gif" alt="' . __('Remove Attendee', 'event_espresso') . '" />';

				echo '</li>';
			}
			?>
		</ul>
		<p>
		<?php _e('(enter 0.00 for free events, enter 2 place decimal i.e. ' . $org_options['currency_symbol'] . '7.00)', 'event_espresso'); ?>
		</p>
		<p><?php _e('<span class="important">Note:</span> A non-member price MUST be entered for each row, even if this is a member only event.', 'event_espresso'); ?></p>


		<script type="text/javascript">

			//Dynamic form fields
			var member_price_counter = '<?php echo $member_price_counter++ ?>';
			function addMemberPriceInput(divName){
				var newdiv = document.createElement('li');
				newdiv.innerHTML = "<label for='add-member-name-" + (member_price_counter) + "'><?php _e('Name', 'event_espresso'); ?> " + (member_price_counter) + ": </label><input id='add-member-name-" + (member_price_counter) + "' type='text' size='10' name='member_price_type[]'><label for='add-member-price-" + (member_price_counter) + "'> <?php _e('Price', 'event_espresso'); ?>: <?php echo $org_options['currency_symbol'] ?></label><input id='add-member-price-" + (member_price_counter) + "' type='text' size='5' name='member_price[]'> <?php echo "<img  class='remove-item' onclick='this.parentNode.parentNode.removeChild(this.parentNode);' title='" . __('Remove this Attendee', 'event_espresso') . "' src='" . EVENT_ESPRESSO_PLUGINFULLURL . "images/icons/remove.gif' alt='" . __('Remove Attendee', 'event_espresso') . "' />" ?>";
				document.getElementById(divName).appendChild(newdiv);
				member_price_counter++;
			}
		</script>
		<?php
	}

}

if (!function_exists('event_espresso_member_pricing_new')) {

	function event_espresso_member_pricing_new() {
		do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
		global $espresso_premium, $org_options;
		if ($espresso_premium != true)
			return;
		$member_price_counter = 1;
		?>
		<ul id="dynamicMemberPriceInput">
			<li>
				<label for="add-member-name-<?php echo $member_price_counter ?>"><?php _e('Name ', 'event_espresso'); ?><?php echo $member_price_counter++ ?>:</label>
				<input size="10" id="add-member-name-<?php echo $member_price_counter ?>" type="text"  name="member_price_type[]">
				<label for="add-member-price-<?php echo $member_price_counter ?>"><?php _e('Price:', 'event_espresso'); ?></label>
				<input size="5" id="add-member-price-<?php echo $member_price_counter ?>" type="text"  name="member_price[]">
				<img class="remove-item" title="<?php echo __('Remove this Attendee', 'event_espresso') ?>" onclick="this.parentNode.parentNode.removeChild(this.parentNode);" src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>images/icons/remove.gif" alt="<?php echo __('Remove Attendee', 'event_espresso') ?>" />
			</li>
		</ul>
		<p>
		<?php _e('(enter 0.00 for free events, enter 2 place decimal i.e. 7.00)', 'event_espresso'); ?>
		</p>
		<p><?php _e('<span class="important">Note:</span> A non-member price MUST be entered, even if this is a member only event.', 'event_espresso'); ?></p>
		<script type="text/javascript">

			//Dynamic form fields
			var member_price_counter = <?php echo $member_price_counter++ ?>;
			function addMemberPriceInput(divName){
				var newdiv = document.createElement('li');
				newdiv.innerHTML = "<label for='add-member-name" + (member_price_counter) + "'><?php _e('Name', 'event_espresso'); ?> " + (member_price_counter) + ": </label><input id='add-member-name-" + (member_price_counter) + "' type='text' size='10' name='member_price_type[]'><label for='add-member-price-" + (member_price_counter) + "'> <?php _e('Price:', 'event_espresso'); ?></label><input id='add-member-price-" + (member_price_counter) + "' type='text' size='5' name='member_price[]'> <?php echo "<img class='remove-item' onclick='this.parentNode.parentNode.removeChild(this.parentNode);' title='" . __('Remove this Attendee', 'event_espresso') . "'  src='" . EVENT_ESPRESSO_PLUGINFULLURL . "images/icons/remove.gif' alt='" . __('Remove Attendee', 'event_espresso') . "' />" ?>";
				document.getElementById(divName).appendChild(newdiv);
				member_price_counter++;
			}
		</script>
		</li>
		<?php
	}

}

//Creates dropdowns if multiple prices are associated with an event
//if (!function_exists('event_espresso_member_price_dropdown')) {

function event_espresso_price_dropdown($event_id, $atts) {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, __LINE__);
	//Attention:
	//This is a copy of a core function. Any changes made here should be added to the core function of the same name
	extract($atts);
	global $wpdb, $org_options, $espresso_premium;

	if ($espresso_premium != true)
		return;

	//Default values
	$html = '';
	$early_bird_message = '';
	$surcharge = '';
	$label = isset($label) && $label != '' ? $label : '<span class="section-title">'.__('Choose an Option: ', 'event_espresso').'</span>';

	//Will make the name an array and put the time id as a key so we know which event this belongs to
	$multi_name_adjust = isset($multi_reg) && $multi_reg == true ? "[$event_id]" : '';

	//Gets the surcharge text
	$surcharge_text = isset($org_options['surcharge_text']) ? $org_options['surcharge_text'] : __('Surcharge', 'event_espresso');

	//Initial price query
	//If the user is looged in, create a special query to get the member price
	if (is_user_logged_in()) {
		$sql = "SELECT id, event_cost, surcharge, surcharge_type, member_price, member_price_type, price_type FROM " . EVENTS_PRICES_TABLE . " WHERE event_id='" . $event_id . "' ORDER BY id ASC";
	} else {
		$sql = "SELECT id, event_cost, surcharge, surcharge_type, price_type FROM " . EVENTS_PRICES_TABLE . " WHERE event_id='" . $event_id . "' ORDER BY id ASC";
	}
	$prices = $wpdb->get_results($sql);

	//If more than one price was added to an event, we need to create a drop down to select the price.
	if ($wpdb->num_rows > 1) {

		//Create the label for the drop down
		$html .= $show_label == 1 ? '<label for="event_cost">' . $label . '</label>' : '';

		//Create a dropdown of prices
		$html .= '<select name="price_option" id="price_option-' . $event_id . '">';

		foreach ($prices as $price) {

           if (is_user_logged_in()) {
 				// member prices
				$member_price = $price->member_price == "" ? $price->event_cost : $price->member_price;
				$member_price_type = $price->member_price_type == "" ? $price->price_type : $price->member_price_type;
            } else {
 				// NON-member prices
				$member_price = $price->event_cost;
				$member_price_type = $price->price_type;
            }

			//Check for Early Registration discount
			if ($early_price_data = early_discount_amount($event_id, $member_price)) {
				$member_price = $early_price_data['event_price'];
				$early_bird_message = __(' Early Pricing', 'event_espresso');
			}

			//Calculate the surcharge
			if ($price->surcharge > 0 && $member_price > 0.00) {
				$surcharge = " + {$org_options['currency_symbol']}{$price->surcharge} " . $surcharge_text;
				if ($price->surcharge_type == 'pct') {
					$surcharge = " + {$price->surcharge}% " . $surcharge_text;
				}
			}

			//Using price ID
			//If the price id was passed to this function, we need need to select that price.
			$selected = isset($current_value) && $current_value == $result->id ? ' selected="selected" ' : '';


			//Create the drop down options
			$html .= '<option ' . $selected . ' value="' . $price->id . '|' . $member_price_type . '">' . $member_price_type . ' (' . $org_options['currency_symbol'] . number_format($member_price, 2, '.', '') . $early_bird_message . ') ' . $surcharge . ' </option>';
		}

		//Create a hidden field so that we know the price dropdown was used
		$html .= '</select><input type="hidden" name="price_select" id="price_select-' . $event_id . '" value="true">';

		//If a single price was added to an event, then create the price display and hidden fields to hold the additional information.
	} else if ($wpdb->num_rows == 1) {
		foreach ($prices as $price) {

			//Convert to the member price if the user is logged in
			if (is_user_logged_in()) {
				$member_price = $price->member_price;
			} else {
				$member_price = $price->event_cost;
			}
			//Check for Early Registration discount
			if ($early_price_data = early_discount_amount($event_id, $member_price)) {
				$member_price = $early_price_data['event_price'];
				$early_bird_message = sprintf(__(' (including %s early discount) ', 'event_espresso'), $early_price_data['early_disc']);
			}

			//Calculate the surcharge
			if ($price->surcharge > 0 && $member_price > 0.00) {
				$surcharge = " + {$org_options['currency_symbol']}{$price->surcharge} " . $surcharge_text;
				if ($price->surcharge_type == 'pct') {
					$surcharge = " + {$price->surcharge}% " . $surcharge_text;
				}
			}

			//Create the single price display
			if ( $price->event_cost != '0.00' ) {
			  $html .= '<span class="event_price_label">' . __('Price: ', 'event_espresso') . '</span> <span class="event_price_value">' . $org_options['currency_symbol'] . number_format($member_price, 2, '.', '') . $early_bird_message . $surcharge . '</span>';

			  //Create hidden fields to pass additional information to the add_attendees_to_db function
			  $html .= '<input type="hidden" name="price_id" id="price_id-' . $event_id . '" value="' . $price->id . '">';
			  $html .= '<input type="hidden" name="event_cost' . $multi_name_adjust . '" id="event_cost-' . $price->id . '" value="' . number_format($price->event_cost, 2, '.', '') . '">';
			  } else {
			  $html .= '<span class="free_event">' . __('Free Event', 'event_espresso') . '</span>';
			  $html .= '<input type="hidden" name="payment' . $multi_name_adjust . '" id="payment-' . $event_id . '" value="' . __('free event', 'event_espresso') . '">';
			  }
	    }
    }

	return $html;
}



if (!function_exists('espresso_member_price_select_action')) {

	function espresso_member_price_select_action($event_id, $atts = '') {
		do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
		$html = '';
		//$html .= is_admin() ? '' : '<p class="event_prices">';
		$html .= event_espresso_price_dropdown($event_id, $atts);
		//$html .= is_admin() ? '' : '</p>';
		echo $html;
		return;
	}
	remove_action('espresso_price_select', 'event_espresso_price_dropdown');
	remove_action('espresso_price_select', 'espresso_price_select_action');
	add_action('espresso_price_select', 'espresso_member_price_select_action', 10, 2);
}

/*
  Returns the final price of an event
 *
 * @params int $price_id
 * @params int $event_id
 */
if (!function_exists('event_espresso_get_final_price')) {

	function event_espresso_get_final_price($price_id, $event_id = 0) {
		do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
		global $wpdb;
		$sql = "SELECT id, event_cost, surcharge, surcharge_type FROM " . EVENTS_PRICES_TABLE . " WHERE id='" . $price_id . "' ORDER BY id ASC LIMIT 1";
		if (is_user_logged_in()) {
			$sql = "SELECT id, member_price event_cost, surcharge, surcharge_type FROM " . EVENTS_PRICES_TABLE . " WHERE id='" . $price_id . "' ORDER BY id ASC LIMIT 1";
		}
		do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, 'sql=' . $sql);
		$results = $wpdb->get_results($sql);
		foreach ($results as $result) {
			if ($wpdb->num_rows >= 1) {
				if ($result->event_cost > 0.00) {
					$event_cost = $result->event_cost;
					// Addition for Early Registration discount
					if ($early_price_data = early_discount_amount($event_id, $event_cost)) {
						$event_cost = $early_price_data['event_price'];
					}
					$surcharge = $result->surcharge; //by default it's 0. if flat rate, will just be formatted and atted to the total
					if ($result->surcharge > 0 && $result->surcharge_type == 'pct') { //if >0 and is percent, calculate surcharg amount to be added to total
						$surcharge = $event_cost * $result->surcharge / 100;
					}
					$event_cost += $surcharge;
				} else {
					$event_cost = __('0.00', 'event_espresso');
				}
			} else if ($wpdb->num_rows == 0) {
				$event_cost = __('0.00', 'event_espresso');
			}
		}
		return empty($event_cost) ? 0 : $event_cost;
	}

}




function event_espresso_filter_orig_price_and_surcharge_sql_for_members( $SQL ) {
	if (is_user_logged_in()) {
		// id 	event_id 	price_type 	event_cost 	surcharge 	surcharge_type 	member_price_type 	member_price 	max_qty 	max_qty_members
		$SQL = "SELECT id, member_price AS event_cost, surcharge, surcharge_type FROM " . EVENTS_PRICES_TABLE . " WHERE id=%d ORDER BY id ASC LIMIT 1";
	}
	return $SQL;
}
add_filter( 'filter_hook_espresso_orig_price_and_surcharge_sql', 'event_espresso_filter_orig_price_and_surcharge_sql_for_members', 10, 1 );



function event_espresso_filter_group_price_dropdown_sql_for_members( $SQL ) {
	if (is_user_logged_in()) {
		// id 	event_id 	price_type 	event_cost 	surcharge 	surcharge_type 	member_price_type 	member_price 	max_qty 	max_qty_members
		$SQL = "SELECT ept.id, ept.member_price AS event_cost, ept.surcharge, ept.surcharge_type, ept.member_price_type AS price_type, edt.allow_multiple, edt.additional_limit ";
		$SQL .= "FROM " . EVENTS_PRICES_TABLE . " ept ";
		$SQL .= "JOIN " . EVENTS_DETAIL_TABLE . "  edt ON ept.event_id =  edt.id ";
		$SQL .= "WHERE event_id=%d ORDER BY ept.id ASC";
		// filter SQL statement
	}
	return $SQL;
}
add_filter( 'filter_hook_espresso_group_price_dropdown_sql', 'event_espresso_filter_group_price_dropdown_sql_for_members', 10, 1 );

/**
 * Espresso Edit Profile
 * @author Chris Reynolds
 * @since 1.9.6
 * This runs the code for the shortcode to display an edit profile form on the front end with Event Espresso profile fields added
 */
function event_espresso_member_edit_profile() {
	/* Get user info. */
	global $current_user, $wp_roles, $org_options;
	get_currentuserinfo();

	// themeroller stuff
	if (!empty($org_options['style_settings']['enable_default_style']) && $org_options['style_settings']['enable_default_style'] == 'Y') {

		//Define the path to the ThemeRoller files
		if (file_exists(EVENT_ESPRESSO_UPLOAD_DIR . "themeroller/index.php")) {
			$themeroller_style_path = EVENT_ESPRESSO_UPLOAD_URL . 'themeroller/';
		} else {
			$themeroller_style_path = EVENT_ESPRESSO_PLUGINFULLURL . 'templates/css/themeroller/';
		}

		//Load custom style sheet if available
		if (!empty($org_options['style_settings']['css_name'])) {
			wp_register_style('espresso_custom_css', EVENT_ESPRESSO_UPLOAD_URL . 'css/' . $org_options['style_settings']['css_name']);
			wp_enqueue_style('espresso_custom_css');
		}

		//Register the ThemeRoller styles
		if (!empty($org_options['themeroller']) && !is_admin()) {

			//Load the themeroller base style sheet
			//If the themeroller-base.css is in the uploads folder, then we will use it instead of the one in the core
			if (file_exists(EVENT_ESPRESSO_UPLOAD_DIR . $themeroller_style_path . 'themeroller-base.css')) {
				wp_register_style('espresso_themeroller_base', $themeroller_style_path . 'themeroller-base.css');
			} else {
				wp_register_style('espresso_themeroller_base', EVENT_ESPRESSO_PLUGINFULLURL . 'templates/css/themeroller/themeroller-base.css');
			}
			wp_enqueue_style('espresso_themeroller_base');

			//Load the smoothness style by default<br />
			if (!isset($org_options['themeroller']['themeroller_style']) || empty($org_options['themeroller']['themeroller_style']) || $org_options['themeroller']['themeroller_style'] == 'N' ) {
				$org_options['themeroller']['themeroller_style'] = 'smoothness';
			}

			//Load the selected themeroller style
			wp_register_style('espresso_themeroller', $themeroller_style_path . $org_options['themeroller']['themeroller_style'] . '/style.css');
			wp_enqueue_style('espresso_themeroller');
		}
	}
	// load some styles
	wp_enqueue_style('my_events_table', EVNT_MBR_PLUGINFULLURL . 'styles/my_events_table.css');

	$error = false;
	$updated = false;
	// TODO add a front-end login form for logged-out users

	/* If profile was saved, update profile. */
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' && is_user_logged_in() ) {

	    /* Update user password. */
	    if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
	        if ( $_POST['pass1'] == $_POST['pass2'] )
	            wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => sanitize_text_field($_POST['pass1']) ) );
	        else
	            $error = __('The passwords you entered do not match.  Your password was not updated.', 'event_espresso');
	    }
	    /* Update user information. */
	    if ( !empty( $_POST['url'] ) ) {
	    	if ( strpos($_POST['url'], 'ttp://') ) {
	    		$url_is_valid = true;
	    	} else {
	    		$url_is_valid = false;
	    	}
	    	if ( $url_is_valid ) {
	    		wp_update_user( array( 'ID' => $current_user->ID, 'user_url' => esc_url( $_POST['url'] ) ));
	    	} else {
	    		$error = __('The URL you entered does not appear to be valid. Please enter a valid URL.', 'event_espresso');
	    	}
	    }
	    if ( !empty( $_POST['email'] ) )
	    	wp_update_user( array( 'ID' => $current_user->ID, 'user_email' => sanitize_email( $_POST['email'] ) ) );
	    if ( !empty( $_POST['first-name'] ) )
	        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
	    if ( !empty( $_POST['last-name'] ) )
	        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
	    if ( !empty( $_POST['description'] ) )
	        update_usermeta( $current_user->ID, 'description', esc_attr( $_POST['description'] ) );
		if ( !empty ( $_POST['event_espresso_address'] ) )
			update_user_meta($current_user->ID, 'event_espresso_address', esc_attr( $_POST['event_espresso_address'] ) );
		if ( !empty ( $_POST['event_espresso_address2'] ) )
			update_user_meta($current_user->ID, 'event_espresso_address2', esc_attr( $_POST['event_espresso_address2'] ) );
		if ( !empty ( $_POST['event_espresso_city'] ) )
			update_user_meta($current_user->ID, 'event_espresso_city', esc_attr( $_POST['event_espresso_city'] ) );
		if ( !empty ( $_POST['event_espresso_state'] ) )
			update_user_meta($current_user->ID, 'event_espresso_state', esc_attr( $_POST['event_espresso_state'] ) );
		if ( !empty ( $_POST['event_espresso_zip'] ) )
			update_user_meta($current_user->ID, 'event_espresso_zip', esc_attr( $_POST['event_espresso_zip'] ) );
		if ( !empty ( $_POST['event_espresso_country'] ) )
			update_user_meta($current_user->ID, 'event_espresso_country', esc_attr( $_POST['event_espresso_country'] ) );
		if ( !empty ( $_POST['event_espresso_phone'] ) )
			update_user_meta($current_user->ID, 'event_espresso_phone', esc_attr( $_POST['event_espresso_phone'] ) );
	    /* Redirect so the page will show updated info. */
	    if ( !$error ) {
	    	$updated = true;
	    }
	}
	if ( (!empty($org_options['style_settings']['enable_default_style']) && $org_options['style_settings']['enable_default_style'] == 'Y') || (espresso_version() >= '3.2.P' && !empty($org_options['style_settings']['enable_default_style']) && $org_options['style_settings']['enable_default_style'] == true) ) { ?>
		<script type="text/javascript">
			$jaer = jQuery.noConflict();
			jQuery(document).ready(function($jaer) {
				$jaer('.warning').addClass('ui-state-highlight ui-corner-all');
				$jaer('.updated').addClass('ui-state-highlight ui-corner-all');
				$jaer('.error').addClass('ui-state-error ui-corner-all');
				$jaer('.notice').removeClass('error');
				$jaer('.notice').removeClass('updated');
				$jaer('.notice').removeClass('warning');
			});
		</script>
	<?php } ?>
	<!-- here's the form -->
	<?php if ( !is_user_logged_in() ) : ?>
		<div class="notice warning">
			<p><?php _e('You must be logged in to edit your profile.', 'event_espresso'); ?></p>
		</div><!-- .warning -->
		<?php
			$args = array( 'redirect' => home_url() );
			wp_login_form($args);
		?>
	<?php else : ?>
		<?php if ( $updated == true ) : ?> <div class="notice updated"><p><?php _e( 'Your profile has been updated', 'event_espresso' ); ?></p></div> <?php endif; ?>
		<?php if ( $error ) echo '<div class="notice error"><p>' . $error . '</p></div>'; ?>
			<form method="post" id="adduser" action="<?php the_permalink(); ?>" class="edit-profile ui-widget event-display-boxes">
				<fieldset>
					<h3 class="ui-widget-header ui-corner-top"><?php _e( 'Name', 'event_espresso' ); ?></h3>
					<div class="event-data-display ui-widget-content ui-corner-bottom">
						<p class="form-username">
							<label for="first-name"><?php _e('First Name', 'event_espresso'); ?></label>
							<input class="text-input ui-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'user_firstname', $current_user->ID ); ?>" />
						</p><!-- .form-username -->
						<p class="form-username">
							<label for="last-name"><?php _e('Last Name', 'event_espresso'); ?></label>
							<input class="text-input ui-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'user_lastname', $current_user->ID ); ?>" />
						</p><!-- .form-username -->
						<p class="form-email">
							<label for="email"><?php _e('E-mail *', 'event_espresso'); ?></label>
							<input class="text-input ui-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" />
						</p><!-- .form-email -->
					</div>
				</fieldset>
				<fieldset>
					<h3 class="ui-widget-header ui-corner-top"><?php _e( 'Contact Info', 'event_espresso' ); ?></h3>
					<div class="event-data-display ui-widget-content ui-corner-bottom">
						<p class="form-address">
							<label for="event_espresso_address"><?php _e( 'Address', 'event_espresso' ); ?></label>
							<input class="text-input ui-input" name="event_espresso_address" type="text" id="event_espresso_address" value="<?php the_author_meta( 'event_espresso_address', $current_user->ID ); ?>" />
						</p>
						<p class="form-address2">
							<label for="event_espresso_address2"><?php _e( 'Address 2', 'event_espresso' ); ?></label>
							<input class="text-input ui-input" name="event_espresso_address2" type="text" id="event_espresso_address2" value="<?php the_author_meta( 'event_espresso_address2', $current_user->ID ); ?>" />
						</p>
						<p class="form-city">
							<label for="event_espresso_city"><?php _e( 'City', 'event_espresso' ); ?></label>
							<input class="text-input ui-input" name="event_espresso_city" type="text" id="event_espresso_city" value="<?php the_author_meta( 'event_espresso_city', $current_user->ID ); ?>" />
						</p>
						<p class="form-state">
							<label for="event_espresso_state"><?php _e( 'State', 'event_espresso' ); ?></label>
							<input class="text-input ui-input" name="event_espresso_state" type="text" id="event_espresso_state" value="<?php the_author_meta( 'event_espresso_state', $current_user->ID ); ?>" />
						</p>
						<p class="form-zip">
							<label for="event_espresso_zip"><?php _e( 'Postal Code', 'event_espresso' ); ?></label>
							<input class="text-input ui-input" name="event_espresso_zip" type="text" id="event_espresso_zip" value="<?php the_author_meta( 'event_espresso_zip', $current_user->ID ); ?>" />
	                        	</p>
						<p class="form-country">
							<label for="event_espresso_country"><?php _e( 'Country', 'event_espresso' ); ?></label>
							<input class="text-input ui-input" name="event_espresso_country" type="text" id="event_espresso_country" value="<?php the_author_meta( 'event_espresso_country', $current_user->ID ); ?>" />
	                        	</p>
						<p class="form-phone">
							<label for="event_espresso_phone"><?php _e( 'Phone', 'event_espresso' ); ?></label>
							<input class="text-input ui-input" name="event_espresso_phone" type="text" id="event_espresso_phone" value="<?php the_author_meta( 'event_espresso_phone', $current_user->ID ); ?>" />
	                        	</p>
						<p class="form-url">
							<label for="url"><?php _e('Website', 'event_espresso'); ?></label>
							<input class="text-input ui-input" name="url" type="text" id="url" value="<?php the_author_meta( 'user_url', $current_user->ID ); ?>" />
						</p><!-- .form-url -->
		            </div>
				</fieldset>
				<fieldset>
					<h3 class="ui-widget-header ui-corner-top"><?php _e( 'Other Information', 'event_espresso' ); ?></h3>
					<div class="event-data-display ui-widget-content ui-corner-bottom">
						<p class="form-password">
							<label for="pass1"><?php _e('Password *', 'event_espresso'); ?> </label>
							<input class="text-input ui-input" name="pass1" type="password" id="pass1" />
						</p><!-- .form-password -->
						<p class="form-password">
							<label for="pass2"><?php _e('Repeat Password *', 'event_espresso'); ?></label>
							<input class="text-input ui-input" name="pass2" type="password" id="pass2" />
						</p><!-- .form-password -->
						<p class="form-textarea">
							<label for="description"><?php _e('Biographical Information', 'event_espresso') ?></label>
							<textarea name="description" class="ui-input" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $current_user->ID ); ?></textarea>
						</p><!-- .form-textarea -->
						<p class="form-submit">
							<input name="updateuser" type="submit" id="updateuser" class="submit button ui-button ui-button-big ui-priority-primary ui-state-default ui-state-hover ui-state-focus ui-corner-all" value="<?php _e('Update', 'event_espresso'); ?>" />
							<?php wp_nonce_field( 'update-user' ) ?>
							<input name="action" type="hidden" id="action" value="update-user" />
						</p><!-- .form-submit -->
					</div>
				</fieldset>
			</form><!-- #adduser -->
	<?php endif;
}
/**
 * Espresso Edit Profile Display
 * @uses event_espresso_member_edit_profile
 * @author Chris Reynolds
 * @since 1.9.6
 * Spits event_espresso_member_edit_profile into an output buffer so it displays correctly in the_content. Used to create the shortcode to display an edit profile form on the front end with Event Espresso profile fields added
 */

function event_espresso_member_edit_profile_display() {
	ob_start();
	event_espresso_member_edit_profile();
	$buffer = ob_get_contents();
	ob_end_clean();
	return $buffer;
}
add_shortcode( 'ESPRESSO_EDIT_PROFILE', 'event_espresso_member_edit_profile_display' );