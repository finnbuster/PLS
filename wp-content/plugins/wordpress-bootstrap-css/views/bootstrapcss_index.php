<?php

include_once( dirname(__FILE__).WORPIT_DS.'worpit_options_helper.php' );
include_once( dirname(__FILE__).WORPIT_DS.'widgets'.WORPIT_DS.'bootstrapcss_widgets.php' );
	
?>
<div class="wrap">
	<div class="bootstrap-wpadmin">

		<div class="page-header">
			<a href="http://worpit.com/"><div class="icon32" id="worpit-icon"><br /></div></a>
			<h2><?php _hlt_e( 'Bootstrap Options :: Twitter Bootstrap Plugin from Worpit' ); ?></h2><?php _hlt_e( '' ); ?>
		</div>
		
		<div class="row">
			<div class="span9">
			
				<form action="<?php echo $worpit_form_action; ?>" method="post" class="form-horizontal">
				<?php
					wp_nonce_field( $worpit_nonce_field );
					printAllPluginOptionsForm( $worpit_aAllOptions, $worpit_var_prefix, 1 );
				?>
				<div class="form-actions">
					<input type="hidden" name="<?php echo $worpit_var_prefix; ?>all_options_input" value="<?php echo $worpit_all_options_input; ?>" />
					<input type="hidden" name="worpit_plugin_form_submit" value="Y" />
					<button type="submit" class="btn btn-primary" name="submit"><?php _hlt_e( 'Save All Settings'); ?></button>
					</div>
				</form>
				
			</div><!-- / span9 -->
		
			<div class="span3" id="side_widgets">
		  		<?php echo getWidgetIframeHtml('side-widgets'); ?>
			</div>
		</div><!-- / row -->
	
	</div><!-- / bootstrap-wpadmin -->
	<?php include_once( dirname(__FILE__).'/bootstrapcss_js.php' ); ?>
</div>