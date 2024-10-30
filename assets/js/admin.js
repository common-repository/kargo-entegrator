const $ = window.jQuery;

$("#wc-orders-filter").on("submit", function (event) {
  if ($("#bulk-action-selector-top").val() === "gcargo_print_bulk_shipments") {
    event.preventDefault();

    const data = $(this)
      .serializeArray()
      .filter((item) => item.name === "id[]")
      .map((item) => item.value);

    $.ajax({
      url: `${window.gcargo.ajaxurl}?action=gcargo_bulk_print&_wpnonce=${window.gcargo.nonce}`,
      type: "POST",
      dataType: "json",
      contentType: "application/json",
      accept: "application/json",
      data: JSON.stringify(data),
      success: (response) => {
        window.open(response, "_blank");
      },
    });
  }
});
