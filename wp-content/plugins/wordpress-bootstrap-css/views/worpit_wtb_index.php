<?php 
	include_once( dirname(__FILE__).'/widgets/bootstrapcss_widgets.php' );
?>

<div class="wrap">
	<div class="bootstrap-wpadmin">

		<div class="page-header">
			<a href="http://worpit.com/"><div class="icon32" id="worpit-icon"><br /></div></a>
			<h2><?php _hlt_e( 'Dashboard :: Twitter Bootstrap Plugin from Worpit' ); ?></h2><?php _hlt_e( '' ); ?>
		</div>

		<?php include_once( dirname(__FILE__).'/widgets/bootstrapcss_common_widgets.php' ); ?>

		<div class="row" id="worpit_promo">
		  <div class="span12">
		  	<?php echo getWidgetIframeHtml('dashboard-widget-worpit'); ?>
		  </div>
		</div><!-- / row -->

		<div class="row" id="developer_channel_promo">
		  <div class="span12">
		  	<?php echo getWidgetIframeHtml('dashboard-widget-developerchannel'); ?>
		  </div>
		</div><!-- / row -->
		
		<div class="row" id="tbs_docs">
		  <div class="span6" id="tbs_docs_shortcodes">
			  <div class="well">
				<h3>WordPress Twitter Bootstrap CSS Plugin Shortcodes</h3>
				<p>Check the <a href="http://bit.ly/OFXMBh" target="_blank">Twitter Bootstrap Plugin Shortcodes Demo Page</a> for complete demos for all shortcodes!</p>
				<p>To learn more about what shortcodes are, <a href="http://www.hostliketoast.com/2011/12/how-extend-wordpress-powerful-shortcodes/">check this link</a></p>
				<p>The following shortcodes are available:</p>
				<ol>
					<li>[ <a href="http://bit.ly/OFXMBh" title="Twitter Bootstrap Plugin Shortcodes Demo Page">TBS_PROGRESS_BAR</a> ] <span class="label label-success">new</span></li>
					<li>[ <a href="http://bit.ly/OFXMBh" title="Twitter Bootstrap Plugin Shortcodes Demo Page">TBS_COLLAPSE | TBS_COLLAPSE_GROUP</a> ] <span class="label label-success">new</span></li>
					<li>[ <a href="http://bit.ly/T65cho" title="Twitter Bootstrap Row Shortcode">TBS_ROW</a> ] <span class="label label-success">new</span></li>
					<li>[ <a href="http://bit.ly/T65cho" title="Twitter Bootstrap Span Shortcode">TBS_SPAN</a> ] <span class="label label-success">new</span></li>
					<li>[ <a href="http://bit.ly/HOt01C" title="Twitter Bootstrap Badge Shortcode">TBS_BADGE</a> ] </li>
					<li>[ <a href="http://bit.ly/zmGUeD" title="Twitter Bootstrap Glyph Icon WordPress Shortcode">TBS_ICON</a> ]</li>
					<li>[ <a href="http://bit.ly/AlETMx" title="Twitter Bootstrap Button WordPress Shortcode">TBS_BUTTON</a> ]</li>
					<li>[ <a href="http://bit.ly/wIUa7U" title="Twitter Bootstrap Button WordPress Shortcode">TBS_BUTTONGROUP</a> ]</li>
					<li>[ <a href="http://bit.ly/wJqEhk" title="Twitter Bootstrap Label WordPress Shortcode">TBS_LABEL</a> ]</li>
					<li>[ <a href="http://bit.ly/zGgnOl" title="Twitter Bootstrap Blockquotes WordPress Shortcode">TBS_BLOCKQUOTE</a> ]</li>
					<li>[ <a href="http://bit.ly/uiipiY" title="Twitter Bootstrap Block Alerts WordPress Shortcode">TBS_BLOCK</a>] * Removed from 2.0.3+ Use [TBS_ALERT]</li>
					<li>[ <a href="http://bit.ly/uiipiY" title="Twitter Bootstrap Block Alerts WordPress Shortcode">TBS_ALERT</a> ]</li>
					<li>[TBS_CODE]</li>
					<li>[ <a href="http://bit.ly/xMn0AZ" title="Twitter Bootstrap Button WordPress Shortcode">TBS_TWIPSY</a>] * Removed from 2.0.3+ Use [TBS_TOOLTIP]</li>
					<li>[ <a href="http://bit.ly/xMn0AZ" title="Twitter Bootstrap Button WordPress Shortcode">TBS_TOOLTIP</a> ]</li>
					<li>[ <a href="http://bit.ly/AC5JW5" title="Twitter Bootstrap Button WordPress Shortcode">TBS_POPOVER</a> ]</li>
					<li>[TBS_DROPDOWN] + [TBS_DROPDOWN_OPTION]. * Not YET fully supported in plugin version v2.0+</li>
					<li>[TBS_TABGROUP] + [TBS_TAB]. * Not YET fully supported in plugin version v2.0+</li>
				</ol>
			  </div>
		  </div><!-- / span6 -->
		  <div class="span6" id="tbs_docs_examples">
		  <div class="well">
			<h3>Shortcode Usage Examples</h3>
			<div class="shortcode-usage">
				<p>Check the <a href="http://bit.ly/OFXMBh" target="_blank">Twitter Bootstrap Plugin Shortcodes Demo Page</a> for complete demos for all shortcodes!</p>
				<p>The following are just some examples of how you can use the shortcodes with the associated HTML output</p>
				<ul>
					<li><span class="code">[TBS_BUTTON id="mySpecialButton" link="http://worpit.com"]Click Me[/TBS_BUTTON]</span>
					<p>will give the following HTML:</p>
					<p class="code">&lt;a href="http://worpit.com/" class="btn"&gt;Click Me&lt;/a&gt;</p>
					<p class="code-description">This will produce a full-featured button with modern gradient, hover and click styles.</p>
					</li>
				</ul>
			</div>
			<div class="shortcode-usage">
				<ul>
					<li><span class="code">[TBS_LABEL class="important"]highlighted text[/TBS_LABEL]</span>
					<p>will give the following HTML:</p>
					<p class="code">&lt;span class="label label-important"&gt;highlighted text&lt;/span&gt;</p>
					<p class="code-description">This will highlight the text. You can optionally add a class to change the highlight colour: new, warning, important, notice</p>
					</li>
				</ul>
			</div>
			<div class="shortcode-usage">
				<p>There will be much more <a href="http://worpit.com/wordpress-twitter-bootstrap-css-plugin-home/">documentation forthcoming on the Worpit website</a>.</a></p>
			</div>
		  </div>
		  </div><!-- / span6 -->
		</div><!-- / row -->
		
		<div class="row">
		  <div class="span6">
		  </div><!-- / span6 -->
		  <div class="span6">
		  	<p></p>
		  </div><!-- / span6 -->
		</div><!-- / row -->
		
	</div><!-- / bootstrap-wpadmin -->

</div><!-- / wrap -->