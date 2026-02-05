<?php
session_start();
include(__DIR__ . "/cp_connect.php");

// --- Complaints per year (Bar Chart)
$complaintsPerYear = [];
$res = $conn->query("SELECT YEAR(date_reported) as yr, COUNT(*) as total FROM report GROUP BY YEAR(date_reported) ORDER BY yr");
while($row = $res->fetch_assoc()){
    $complaintsPerYear[$row['yr']] = (int)$row['total'];
}

// --- Pie chart: ComplaintType distribution
$complaintTypeData = [];
$res = $conn->query("SELECT type_of_crime, COUNT(*) as total FROM report GROUP BY type_of_crime");
while($row = $res->fetch_assoc()){
    $complaintTypeData[$row['type_of_crime']] = (int)$row['total'];
}

// --- Line chart: Monthly complaints current year
$monthlyComplaints = array_fill(1,12,0);
$res = $conn->query("SELECT MONTH(date_reported) as mn, COUNT(*) as total FROM report WHERE YEAR(date_reported)=YEAR(CURDATE()) GROUP BY mn");
while($row = $res->fetch_assoc()){
    $monthlyComplaints[$row['mn']] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Complaints Dashboard</title>

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
    <h2 class="mb-4 text-center">Complaints Analytics</h2>

    <div class="row">

        <!-- Bar Chart -->
        <div class="col-md-6">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Complaints per Year</h5>
                    <canvas id="complaintsBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-md-6">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Complaint Type Distribution</h5>
                    <canvas id="complaintsPieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Line Chart -->
        <div class="col-12">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Monthly Complaints (<?= date('Y') ?>)</h5>
                    <canvas id="complaintsLineChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // --- Bar Chart
    new Chart(document.getElementById('complaintsBarChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($complaintsPerYear)) ?>,
            datasets: [{
                label: 'Complaints per Year',
                data: <?= json_encode(array_values($complaintsPerYear)) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderRadius: 5
            }]
        },
        options: { responsive:true, plugins:{ legend:{ display:true } } }
    });

    // --- Pie Chart
    new Chart(document.getElementById('complaintsPieChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($complaintTypeData)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($complaintTypeData)) ?>,
                backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF','#FF9F40','#C9CBCF']
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
    new Chart(document.getElementById('complaintsLineChart'), {
        type:'line',
        data:{
            labels:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
            datasets:[{
                label:'Monthly Complaints',
                data: <?= json_encode(array_values($monthlyComplaints)) ?>,
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
