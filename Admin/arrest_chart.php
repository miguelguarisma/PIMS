<?php
session_start();
include(__DIR__ . "/cp_connect.php");

// --- Arrestees per year (Bar Chart)
$arresteesPerYear = [];
$res = $conn->query("SELECT YEAR(arrest_date) as yr, COUNT(*) as total FROM arrestees GROUP BY YEAR(arrest_date) ORDER BY yr");
while($row = $res->fetch_assoc()){
    $arresteesPerYear[$row['yr']] = (int)$row['total'];
}

// --- Pie chart: Gender distribution
$genderData = [];
$res = $conn->query("SELECT Gender, COUNT(*) as total FROM arrestees GROUP BY Gender");
while($row = $res->fetch_assoc()){
    $genderData[$row['Gender']] = (int)$row['total'];
}

// --- Line chart: Monthly arrests for current year
$monthlyData = array_fill(1, 12, 0);
$res = $conn->query("SELECT MONTH(arrest_date) as mn, COUNT(*) as total FROM arrestees WHERE YEAR(arrest_date)=YEAR(CURDATE()) GROUP BY mn");
while($row = $res->fetch_assoc()){
    $monthlyData[$row['mn']] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Arrestees Dashboard</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<style>
.chart-card { margin-bottom: 30px; }
.chart-card h5 { font-weight: bold; }
</style>
</head>
<body>
<div class="container my-4">

    <h2 class="mb-4 text-center">Arrestees Analytics</h2>

    <div class="row">

        <!-- Bar Chart Card -->
        <div class="col-md-6">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Arrestees per Year</h5>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart Card -->
        <div class="col-md-6">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Gender Distribution</h5>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Line Chart Card -->
        <div class="col-12">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Monthly Arrests (<?= date('Y') ?>)</h5>
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // --- Bar Chart
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($arresteesPerYear)) ?>,
            datasets: [{
                label: 'Arrestees per Year',
                data: <?= json_encode(array_values($arresteesPerYear)) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderRadius: 5
            }]
        },
        options: { responsive:true, plugins:{ legend:{ display:true } } }
    });

    // --- Pie Chart with percentages
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($genderData)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($genderData)) ?>,
                backgroundColor: ['#FF6384', '#36A2EB']
            }]
        },
        options:{
            responsive:true,
            plugins:{
                legend:{ position:'bottom', labels:{ font:{ size:14 } } },
                datalabels:{
                    color:'#fff',
                    formatter:(value, ctx)=>{
                        let sum = ctx.chart.data.datasets[0].data.reduce((a,b)=>a+b,0);
                        let pct = (value*100/sum).toFixed(1)+'%';
                        return ctx.chart.data.labels[ctx.dataIndex] + "\n" + pct;
                    },
                    font:{ weight:'bold', size:12 }
                }
            }
        },
        plugins:[ChartDataLabels]
    });

    // --- Line Chart
    new Chart(document.getElementById('lineChart'), {
        type:'line',
        data:{
            labels:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
            datasets:[{
                label:'Monthly Arrests',
                data: <?= json_encode(array_values($monthlyData)) ?>,
                borderColor:'rgba(255,99,132,1)',
                backgroundColor:'rgba(255,99,132,0.2)',
                fill:true,
                tension:0.2,
                pointRadius:5,
                pointHoverRadius:8
            }]
        },
        options:{ responsive:true }
    });
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
