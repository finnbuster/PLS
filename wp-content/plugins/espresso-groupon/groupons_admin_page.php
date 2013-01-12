<?php

function event_espresso_groupon_config_mnu() {
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	global $wpdb;
//$wpdb->show_errors();
	$edit_action = !empty($_REQUEST['action']) && $_REQUEST['action'] == 'edit' ? true : false;
	$add_new_action = !empty($_REQUEST['action']) && $_REQUEST['action'] == 'add_new_groupon' ? true : false;
	$delete = empty($_POST['delete_groupon']) ? false : true;
	$checkbox = empty($_POST['checkbox']) ? false : $_POST['checkbox'];
	$csv_import_action = !empty($_REQUEST['action']) && $_REQUEST['action'] == 'csv_import' ? true : false;
	$submit = isset($_POST['Submit']) ? true : false;
	$update_action = !empty($_REQUEST['action']) && $_REQUEST['action'] == 'update' ? true : false;
	$groupon_id = empty($_REQUEST['groupon_id']) ? false : $_REQUEST['groupon_id'];
	$groupon_code = empty($_REQUEST['groupon_code']) ? false : htmlentities2($_REQUEST['groupon_code']);
	$groupon_status = empty($_REQUEST['groupon_status']) ? false : true;
	$old_groupon_status = empty($_REQUEST['old_groupon_status']) ? false : true;
	$attendee_id = empty($_REQUEST['attendee_id']) ? false : $_REQUEST['attendee_id'];
	$code_reuse = ($update_action && !$old_groupon_status && $groupon_status && $attendee_id) ? true : false;
	$groupon_holder = empty($_REQUEST['groupon_holder']) ? false : $_REQUEST['groupon_holder'];
	$add_action = !empty($_REQUEST['action']) && $_REQUEST['action'] == 'add' ? true : false;
	?>
	<div class="wrap">
		<div id="icon-options-event" class="icon32"> </div>
		<h2><?php echo _e('Manage Groupon Codes', 'event_espresso') ?>
			<?php
			if (!$edit_action && !$add_new_action) {
				echo '<a href="admin.php?page=groupons&amp;action=add_new_groupon" class="button add-new-h2" style="margin-left: 20px;">' . __('Add New Groupon Code', 'event_espresso') . '</a>';
			}
			?>
		</h2>
		<?php
		ob_start();
		if ($delete) {
			if (is_array($checkbox)) {
				while (list($key, $value) = each($checkbox)):
					$del_id = $key;
					//Delete discount data
					$sql = "DELETE FROM " . EVENTS_GROUPON_CODES_TABLE . " WHERE id='$del_id'";
					$wpdb->query($sql);

				//$sql = "DELETE FROM ".EVENTS_GROUPON_REL_TABLE." WHERE groupon_id='$del_id'";
				//$wpdb->query($sql);
				endwhile;
			}
			?>
			<div id="message" class="updated fade"><p><strong><?php _e('Groupon Code(s) have been successfully deleted from the database.', 'event_espresso'); ?></strong></p></div>
			<?php
		}

		if ($submit) {
			if ($update_action) {
				global $wpdb;
				//Post the new event into the database
				$sql = "UPDATE " . EVENTS_GROUPON_CODES_TABLE . " SET groupon_code='$groupon_code', groupon_status='$groupon_status', groupon_holder='$groupon_holder' WHERE id = $groupon_id";

				if ($wpdb->query($sql)) {
					?>
					<div id="message" class="updated fade"><p><strong><?php _e('The groupon code ' . $groupon_code . ' has been updated.', 'event_espresso'); ?></strong></p></div>
				<?php } else { ?>
					<div id="message" class="error"><p><strong><?php _e('The groupon code ' . $groupon_code . ' was not updated.', 'event_espresso'); ?> <?php print $wpdb->print_error(); ?></strong></p></div>
					<?php
				}
				if ($code_reuse) {
					$sql = "SELECT id FROM " . EVENTS_GROUPON_CODES_TABLE . " WHERE attendee_id='$attendee_id'";
					if (!$wpdb->get_row($sql)) {
						$sql = "INSERT INTO " . EVENTS_GROUPON_CODES_TABLE . " (groupon_code, groupon_status, attendee_id, groupon_holder) VALUES('$groupon_code.-(used)', '0', '$attendee_id', 'USED_CODE')";
						$wpdb->query($sql);
					}
				}
			}
		}

		function add_groupon_to_db() {
			do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
			global $wpdb;
			$submit = isset($_POST['Submit']) ? true : false;
			$add_action = !empty($_REQUEST['action']) && $_REQUEST['action'] == 'add' ? true : false;
			$groupon_code = empty($_REQUEST['groupon_code']) ? false : htmlentities2($_REQUEST['groupon_code']);
			$groupon_status = empty($_REQUEST['groupon_status']) ? false : $_REQUEST['groupon_status'];
			$groupon_holder = empty($_REQUEST['groupon_holder']) ? false : $_REQUEST['groupon_holder'];
			if ($submit) {
				if ($add_action) {
					$sql = "INSERT INTO " . EVENTS_GROUPON_CODES_TABLE . " (groupon_code, groupon_status, groupon_holder) VALUES('$groupon_code', '$groupon_status', '$groupon_holder')";

					if ($wpdb->query($sql)) {
						?>
						<div id="message" class="updated fade"><p><strong><?php _e('The groupon code ' . $groupon_code . ' has been added.', 'event_espresso'); ?></strong></p></div>
						<?php
					} else {
						?>
						<div id="message" class="error"><p><strong><?php _e('The groupon code ' . $groupon_code . ' was not saved.', 'event_espresso'); ?> <?php //print $wpdb->print_error();  ?>.</strong></p></div>
						<?php
					}
				}
			}
		}

		if ($add_action) {
			add_groupon_to_db();
		}
		if ($csv_import_action) {
			require_once ('csv_import.php');
			groupon_csv_import();
		}

		function add_new_event_groupon() {
			do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
			$uri = empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'];
			?>
			<div class="metabox-holder">
				<div class="postbox">
					<h3><?php _e('Add a Groupon Code', 'event_espresso'); ?></h3>
					<div class="inside">
						<form id="add-new-groupon" method="post" action="<?php echo $uri; ?>">
							<input type="hidden" name="action" value="add">
							<ul>
								<li>
									<label for="groupon_code"><?php _e('Groupon Code: ', 'event_espresso'); ?><em title="<?php _e('This field is required', 'event_espresso') ?>"> *</em></label> <input type="text" id="groupon_code" name="groupon_code" size="25"></li>
								<li>
									<label><?php _e('Groupon Status: ', 'event_espresso'); ?></label>
									<input type="radio" checked="checked" name="groupon_status" value="1"><?php _e('Active', 'event_espresso'); ?>
									<input type="radio" name="groupon_status" value="0"><?php _e('Used', 'event_espresso'); ?></li>
									<!--<input name="groupon_status"></li>-->
								<li><label><?php _e('Groupon Holder: ', 'event_espresso'); ?></label><input type="text" name="groupon_holder" size="25"></li>
								<li>
									<p>
										<input class="button-primary" type="submit" name="Submit" value="<?php _e('Submit', 'event_espresso'); ?>" id="add_new_groupon" />
									</p>
								</li>
							</ul>
						</form>
					</div>
				</div>
			</div>
			<?php
		}

		if ($add_new_action) {
			add_new_event_groupon();
		}

		function edit_event_groupon() {
			do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
			global $wpdb;
			$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : 0;

			if ($event_groupon = $wpdb->get_row("SELECT * FROM " . EVENTS_GROUPON_CODES_TABLE . " WHERE id = " . $id)) {
				$groupon_id = $event_groupon->id;
				$attendee_id = $event_groupon->attendee_id;
				$groupon_code = $event_groupon->groupon_code;
				$groupon_status = $event_groupon->groupon_status;
				$groupon_holder = $event_groupon->groupon_holder;
				$uri = empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'];
				?>
				<!--Add event display-->
				<div class="metabox-holder">
					<div class="postbox">
						<h3><?php _e('Edit Groupon Code:', 'event_espresso'); ?> <?php echo $groupon_code ?></h3>
						<div class="inside">
							<form method="post" action="<?php echo $uri; ?>">
								<input type="hidden" name="groupon_id" value="<?php echo $groupon_id; ?>">
								<input type="hidden" name="attendee_id" value="<?php echo $attendee_id; ?>">
								<input type="hidden" name="action" value="update">
								<input type="hidden" name="old_groupon_status" value="<?php echo $groupon_status; ?>">
								<ul>
									<li>
										<label><?php _e('Groupon Code: ', 'event_espresso'); ?></label> <input type="text" name="groupon_code" value="<?php echo $groupon_code ?>" size="25"></li>
									<li>
										<label><?php _e('Groupon Status: ', 'event_espresso'); ?></label>
										<?php
										$values = array(
												array('id' => '1', 'text' => __('Active', 'event_espresso')),
												array('id' => '0', 'text' => __('Used', 'event_espresso')));
										echo select_input('groupon_status', $values, $groupon_status);
										?> 
									</li>
									<li><label><?php _e('Groupon Holder: ', 'event_espresso'); ?></label><input type="text" name="groupon_holder" value="<?php echo $groupon_holder ?>"></li>

									<li>
										<input class="button-primary" type="submit" name="Submit" value="<?php _e('Update', 'event_espresso'); ?>" id="update_groupon" /> 
									</li>
								</ul>
							</form>
						</div>
					</div>
				</div>
				<?php
			}
		}

		if ($edit_action) {
			edit_event_groupon();
		}
		$uri = empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'];
		?>
		<form id="form1" name="form1" method="post" action="<?php echo $uri ?>">

			<table id="table" class="widefat fixed" width="100%"> 
				<thead>
					<tr>
						<th class="manage-column column-cb check-column" id="cb" scope="col" style="width:5.5%;"><input type="checkbox"></th>
						<th class="manage-column column-comments num" id="id" style="padding-top:7px; width:5.5%;" scope="col" title="Click to Sort"><?php _e('ID', 'event_espresso'); ?></th>
						<th class="manage-column column-title" id="name" scope="col" title="Click to Sort" style="width:20%;"><?php _e('Code', 'event_espresso'); ?></th>
						<th class="manage-column column-author" id="status" scope="col" title="Click to Sort" style="width:20%;"><?php _e('Status', 'event_espresso'); ?></th>
						<th class="manage-column column-author" id="name" scope="col" title="Click to Sort" style="width:20%;"><?php _e('Groupon Holder', 'event_espresso'); ?></th>
						<th class="manage-column column-author" id="action" scope="col" title="Click to Sort" style="width:20%;"><?php _e('Action', 'event_espresso'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$wpdb->get_results("SELECT * FROM " . EVENTS_GROUPON_CODES_TABLE);
					if ($wpdb->num_rows > 0) {
						$event_groupons = $wpdb->get_results("SELECT * FROM " . EVENTS_GROUPON_CODES_TABLE . " ORDER BY id ASC");
						foreach ($event_groupons as $event_groupon) {

							$groupon_id = $event_groupon->id;
							$groupon_code = $event_groupon->groupon_code;
							$groupon_status = $event_groupon->groupon_status;
							$groupon_holder = $event_groupon->groupon_holder;
							if ($groupon_holder == 'USED_CODE') continue;

							$active_groupon = '<span style="color: #F00; font-weight:bold;">' . __('USED', 'event_espresso') . '</span>';
							if ($groupon_status > 0) {
								$active_groupon = '<span style="color: #090; font-weight:bold;">' . __('ACTIVE', 'event_espresso') . '</span>';
							}
							?>
							<tr>
								<td><input name="checkbox[<?php echo $groupon_id ?>]" type="checkbox"  title="Delete <?php echo $groupon_code ?>"></td>
								<td><?php echo $groupon_id ?></td>
								<td><?php echo $groupon_code ?></td>
								<td><?php echo $active_groupon ?></td>
								<td><?php echo $groupon_holder ?></td>
								<td><a href="admin.php?page=groupons&action=edit&id=<?php echo $groupon_id ?>"><?php _e('Edit Groupon', 'event_espresso'); ?></a></td>
							</tr>
							<?php
						}
					}
					?>

				</tbody>
			</table>
			<input type="checkbox" name="sAll" onclick="selectAll(this)" /> <strong><?php _e('Check All', 'event_espresso'); ?></strong> 
			<input name="delete_groupon" type="submit" class="button-secondary" id="delete_groupon" value="<?php _e('Delete Groupon', 'event_espresso'); ?>" style="margin-left:100px;" onclick="return confirmDelete();"> <?php echo '<a href="admin.php?page=groupons&amp;action=add_new_groupon" class="button add-new-h2" style="margin-left: 20px;">' . __('Add New Groupon Code', 'event_espresso') . '</a>'; ?> <?php /* ?><a  style="margin-left:5px"class="button-primary" href="admin.php?page=groupons&amp;action=csv_import"><?php _e('Import CSV','event_espresso'); ?></a><?php */ ?>
		</form>
		<?php
		$main_post_content = ob_get_clean();
		espresso_choose_layout($main_post_content, event_espresso_display_right_column());
		?>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($) {						
			/* show the table data */
			var mytable = $('#table').dataTable( {
				"sDom": 'Clfrtip',
				"aoColumns": [
					{ "bSortable": false },
					null,
					null,
					null,
					null,
					null
				],
		               
				"bAutoWidth": false,	
				"bStateSave": true,
				"sPaginationType": "full_numbers",
				"oLanguage": {	"sSearch": "<strong><?php _e('Live Search Filter', 'event_espresso'); ?>:</strong>",
					"sZeroRecords": "<?php _e('No Records Found!', 'event_espresso'); ?>" }
		        			
			} );
		        	
		} );
		// Add new groupon code form validation
		jQuery(function(){
			jQuery('#add-new-groupon').validate( {
				rules: {
					groupon_code: "required"
				},
				messages: {
					groupon_code: "Please add your groupon code"
				}
			});
		});
	</script>
	<?php
//============= End Event Registration Groupon Subpage - Add/Delete/Edit Groupon Codes  =============== //
}