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
      // seoName saving
      $("#wc-generate-products-csv").click(function () {
        // $("#wf-setup-seo-name-spinner").show();
        // $("#wf-setup-seo-name-spinner").css("visibility", "visible");
        // $("#wf-setup-seo-name-save-btn").prop("disabled", true);

        var data = {};

        if (
          $("#products_to_include") &&
          $("#products_to_include :selected").val() === "new-products"
        ) {
          data["afterLastExport"] = true;
        }

        // hook up csv generation
        $.ajax({
          type: "GET",
          url: wf.restURL + "wf/v1/products/csv",
          beforeSend: function (xhr) {
            xhr.setRequestHeader("X-WP-Nonce", wf.restNonce);
          },
          data: data,
          xhrFields: {
            responseType: "blob", // to avoid binary data being mangled on charset conversion
          },
          success: function (blob, status, xhr) {
            // check for a filename
            var filename = "";
            var disposition = xhr.getResponseHeader("Content-Disposition");
            if (disposition && disposition.indexOf("attachment") !== -1) {
              var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
              var matches = filenameRegex.exec(disposition);
              if (matches != null && matches[1])
                filename = matches[1].replace(/['"]/g, "");
            }

            if (typeof window.navigator.msSaveBlob !== "undefined") {
              // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
              window.navigator.msSaveBlob(blob, filename);
            } else {
              var URL = window.URL || window.webkitURL;
              var downloadUrl = URL.createObjectURL(blob);

              if (filename) {
                // use HTML5 a[download] attribute to specify filename
                var a = document.createElement("a");
                // safari doesn't support this yet
                if (typeof a.download === "undefined") {
                  window.location.href = downloadUrl;
                } else {
                  a.href = downloadUrl;
                  a.download = filename;
                  document.body.appendChild(a);
                  a.click();
                }
              } else {
                window.location.href = downloadUrl;
              }

              setTimeout(function () {
                URL.revokeObjectURL(downloadUrl);
              }, 100); // cleanup
            }
          },
        });
      });
    });
  });
})(jQuery);
