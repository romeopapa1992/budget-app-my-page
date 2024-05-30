$(document).ready(function() {
    // Sprawdź, czy użytkownik jest zalogowany, gdy kliknięty jest przycisk "Balance"
    $('#balance-link').on('click', function(event) {
        event.preventDefault(); // Zapobiega domyślnemu zachowaniu linku

        $.ajax({
            url: 'checkLoggedIn.php',
            method: 'GET',
            success: function(response) {
                var data = JSON.parse(response);
                if (!data.loggedIn) {
                    // Pokaż modal zamiast alertu
                    var myModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
                        keyboard: false
                    });
                    myModal.show();
                    
                    // Przekierowanie po zamknięciu modala
                    $('#exampleModal').on('hidden.bs.modal', function () {
                        window.location.href = 'signin.html';
                    });
                } else {
                    // Przekieruj do strony balance.html
                    window.location.href = 'balance.html';
                }
            }
        });
    });

    if (window.location.pathname.endsWith('balance.html'))  {
        $.ajax({
            url: 'checkLoggedIn.php',
            method: 'GET',
            success: function(response) {
                var data = JSON.parse(response);
                if (!data.loggedIn) {
                    // Pokaż modal zamiast alertu
                    var myModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
                        keyboard: false
                    });
                    myModal.show();
                    
                    // Przekierowanie po zamknięciu modala
                    $('#exampleModal').on('hidden.bs.modal', function () {
                        window.location.href = 'signin.html';
                    });
                }
            }
        });
    }

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
                if (data.error) {
                    alert(data.error);
                    window.location.href = 'signin.html';
                } else {
                    $('#balance-result').html(
                        `<h3>Balance: ${data.balance}</h3>
                        <p>Total Income: ${data.income}</p>
                        <p>Total Expense: ${data.expense}</p>`
                    );
                }
            }
        });
    });
});
