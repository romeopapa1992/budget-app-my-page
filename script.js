$(document).ready(function() {
    $('#period').change(function() {
      if ($(this).val() === 'custom') {
        $('#custom-date-range').removeClass('d-none');
      } else {
        $('#custom-date-range').addClass('d-none');
      }
    });

    $('#balance-form').submit(function(event) {
      event.preventDefault();
      var period = $('#period').val();
      var startDate = $('#startDate').val();
      var endDate = $('#endDate').val();

      $.ajax({
        url: 'balance.php',
        method: 'POST',
        data: { period: period, startDate: startDate, endDate: endDate },
        success: function(response) {
          var data = JSON.parse(response);
          $('#balance-result').html(
            `<h3>Balance: ${data.balance}</h3>
            <p>Total Income: ${data.income}</p>
            <p>Total Expense: ${data.expense}</p>`
          );
        }
      });
    });
  });