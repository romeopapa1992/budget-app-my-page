$(document).ready(function() {

    const signinForm = $("#signin-form");
    const inputs = signinForm.find("input");
  
    signinForm.submit(function(event) {
      event.preventDefault();
      let hasError = false;
  
      inputs.each(function() {
        const value = $(this).val().trim();
        const errorElement = $(this).siblings(".error-text");
  
        if (value === "") {
          $(this).addClass("error");
          errorElement.show();
          hasError = true;
        } else {
          $(this).removeClass("error");
          errorElement.hide();
        }
      });
  
      if (!hasError) {
        $.ajax({
          url: 'signin.php',
          type: 'POST',
          data: signinForm.serialize(),
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              alert('Logowanie zakończone sukcesem!');
              window.location.href = 'balance.html';
            } else {
              alert(response.message);
            }
          }
        });
      }
    });

    function isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }

     // Check if user is logged in on "Balance" link click
     $('#balance-link').on('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
    
        $.ajax({
          url: 'checkLoggedIn.php',
          method: 'GET',
          success: function(response) {
            var data = JSON.parse(response);
            if (!data.loggedIn) {
              // Show modal instead of alert
              var myModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
                keyboard: false
              });
              myModal.show();
    
              // Redirect after modal close
              $('#exampleModal').on('hidden.bs.modal', function () {
                window.location.href = 'signin.html';
              });
            } else {
              // Redirect to balance.html
              window.location.href = 'balance.html';
            }
          }
        });
      });
    
      // Toggle custom date range visibility on period change
      $('#period').change(function() {
        if ($(this).val() === 'custom') {
          $('#custom-date-range').removeClass('d-none');
        } else {
          $('#custom-date-range').addClass('d-none');
        }
        // Clear the balance result when period is changed
        $('#balance-result').html('');
      });
    
      // Handle balance form submission and display results
      $('#balance-form').submit(function(event) {
        event.preventDefault();
        var period = $('#period').val();
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
    
        if (period === 'custom' && (!startDate || !endDate)) {
          alert('Please select both start and end dates for custom period.');
          return;
        }
    
        $.ajax({
          url: 'balance.php',
          method: 'POST',
          data: { period: period, startDate: startDate, endDate: endDate },
          success: function(response) {
              var data = JSON.parse(response);
              if (data.error) {
                  alert(data.error);
              } else {
                  $('#balance-result').html(
                      `Balance: ${data.balance}
                      Total Income: ${data.income}
                      Total Expense: ${data.expense}`
                  );
              }
          }
      });
  });
  
  $('#clear-button').on('click', function() {
      $('#balance-form')[0].reset();
      $('#custom-date-range').addClass('d-none');
      $('#balance-result').html('');
  });
  
});
