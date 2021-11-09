<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wherefrom.org
 *
 * @package    Wherefrom
 * @subpackage Wherefrom/admin/partials
 */

$isWooCommerce = WherefromUtils::isWooCommerceActive()
?>

<div class="wf-wrapper">
  <div class="wf-header">
    <img src="<?php echo plugin_dir_url( __FILE__ ) . '../img/logo.png' ?>" width="48px" alt="wherefrom logo" />
  </div>
</div>

<div class="wf-content">
  <h1>Let's get you set up</h1>
  <p>We now need to go through the basic setup of the wordpress plugin</p>
  <div id="wf-setup-wizzard" class="<?php echo $isWooCommerce ? 'wc' : 'wp' ?>">
    <h3>Step 1: Claim your wherefrom brand profile</h3>
    <div>
      <p>
        If you are new to Wherefrom, we need a few details about your brand.<br />
        Please proceed to the <a href="https://wherefrom.org/brands/claim" target="_blank">Wherefrom Brand Claim</a> page,<br />
        fill in the form and we will be in contact shortly.
      </p>
      <a href="https://wherefrom.org/brands/claim" target="_blank"><button class="white">Claim Profile <i class="fas fa-external-link-alt fa-xs"></i></button></a>
      <br />
      <p>Once your profile is validated, we will provide you with a unique wherefrom url.<br />
      If you have already done that, please proceed to the next step.</p>
      <button id="wf-setup-step1-next-btn" class="green">Next</button>
    </div>
    <h3>Step 2: Set your unique wherefrom profile name</h3>
    <div>
      <p>
        We use your unique wherefrom profile name to show your score in the widget.<br />
        Your unique profile name is your last part of your profile url:
      </p>
      <div class="row">
        <div>https://www.wherefrom.org/<input id="wf-setup-seo-name-input" type="text" /></div>
        <button id="wf-setup-seo-name-save-btn" class="green">save</button>
        <div id="wf-setup-seo-name-spinner" class="lds-ring" style="margin-left: 10px; visibility: hidden;"><div></div><div></div><div></div><div></div></div>
      </div>
    </div>
    <?php if ($isWooCommerce) { ?> 
      <!-- <h3>Step 3: Enable products auto sync</h3>
      <div>
        <p>
          By enabling auto-sync your newly added products will be automatically published to wherefrom.<br />
          This will take out the hassle of having to send us over CSV files when you update your products list.<br />
          Additionally, all your products will carry a Wherefrom Score.
        </p>
        <p>To enable autosync, please request a private API by emailing us at <a href="mailto: support@wherefrom.org">support@wherefrom.org</a></p>
      
        <div class="row">
          <div>Your API key: <input id="wf-setup-api-key-input" type="text" /></div>
          <button id="wf-setup-api-key-save-btn" class="green">save & enable sync</button>
          <div id="wf-setup-api-key-spinner" class="lds-ring" style="margin-left: 10px; visibility: hidden;"><div></div><div></div><div></div><div></div></div>
        </div>
      </div> -->
    <?php } ?>
  </div>
</div>
