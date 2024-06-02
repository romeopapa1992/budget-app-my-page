$(document).ready(function() {

  $("form").submit(function(event) {
    event.preventDefault();
    const form = $(this);
    if (form.attr('action') === 'registration.php') {
      validateAndSubmitForm(form, false);
    } else if (form.attr('action') === 'signin.php') {
      validateAndSubmitForm(form, true);
    }
  });

  function validateAndSubmitForm(form, isLoginForm = false) {
    const inputs = form.find("input");
    let hasError = false;

    inputs.each(function() {
      const input = $(this);
      const value = input.val().trim();
      const errorElement = input.siblings(".error-text");
      const inputType = input.attr('type');

      if (value === "") {
        showError(input, errorElement);
        hasError = true;
      } else {
        if (!isLoginForm && inputType === 'password' && !validatePassword(value)) {
          showError(input, errorElement);
          hasError = true;
        } else if (inputType === 'email' && !validateEmail(value)) {
          showError(input, errorElement);
          hasError = true;
        } else {
          hideError(input, errorElement);
        }
      }
    });

    if (!hasError) {
      submitForm(form, isLoginForm);
    }
  }

  function validatePassword(password) {
    const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\da-zA-Z]).{8,}$/;
    return passwordPattern.test(password);
  }

  function validateEmail(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
  }

  function showError(input, errorElement) {
    input.addClass("error");
    errorElement.show();
  }

  function hideError(input, errorElement) {
    input.removeClass("error");
    errorElement.hide();
  }

  function submitForm(form, isLoginForm) {
    $.ajax({
      url: form.attr('action'),
      type: 'POST',
      data: form.serialize(),
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          if (isLoginForm) {
            window.location.href = 'balance.html';
          } else {
            alert('Registration successful! You can now log in.');
            window.location.href = 'signin.html';
          }
        } else {
          alert(response.message);
        }
      }
    });
  }

  $('#balance-link').on('click', function(event) {
    event.preventDefault();
    checkUserLoggedIn();
  });

  function checkUserLoggedIn() {
    $.ajax({
      url: 'checkLoggedIn.php',
      method: 'GET',
      success: function(response) {
        const data = JSON.parse(response);
        if (!data.loggedIn) {
          showLoginModal();
        } else {
          window.location.href = 'balance.html';
        }
      },
      error: function() {
        alert('An error occurred while checking the login status.');
      }
    });
  }

  function showLoginModal() {
    const myModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
      keyboard: false
    });
    myModal.show();
    $('#exampleModal').on('hidden.bs.modal', function() {
      window.location.href = 'signin.html';
    });
  }

  $('#period').change(function() {
    toggleCustomDateRange($(this).val() === 'custom');
    $('#balance-info').addClass('d-none'); 
  });

  function toggleCustomDateRange(show) {
    $('#custom-date-range').toggleClass('d-none', !show);
  }

  $('#balance-form').submit(function(event) {
    event.preventDefault();
    handleBalanceFormSubmit();
  });

  function handleBalanceFormSubmit() {
    const period = $('#period').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();

    if (period === 'custom' && (!startDate || !endDate)) {
      const errorElementStart = $('#startDate').siblings(".error-text");
      const errorElementEnd = $('#endDate').siblings(".error-text");
      
      if (!startDate) {
        showError($('#startDate'), errorElementStart);
        hasError = true;
      } else {
        hideError($('#startDate'), errorElementStart);
      }
      
      if (!endDate) {
        showError($('#endDate'), errorElementEnd);
        hasError = true;
      } else {
        hideError($('#endDate'), errorElementEnd);
      }

      //return;
    }
    

    $.ajax({
      url: 'balance.php',
      method: 'POST',
      data: { period, startDate, endDate },
      success: function(response) {
        const data = JSON.parse(response);
        if (data.error) {
          alert(data.error);
        } else {
          displayBalanceResult(data);
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error: ', error);
        alert('An error occurred while fetching the balance.');
      }
    });
  }

  function displayBalanceResult(data) {
    $('#balance').html(`Balance: ${parseFloat(data.balance).toFixed(2)} PLN`);
    $('#total-income').html(`Total Income: ${parseFloat(data.income).toFixed(2)} PLN`);
    $('#total-expense').html(`Total Expense: ${parseFloat(data.expense).toFixed(2)} PLN`);
    $('#balance-info').removeClass('d-none');
  }

  $('#clear-button').on('click', function() {
    clearBalanceForm();
  });

  function clearBalanceForm() {
    $('#balance-form')[0].reset();
    $('#custom-date-range').addClass('d-none');
    $('#balance-info').addClass('d-none');
  }

});