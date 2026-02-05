<?php
session_start();
include(__DIR__ . "/cp_connect.php");

// --- Cases per year (Bar Chart)
$casesPerYear = [];
$res = $conn->query("SELECT YEAR(FiledDate) as yr, COUNT(*) as total FROM cases GROUP BY YEAR(FiledDate) ORDER BY yr");
while($row = $res->fetch_assoc()){
    $casesPerYear[$row['yr']] = (int)$row['total'];
}

// --- Pie chart: CaseType distribution
$caseTypeData = [];
$res = $conn->query("SELECT CaseType, COUNT(*) as total FROM cases GROUP BY CaseType");
while($row = $res->fetch_assoc()){
    $caseTypeData[$row['CaseType']] = (int)$row['total'];
}

// --- Line chart: Monthly cases current year
$monthlyCases = array_fill(1,12,0);
$res = $conn->query("SELECT MONTH(FiledDate) as mn, COUNT(*) as total FROM cases WHERE YEAR(FiledDate)=YEAR(CURDATE()) GROUP BY mn");
while($row = $res->fetch_assoc()){
    $monthlyCases[$row['mn']] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cases Dashboard</title>

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
    <h2 class="mb-4 text-center">Cases Analytics</h2>

    <div class="row">

        <!-- Bar Chart -->
        <div class="col-md-6">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Cases per Year</h5>
                    <canvas id="casesBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-md-6">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Case Type Distribution</h5>
                    <canvas id="casesPieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Line Chart -->
        <div class="col-12">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Monthly Cases (<?= date('Y') ?>)</h5>
                    <canvas id="casesLineChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // --- Bar Chart
    new Chart(document.getElementById('casesBarChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($casesPerYear)) ?>,
            datasets: [{
                label: 'Cases per Year',
                data: <?= json_encode(array_values($casesPerYear)) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderRadius: 5
            }]
        },
        options: { responsive:true, plugins:{ legend:{ display:true } } }
    });

    // --- Pie Chart
    new Chart(document.getElementById('casesPieChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($caseTypeData)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($caseTypeData)) ?>,
                backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF']
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
    new Chart(document.getElementById('casesLineChart'), {
        type:'line',
        data:{
            labels:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
            datasets:[{
                label:'Monthly Cases',
                data: <?= json_encode(array_values($monthlyCases)) ?>,
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
