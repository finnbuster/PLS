<div style="display: none;">
	<?php
  	/**
  	 * Custom Ticket Templates Help Box
  	 */
  ?>
	<div id="customized_ticket_info" class="pop-help" >
		<div class="TB-ee-frame">
			<h2>
				<?php _e('Custom Ticket Templates', 'event_espresso'); ?>
			</h2>
			<p>
				<?php _e('At least one ticket template must exist in order to use Event Espresso tickets.', 'event_espresso'); ?>
			</p>
		</div>
	</div>
	<?php
  	/**
  	 * Ticket Name Help Box
  	 */
  ?>
	<div id="ticket_name_info" class="pop-help" >
		<div class="TB-ee-frame">
			<h2>
				<?php _e('Ticket Name', 'event_espresso'); ?>
			</h2>
			<p>
				<?php _e('Ticket Name will appear in the Custom Ticket dropdown menu when you are adding or editing an event.', 'event_espresso'); ?>
			</p>
		</div>
	</div>
	<?php
  	/**
  	 * Base Ticket Template Help Box
  	 */
  ?>
	<div id="base_template_info" class="pop-help" >
		<div class="TB-ee-frame">
			<h2>
				<?php _e('Base Ticket Template', 'event_espresso'); ?>
			</h2>
			<p>
				<?php _e('If you want to use customized ticket templates, templates must be uploaded to <tt>/wp-content/uploads/espresso/tickets/templates/</tt>.', 'event_espresso'); ?>
			</p>
		</div>
	</div>
	<?php
  	/**
  	 * Ticket Logo Help Box
  	 */
  ?>
	<div id="ticket_logo_info" class="pop-help" >
		<div class="TB-ee-frame">
			<h2>
				<?php _e('Ticket Logo', 'event_espresso'); ?>
			</h2>
			<p>
				<?php _e('If no logo is uploaded, ticket will use the default logo on the General Settings page.', 'event_espresso'); ?>
			</p>
		</div>
	</div>
	<?php
  	/**
  	 * Ticket Description Help Box
  	 */
  ?>
	<div id="ticket_description_info" class="pop-help" >
		<div class="TB-ee-frame">
			<h2>
				<?php _e('Ticket Description/Instructions', 'event_espresso'); ?>
			</h2>
			<p>
				<?php _e('Use this editor to add any information about the venue or instructions for the ticket holder.  On the default template, this will appear under the ticket.', 'event_espresso'); ?>
			</p>
			<p> <em class="important"> <strong>
				<?php _e('ATTENTION:', 'event_espresso'); ?>
				</strong><br />
				<?php _e('The custom shortcodes will not work here. Please edit the HTML files.', 'event_espresso'); ?>
				</em> </p>
		</div>
	</div>
	<?php
    	/**
    	 * Ticket Guide
    	 */
    ?>
	<div id="custom_ticket_tags" class="pop-help" >
		<div class="TB-ee-frame"  style=" height:500px; overflow:scroll">
			<h2>
				<?php _e('Custom Ticket Tags', 'event_espresso'); ?>
			</h2>
			<p> //Attendee/Event Information<br />
				[att_id]<br />
				[qr_code]<br />
				[event_id]<br />
				[event_identifier]<br />
				[registration_id]<br />
				[registration_date]<br />
				[fname]<br />
				[lname]<br />
				[event_name]<br />
				[description]<br />
				[event_link]<br />
				[event_url]</p>
				
				<p>//Payment details<br />
				[cost]<br />
				[ticket_type]</p>
				
				<p>//Organization details<br />
				[company]<br />
				[co_add1]<br />
				[co_add2]<br />
				[co_city]<br />
				[co_state]<br />
				[co_zip]</p>
				
				<p>//Dates<br />
				[start_date]<br />
				[start_time]<br />
				[end_date]<br />
				[end_time]</p>
				
				<p>//Ticket data<br />
				[ticket_content]</p>
				
				<p>//Venue information<br />
				[venue_title]<br />
				[venue_address]<br />
				[venue_address2]<br />
				[venue_city]<br />
				[venue_state]<br />
				[venue_zip]<br />
				[venue_country]<br />
				[venue_phone]<br />
				[venue_description]<br />
				[venue_website]<br />
				[venue_image]<br />
				[google_map_image]<br />
				[google_map_link]</p> 
			<p>
				<?php _e('', 'event_espresso'); ?>
			</p>
		</div>
	</div>
	<?php
    	/**
    	 * Ticket Guide
    	 */
    ?>
	<div id="ticket-guide" class="pop-help" >
		<div class="TB-ee-frame">
			<h2>
				<?php _e('Ticket Guide', 'event_espresso'); ?>
			</h2>
			<p>
				<?php _e('', 'event_espresso'); ?>
			</p>
			<p>
				<?php _e('', 'event_espresso'); ?>
			</p>
			<p> <em class="important"> <strong>
				<?php _e('ATTENTION:', 'event_espresso'); ?>
				</strong><br />
				<?php _e('', 'event_espresso'); ?>
				</em> </p>
		</div>
	</div>
</div>
<!-- close parent display --> 
