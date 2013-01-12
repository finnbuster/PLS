<?php
/**
* Handles the form logic of the MailChimp API Key save routines within the administrator's control panel.
*/
function event_espresso_mailchimp_settings(){
	MailChimpView::head();
	if(isset($_REQUEST["update_mailchimp_settings_post"])){
		$process=MailChimpController::update_mailchimp_settings();
		if(MailChimpController::mailchimp_is_error($process)){
			MailChimpView::configuration($process);
		}else{
			MailChimpView::configuration(null,1);
		}
	}else{
		MailChimpView::configuration();
	}
	MailChimpView::foot();
}