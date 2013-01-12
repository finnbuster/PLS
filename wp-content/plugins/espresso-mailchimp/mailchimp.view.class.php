<?php
class MailChimpView{
	/**
     * Displays the administrator's MailChimp Integration configuration form to retrieve the MailChimp API Key
     *
     * @param array $errors, if provided, will list the errors in a list format, with the 'red_alert' stylesheet class.
     * @param boolean $success, if true, will create a success message with the 'green_alert' stylesheet class.
     *
     */
	function configuration($errors=null, $success=false){
		//define the current key.  If current key is now invalid, reset to null and display error
		$currentKey=MailChimpController::get_valid_mailchimp_key();
		if(MailChimpController::mailchimp_is_error($currentKey)) { 
			$errors=$currentKey;
			$currentKey=null;
		}
		?>
		 <form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
		 <ul>
		 <?php
		 //if errors occurred, display them here.
		 if(MailChimpController::mailchimp_is_error($errors)){
		 	 echo "<li style='width: 40%' class='red_alert'>";
			foreach($errors as $error){
				echo "<p>$error</p>";
			}
			echo "</li>";
		}
		//if the credentials were successfully modified with no errors, let the user know.
		if($success){
			 echo "<li style='width: 40%' class='green_alert'>";
			 echo "<p>MailChimp API Key Updated</p>";
			 echo "</li>";
		}
		?>
		 <li>MailChimp API Key  <?php echo apply_filters('espresso_help', 'api-key-help') ?></li>
		 <li><input size="45" type="text" name="mailchimp_api_key" value="<?php echo $currentKey; ?>" /></li>
		 <li><input class="button-primary" name="update_mailchimp_settings_post" value="Save MailChimp API Key" type="submit" /></li>
		 </ul>
		 </form>
    <?php ### help dialogue ### ?>
			<div id="api-key-help" style="display:none">
			 <div class="TB-ee-frame">
    	 <h2><?php _e("MailChimp API Key","event_espresso"); ?> </h2>
    	 <p><?php _e("If you do not have a MailChimp API key, please <a href='http://kb.mailchimp.com/article/where-can-i-find-my-api-key/' target='_blank'>click here</a> to learn how to create one. </p><p>An API key is required for this plugin.",'event-espresso');?> </p>
     </div>
			</div>
		 
		 <?php
	}
	
	/**
     * Displays the Add Event / Update Event "MailChimp List Integration" option.  It will use the get_lists MailChimpController function to populate the dropdown.
     * If there are no lists within the MailChimp instance, or if the MailChimp integration has not been configured, nothing will be returned.
     */
	function event_list_selection(){
		//grab the lists from the MailChimp integration. 
		$lists=MailChimpController::get_lists();
		//do not display the mailchimp integration settings if there are no lists to display
		//this will likely only happen if the API key is invalid, or has not been setup.
		if(!empty($lists)){
			?>
			<div style="display: block;" id="mailchimp-options" class="postbox">
			<div class="handlediv" title="Click to toggle"><br />
			</div>
			<h3 class="hndle"><span>
			<?php _e('MailChimp List Integration','event_espresso'); ?>
			</span></h3>
			<div class="inside">
			 <p>
			  <?php echo $lists; ?>
			 </p>
			</div>
			</div>
			<?php ### help dialogue ### ?>
			<div id="mailchimp-list-integration" style="display:none">
			 <div class="TB-ee-frame">
			  <h2><?php _e("MailChimp List Integration","event_espresso"); ?> </h2>
			  <p><?php _e("The following information will be sent to the selected MailChimp list for future communications <ul><li>Registrant's First Name</li><li>Registrant's Last Name</li><li>Registrant's Email Address</li></ul>",'event-espresso');?> </p>
			 </div>
			</div>
			
			<?php
        }
    }
    
    /**
     * Displays the common "head" elements of the MailChimp Integration configuration view.
     */
	function head(){
	?>
		<div id="mailchimp-api" class="wrap">
		  <div id="icon-options-event" class="icon32"></div>
			<h2><?php echo _e('Manage MailChimp Integration Settings', 'event_espresso'); ?></h2>
		<?php ob_start(); ?>		
				     <div class="meta-box-sortables ui-sortables">
							
							<div class="metabox-holder">
    		     <div class="postbox">
					      <div title="Click to toggle" class="handlediv"><br /></div>
    			    <h3 class="hndle"><?php _e('Mail Chimp Integration Settings','event_espresso'); ?></h3>
    			    <div class="inside">
    <?php
	}
	
	/**
     * Displays the common "footer" elements of the MailChimp Integration configuration view.
     */
	function foot(){
		?>
				       </div><!-- / .inside -->
    		   </div><!-- /.postbox -->
    	   </div><!-- / .metabox-holder -->
    

			
			<script type="text/javascript" charset="utf-8">
			//<![CDATA[
				jQuery(document).ready(function() {
					postboxes.add_postbox_toggles('template_conf');
      }); 
			//]]>
			</script>
			  
				 </div><!-- / .meta-box-sortables .ui-sortables -->
			<?php
			$main_post_content = ob_get_clean();
			espresso_choose_layout($main_post_content, event_espresso_display_right_column());
			?>		
		 </div><!-- / #wrap -->
    <?php
	}
}
