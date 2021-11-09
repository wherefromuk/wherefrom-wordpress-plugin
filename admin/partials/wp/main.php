<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wherefrom.org
 * @since      1.0.0s
 *
 * @package    Wherefrom
 * @subpackage Wherefrom/admin/partials
 */
?>

<div class="wf-wrapper">
  <div class="wf-header">
    <img src="<?php echo plugin_dir_url( __FILE__ ) . '../../img/logo.png' ?>" width="48px" alt="wherefrom logo" />
  </div>
</div>

<div class="wf-content">
  <h1>You are all set!</h1>
  <p>You can now use the wherefrom score wigets within your pages, by using the two shortcodes provided:</p>
  <br />
  <h3>Banner widget: <code>[wherefrom_brand_widget_banner]</code></h3>
  <br />
  <?php echo do_shortcode('[wherefrom_brand_widget_banner]'); ?>
  <br />
  <br />
  <br />
  <h3>Stacked widget: <code>[wherefrom_brand_widget_stacked]</code></h3>
  <br />
  <?php echo do_shortcode('[wherefrom_brand_widget_stacked]'); ?>
  <br />
  <br />
  <p>You can find more details on the <a href="https://docs.wherefrom.org/wordpress-plugin#shortcodes" target="_blank">Wherefrom Docs</a> page.</p>
  <p>Alternatively please feel free to email us at <a href="mailto: support@wherefrom.org">support@wherefrom.org</a></p>
</div>



