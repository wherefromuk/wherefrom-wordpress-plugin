<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wherefrom.org
 * @since      1.0.0
 *
 * @package    Wherefrom
 * @subpackage Wherefrom/admin/partials
 */
?>

<div class="wf-wrapper">
  <div class="wf-header">
    <img src="https://docs.wherefrom.org/logo.png" width="48px" alt="wherefrom logo" />
  </div>
</div>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wf-content">
	<h2>Wherefrom Settings</h2>  
	<!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
	<?php settings_errors(); ?>  
	<form method="POST" action="options.php">  
		<?php 
			settings_fields( 'wherefrom_general_settings' );
			do_settings_sections( 'wherefrom_general_settings' ); 
		?>             
		<?php submit_button(); ?>  
	</form> 
</div>