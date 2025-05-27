<script>
  $(document).ready(function () {
    $('#btnExportCsv').on('click', function (e) {
      e.preventDefault();
      const month = $('#dates').val() || new Date().getMonth() + 1;
      window.location.href = `/export/csv?month=${month}`;
    });
    $('#btnExportPdf').on('click', function (e) {
      e.preventDefault();
      const month = $('#dates').val() || new Date().getMonth() + 1;
      window.location.href = `/export/pdf?month=${month}`;
    });
    function filterTable() {
      const type = $('#typeFilter').val().toLowerCase();
      const desc = $('#descriptionFilter').val().toLowerCase();
      const startDate = $('#startDate').val();
      const endDate = $('#endDate').val();

      $('#transactionsTable tbody tr').each(function () {
        const row = $(this);
        const rowType = row.find('td:eq(0)').text().toLowerCase();
        const rowDesc = row.find('td:eq(3)').text().toLowerCase();
        const rawDate = row.find('td:eq(1)').text().trim();
        const parts = rawDate.split('/');
        
        let rowDate = '';
        if (parts.length === 3) {
          rowDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
        }

        const typeMatch = !type || rowType.includes(type);
        const descMatch = !desc || rowDesc.includes(desc);
        const startMatch = !startDate || rowDate >= startDate;
        const endMatch = !endDate || rowDate <= endDate;

        row.toggle(typeMatch && descMatch && startMatch && endMatch);
      });
    }
    $('#typeFilter, #descriptionFilter, #startDate, #endDate').on('change keyup', filterTable);
  });
</script>