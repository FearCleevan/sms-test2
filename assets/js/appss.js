$(document).ready(function () {
    var table = $("#example").DataTable({
      dom: "<'row'<'col-sm-12 col-md-6'l>" +
        "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6 text-right'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-5'i>>" +
        "<'col-sm-12 col-md-6 text-right'p>>",
      buttons: [
        {
          extend: "copy",
          text: "Copy",
          exportOptions: {
            rows: ":visible",
            columns: ":not(:last-child)", // Exclude the last column (Action column)
          },
        },
        {
          extend: "csv",
          text: "CSV",
          exportOptions: {
            rows: ":visible",
            columns: ":not(:last-child)", // Exclude the last column (Action column)
          },
        },
        {
          extend: "excel",
          text: "Excel",
          exportOptions: {
            rows: ":visible",
            columns: ":not(:last-child)", // Exclude the last column (Action column)
          },
        },
        {
          extend: "pdf",
          text: "PDF",
          exportOptions: {
            rows: ":visible",
            columns: ":not(:last-child)", // Exclude the last column (Action column)
          },
        },
        {
          extend: "print",
          text: "Print",
          exportOptions: {
            rows: ":visible",
            columns: ":not(:last-child)", // Exclude the last column (Action column)
          },
        },
      ],
      responsive: true,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      columnDefs: [
        // Disable filtering for the Profile (1st column) and Action (last column)
        {
          targets: [1, 11], // Index of Profile and Action columns (starting from 0)
          searchable: false, // Disable search
          orderable: false, // Disable ordering
        },
      ],
    });
  });
  