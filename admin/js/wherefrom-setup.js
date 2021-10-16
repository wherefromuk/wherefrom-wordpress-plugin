(function ($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  $(function () {
    jQuery(document).ready(function () {
      $("#wherefrom_categories_to_exclude").multiSelect({ keepOrder: true });

      $("#wf-setup-wizzard").css("display", "block");
      // registewr ui plugins
      $("#wf-setup-wizzard").steps({
        headerTag: "h3",
        bodyTag: "div",
        transitionEffect: "slideLeft",
        autoFocus: false,
        enableKeyNavigation: false,
        titleTemplate: "#title#",
        labels: {
          current: "",
        },
      });

      // register event handlers
      $("#wf-setup-step1-next-btn").click(function () {
        $("#wf-setup-wizzard").steps("next");
      });

      // seoName saving
      $("#wf-setup-seo-name-save-btn").click(function () {
        $("#wf-setup-seo-name-spinner").show();
        $("#wf-setup-seo-name-spinner").css("visibility", "visible");
        $("#wf-setup-seo-name-save-btn").prop("disabled", true);

        // save seoName
        $.ajax({
          type: "POST",
          url: wf.restURL + "wf/v1/settings/seo-name",
          beforeSend: function (xhr) {
            xhr.setRequestHeader("X-WP-Nonce", wf.restNonce);
          },
          data: {
            seoName: $("#wf-setup-seo-name-input").val(),
          },
          success: function (response) {
            if (response) {
              window.location.reload();
            }

            // if (response && !!response.seoName) {
            //   if ($("#wf-setup-wizzard").hasClass("wp")) {
            //     window.location.reload();
            //   } else {
            //     // proceed to step 3 if woocommerce
            //     $("#wf-setup-wizzard").steps("next");
            //   }
            // }
          },
        });
      });

      // save api key
      $("#wf-setup-api-key-save-btn").click(function () {
        $("#wf-setup-api-key-spinner").show();
        $("#wf-setup-api-key-spinner").css("visibility", "visible");
        $("#wf-setup-api-key-save-btn").prop("disabled", true);

        $.ajax({
          type: "POST",
          url: wf.restURL + "wf/v1/settings/api-key",
          beforeSend: function (xhr) {
            xhr.setRequestHeader("X-WP-Nonce", wf.restNonce);
          },
          data: {
            key: $("#wf-setup-api-key-input").val(),
          },
          success: function (response) {
            if (response) {
              window.location.reload();
            }
          },
        });
      });
    });
  });
})(jQuery);
