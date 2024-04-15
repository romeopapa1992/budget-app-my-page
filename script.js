const periodSelect = document.getElementById('period');
const customDateRange = document.getElementById('custom-date-range');

    periodSelect.addEventListener('change', (event) => {
      if (event.target.value === 'custom') {
        customDateRange.classList.remove('d-none'); 
      } else {
        customDateRange.classList.add('d-none'); 
      }
    });

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);
    
function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Task', 'Amount'],
      ['Food', 100],
      ['Transport', 50],
      ['House', 300],
      ['Shopping', 200],
      ['Entertaiment', 80],
    ]);
      
    var options = {'width':550, 'height':400};
      
    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
      chart.draw(data, options);
}

google.charts.load('current', {'packages':['table']});
google.charts.setOnLoadCallback(drawTable);

function drawTable() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Date');
    data.addColumn('number', 'Amount (PLN)');
    data.addColumn('string', 'Category');
    data.addColumn('string', 'Comment');
    data.addRows([
        ['2024-03-01', 100, 'Food', 'Lunch with friends'],
        ['2024-03-04', 50, 'Transport', 'Bus ticket'],
        ['2024-03-11', 300, 'House', 'Groceries'],
        ['2024-03-18', 200, 'Shopping', 'New shoes'],
        ['2024-03-28', 80, 'Entertainment', 'Theatre']
    ]);

    var table = new google.visualization.Table(document.getElementById('table_div'));

    table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
}