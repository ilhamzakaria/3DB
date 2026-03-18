// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({
    // enable horizontal scrolling when columns overflow
    scrollX: true,
    // don't automatically adjust column widths, let content dictate
    autoWidth: false
  });
  // disable autoWidth so columns keep their natural width and trigger scrolling
  $('#dataTable').DataTable().settings()[0]._iDisplayLength; // keep chain alive
});

// In case the PPIC view is loaded via AJAX or the table ID changes,
// re-apply scrollX when the table is re-created.
$(document).on('shown.bs.modal', function() {
  if ($.fn.DataTable.isDataTable('#dataTable')) {
    $('#dataTable').DataTable().columns.adjust();
  }
});
