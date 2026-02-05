<?php
session_start();
include(__DIR__ . "/cp_connect.php");

// --- AUTH CHECK ---
if (!isset($_SESSION['email'])) {
    $_SESSION['login_error'] = "❌ Please login first!";
    header("Location: ../index.php");
    exit();
}

// ----------------- METRICS -----------------
// counts (safe fallbacks with ?? 0)
$complaintsCount = $conn->query("SELECT COUNT(*) as total FROM report")->fetch_assoc()['total'] ?? 0;
$casesCount = $conn->query("SELECT COUNT(*) as total FROM cases")->fetch_assoc()['total'] ?? 0;
$arrestedCount = $conn->query("SELECT COUNT(*) as total FROM arrestees")->fetch_assoc()['total'] ?? 0;

$pendingComplaints = $conn->query("SELECT COUNT(*) as total FROM report WHERE status='Pending'")->fetch_assoc()['total'] ?? 0;
$solvedCases = $conn->query("SELECT COUNT(*) as total FROM cases WHERE status='Solved'")->fetch_assoc()['total'] ?? 0;
$ongoingInvestigations = $conn->query("SELECT COUNT(*) as total FROM cases WHERE status='Ongoing'")->fetch_assoc()['total'] ?? 0;
$evidenceLogged = $conn->query("SELECT COUNT(*) as total FROM evidence")->fetch_assoc()['total'] ?? 0;

// tolerant officer count to avoid missing role naming
$activeOfficers = $conn->query("SELECT COUNT(*) as total FROM users.users WHERE role IN ('Police','police','user','User')")->fetch_assoc()['total'] ?? 0;

// ----------------- DATA FOR CHARTS (LOAD ALL) -----------------

// --- 1) COMPLAINTS (report table)
// Bar: complaints per year
$complaintsYear = [];
$res = $conn->query("SELECT YEAR(date_reported) as yr, COUNT(*) as total FROM report GROUP BY YEAR(date_reported) ORDER BY yr");
while ($r = $res->fetch_assoc()) $complaintsYear[$r['yr']] = (int)$r['total'];

// Pie: complaint type distribution (user requested type)
$complaintsType = [];
$res = $conn->query("SELECT `type_of_crime`, COUNT(*) as total FROM report GROUP BY `type_of_crime`");
while ($r = $res->fetch_assoc()) $complaintsType[$r['type_of_crime']] = (int)$r['total'];

// Line: monthly complaints for current year (Jan..Dec)
$complaintsMonthly = array_fill(1, 12, 0);
$res = $conn->query("SELECT MONTH(date_reported) as mn, COUNT(*) as total FROM report WHERE YEAR(date_reported)=YEAR(CURDATE()) GROUP BY mn");
while ($r = $res->fetch_assoc()) $complaintsMonthly[(int)$r['mn']] = (int)$r['total'];

// --- 2) CASES (cases table)
// Bar: cases per year
$casesYear = [];
$res = $conn->query("SELECT YEAR(FiledDate) as yr, COUNT(*) as total FROM cases GROUP BY YEAR(FiledDate) ORDER BY yr");
while ($r = $res->fetch_assoc()) $casesYear[$r['yr']] = (int)$r['total'];

// Pie: case type distribution
$casesType = [];
$res = $conn->query("SELECT CaseType, COUNT(*) as total FROM cases GROUP BY CaseType");
while ($r = $res->fetch_assoc()) $casesType[$r['CaseType']] = (int)$r['total'];

// Line: monthly cases for current year
$casesMonthly = array_fill(1, 12, 0);
$res = $conn->query("SELECT MONTH(FiledDate) as mn, COUNT(*) as total FROM cases WHERE YEAR(FiledDate)=YEAR(CURDATE()) GROUP BY mn");
while ($r = $res->fetch_assoc()) $casesMonthly[(int)$r['mn']] = (int)$r['total'];

// --- 3) ARRESTEES (arrestees table)
// Bar: arrests per year
$arrestYear = [];
$res = $conn->query("SELECT YEAR(arrest_date) as yr, COUNT(*) as total FROM arrestees GROUP BY YEAR(arrest_date) ORDER BY yr");
while ($r = $res->fetch_assoc()) $arrestYear[$r['yr']] = (int)$r['total'];

// Pie: gender distribution
$arrestGender = [];
$res = $conn->query("SELECT gender, COUNT(*) as total FROM arrestees GROUP BY gender");
while ($r = $res->fetch_assoc()) $arrestGender[$r['gender']] = (int)$r['total'];

// Line: monthly arrests for current year
$arrestMonthly = array_fill(1, 12, 0);
$res = $conn->query("SELECT MONTH(arrest_date) as mn, COUNT(*) as total FROM arrestees WHERE YEAR(arrest_date)=YEAR(CURDATE()) GROUP BY mn");
while ($r = $res->fetch_assoc()) $arrestMonthly[(int)$r['mn']] = (int)$r['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>PIMS - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">

    <!-- Chart.js + DataLabels -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

    <style>
        
        
    /* === Glassmorphic Charts & Cards === */
.charts-section { margin-top: 30px; }
.charts { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; align-items: stretch; }

.chart-card {
    background: rgba(255, 255, 255, 0.08);       /* translucent glass */
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 15px;
    border: 1px solid rgba(255,255,255,0.15);
    padding: 20px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 380px;
    transition: all 0.25s ease;
    color: #fff; /* make text readable on glass */
}

.chart-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.chart-card h2 {
    text-align: center;
    margin-bottom: 12px;
    font-size: 18px;
    color: #fff;
}

.chart-card canvas {
    flex: 1;
    height: 300px !important;
    width: 100% !important;
    margin-top: 6px;
}

.switcher { display:flex; gap:10px; justify-content:flex-end; margin-bottom:6px; }
.switcher button { padding:8px 14px; border-radius:8px; border:0; cursor:pointer; background:#eee; font-weight:600; transition:0.18s; }
.switcher button.active { background:#1E90FF; color:#fff; }
.switcher button:hover { transform:translateY(-2px); }

/* Responsive adjustments */
@media(max-width:768px){ 
    .charts { grid-template-columns:1fr; } 
    .chart-card { height:360px; } 
}

/* Glass style for reminders */
#reminders {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 400px;
    overflow-y: auto;
    padding-right: 6px;
    margin-top: 80px;
}

.reminder-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    padding: 12px 15px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.reminder-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 24px rgba(0,0,0,0.2);
}

.reminder-text p {
    margin: 0;
    font-weight: 500;
}
.reminder-text small {
    color: #ccc;
    font-size: 0.85em;
}

.reminder-card.empty {
    justify-content: center;
    color: #aaa;
    font-style: italic;
}



body {
    margin: 0;
    font-family: "Poppins", sans-serif;
    min-height: 100vh;
    display: flex;
    background: url('../pnp2.jpg') no-repeat center center/cover;
    position: relative;
}
/* === Dark overlay === */
body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background:   rgba(0, 10, 25, 0.45);
    z-index: -1;
}

/* === Optional: Soft glow behind main container === */
.glow-behind {
    position: fixed;
    width: 500px;
    height: 500px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, rgba(0,255,204,0.4) 0%, rgba(0,0,0,0) 70%);
    border-radius: 50%;
    z-index: -1;
    animation: pulseGlow 6s ease-in-out infinite alternate;
}
@keyframes pulseGlow {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 0.7; }
    100% { transform: translate(-50%, -50%) scale(1.3); opacity: 0.3; }
}

/* === Optional: Particle background === */
#particles {
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    z-index:-1;
    pointer-events:none;
}


    </style>
</head>
<body>
<div class="container">
    <!-- SIDEBAR (unchanged look) -->
    <aside>
        <div class="toggle">
            <div class="logo">
                
                <img src="img/logo.jpg" alt="logo" />
                <h2>PI<span class="danger">MS</span></h2>
            </div>
            <div class="close" id="close-btn"><span class="material-icons-sharp">close</span></div>
        </div>
        <div class="sidebar">
            <a href="../Admin/admin_page.php" class="active"><span class="material-icons-sharp">dashboard</span><h3>Dashboard</h3></a>
            <a href="../Admin/user_management.php"><span class="material-icons-sharp">person_outline</span><h3>Users</h3></a>
            <a href="complaint_record.php"><span class="material-icons-sharp">receipt_long</span><h3>Complaint Records</h3></a>
            <a href="arrested_record.php"><span class="material-icons-sharp">receipt_long</span><h3>Arrested Records</h3></a>
            <a href="case_record.php"><span class="material-icons-sharp">receipt_long</span><h3>Case Records</h3></a>
            <a href="#"><span class="material-icons-sharp">insights</span><h3>Evidence Management</h3></a>
            <a href="#"><span class="material-icons-sharp">report_gmailerrorred</span><h3>Reports</h3></a>
            <a href="#"><span class="material-icons-sharp">settings</span><h3>Settings</h3></a>
            <a href="../logout.php" onclick="return confirm('Are you sure you want to logout?');"><span class="material-icons-sharp">logout</span><h3>Logout</h3></a>
        </div>
    </aside>

    <!-- MAIN -->
    <main>
        <h1>Analytics</h1>

        <!-- top metric cards (unchanged style) -->
        <div class="analyse">
            <div class="sales"><div class="status"><div class="info"><h3>Arrested</h3><h1><?= $arrestedCount ?></h1></div></div></div>
            <div class="visits"><div class="status"><div class="info"><h3>Cases</h3><h1><?= $casesCount ?></h1></div></div></div>
            <div class="searches"><div class="status"><div class="info"><h3>Complaints</h3><h1><?= $complaintsCount ?></h1></div></div></div>
        </div>

        <div class="analyse" style="margin-top:14px;">
            <div class="sales"><div class="status"><div class="info"><h3>Pending Complaints</h3><h1><?= $pendingComplaints ?></h1></div></div></div>
            <div class="visits"><div class="status"><div class="info"><h3>Solved Cases</h3><h1><?= $solvedCases ?></h1></div></div></div>
            <div class="searches"><div class="status"><div class="info"><h3>Ongoing Investigations</h3><h1><?= $ongoingInvestigations ?></h1></div></div></div>
        </div>

        <!-- SWITCHER -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:18px;">
            <h2 id="mainTitle" style="margin:0">Complaints Analytics</h2>
            <div class="switcher" role="tablist" aria-label="Analytics switch">
                <button class="active" data-type="complaints">Complaints</button>
                <button data-type="cases">Cases</button>
                <button data-type="arrested">Arrested</button>
            </div>
        </div>

        <!-- CHARTS: Bar | Pie | Line (Bar left, Pie right, Line full width below) -->
        <div class="charts-section">
            <div class="charts" style="grid-template-columns:1fr 1fr; margin-top:12px;">
                <div class="chart-card">
                    <h2 id="chartA_title">Bar</h2>
                    <canvas id="chartBar"></canvas>
                </div>
                <div class="chart-card">
                    <h2 id="chartB_title">Pie</h2>
                    <canvas id="chartPie"></canvas>
                </div>
            </div>

            <div class="chart-card" style="margin-top:20px; height:360px;">
                <h2 id="chartC_title">Line</h2>
                <canvas id="chartLine"></canvas>
            </div>
        </div>
    </main>

    <!-- RIGHT SECTION (unchanged) -->
    <div class="right-section">
        <div class="nav">
            <button id="menu-btn"><span class="material-icons-sharp">menu</span></button>
            <div class="dark-mode">
                <span class="material-icons-sharp active">light_mode</span>
                <span class="material-icons-sharp">dark_mode</span>
            </div>
            <div class="profile">
                <div class="info"><p>Hey, <b>Migz</b></p><small class="text-muted">Admin</small></div>
                <div class="profile-photo"><img src="img/migz.png" alt="profile"></div>
            </div>
        </div>

        <div class="user-profile">
            <div class="logo"><img src="img/logo.jpg" alt="logo"><h2>PIMS</h2><p>Police Information Management System</p></div>
        </div>

        <div id="reminders">
<?php
$today = date('Y-m-d');
$notifications = [];

// Fetch new complaints
$c = $conn->query("SELECT * FROM report WHERE DATE(time_reported) = '$today'");
while($r = $c->fetch_assoc()) {
    $notifications[] = [
        'type'=>'complaint',
        'message'=>"New complaint filed: {$r['reported_by']}",
        'time'=>$r['time_reported']
    ];
}

// Fetch new arrestees
$a = $conn->query("SELECT * FROM arrestees WHERE DATE(arrest_time) = '$today'");
while($r = $a->fetch_assoc()) {
    $notifications[] = [
        'type'=>'arrestee',
        'message'=>"New arrestee: {$r['full_name']}",
        'time'=>$r['arrest_time']
    ];
}

// Fetch new cases
$cs = $conn->query("SELECT * FROM cases WHERE DATE(FiledDate) = '$today'");
while($r = $cs->fetch_assoc()) {
    $notifications[] = [
        'type'=>'case',
        'message'=>"New case filed: {$r['CaseType']}",
        'time'=>$r['FiledDate']
    ];
}

if(count($notifications) == 0){
    echo "<div class='reminder-card empty'>No new notifications.</div>";
} else {
    foreach(array_reverse($notifications) as $n) {
        $color = $n['type']=='complaint' ? '#1E90FF' : ($n['type']=='arrestee' ? '#28a745' : '#FF8C00');
        $icon = $n['type']=='complaint' ? 'report' : ($n['type']=='arrestee' ? 'person' : 'gavel');
        echo "<div class='reminder-card' style='border-left: 5px solid $color;'>
                <span class='material-icons-sharp' style='color:$color; font-size:24px; vertical-align:middle;'>$icon</span>
                <div class='reminder-text'>
                    <p>{$n['message']}</p>
                    <small>{$n['time']}</small>
                </div>
              </div>";
    }
}
?>
</div>
</div>
    </div>
</div>

<!-- existing scripts (keep) -->
<script src="orders_admin.js"></script>
<script src="admin.js"></script>

<!-- CHARTS SCRIPT: uses loaded PHP data (option 1: load-all) -->
<script>
/* --- prepare dataset object with PHP values --- */
const datasets = {
    complaints: {
        title: "Complaints",
        bar: {
            labels: <?= json_encode(array_values(array_keys($complaintsYear))) ?>,
            data: <?= json_encode(array_values($complaintsYear)) ?>
        },
        pie: {
            labels: <?= json_encode(array_values(array_keys($complaintsType))) ?>,
            data: <?= json_encode(array_values($complaintsType)) ?>
        },
        line: {
            labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
            data: <?= json_encode(array_values($complaintsMonthly)) ?>
        },
        color: 'rgba(54,162,235,0.8)'
    },
    cases: {
        title: "Cases",
        bar: {
            labels: <?= json_encode(array_values(array_keys($casesYear))) ?>,
            data: <?= json_encode(array_values($casesYear)) ?>
        },
        pie: {
            labels: <?= json_encode(array_values(array_keys($casesType))) ?>,
            data: <?= json_encode(array_values($casesType)) ?>
        },
        line: {
            labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
            data: <?= json_encode(array_values($casesMonthly)) ?>
        },
        color: 'rgba(255,159,64,0.85)'
    },
    arrested: {
        title: "Arrested",
        bar: {
            labels: <?= json_encode(array_values(array_keys($arrestYear))) ?>,
            data: <?= json_encode(array_values($arrestYear)) ?>
        },
        pie: {
            labels: <?= json_encode(array_values(array_keys($arrestGender))) ?>,
            data: <?= json_encode(array_values($arrestGender)) ?>
        },
        line: {
            labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
            data: <?= json_encode(array_values($arrestMonthly)) ?>
        },
        color: 'rgba(75,192,192,0.85)'
    }
};

/* --- create charts (and keep references) --- */
let chartBar = null, chartPie = null, chartLine = null;

function createBar(ctx, labels, data, color, title) {
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: data,
                backgroundColor: color,
                borderRadius: 10,
                barThickness: 36
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1200, easing: 'easeOutCubic' },
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#333' } },
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { color: '#333' } }
            }
        }
    });
}

function createPie(ctx, labels, data, colors, title) {
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: data,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 18
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            animation: { animateRotate: true, animateScale: true, duration: 1200, easing: 'easeOutElastic' },
            plugins: {
                legend: { position: 'right', labels: { color: '#333' } },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => {
                        const sum = ctx.chart.data.datasets[0].data.reduce((a,b)=>a+b,0);
                        return sum ? (value*100/sum).toFixed(1) + '%' : '';
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}

function createLine(ctx, labels, data, color, title) {
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: data,
                borderColor: color,
                backgroundColor: color.replace('0.85','0.12') || 'rgba(54,162,235,0.12)',
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1200, easing: 'easeInOutQuart' },
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { color: '#333' } },
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { color: '#333' } }
            }
        }
    });
}

/* helper to generate palette (recycled colors) */
function palette(base) {
    return [
        base,
        '#FF6384',
        '#FFCE56',
        '#36A2EB',
        '#9966FF',
        '#4BC0C0'
    ];
}

/* --- render chosen dataset into the three charts --- */
function renderDataset(key) {
    const ds = datasets[key];

    // update main header
    document.getElementById('mainTitle').innerText = ds.title + " Analytics";
    document.getElementById('chartA_title').innerText = ds.title + " per Year";
    document.getElementById('chartB_title').innerText = (key === 'cases' ? 'Case Type Distribution' : (key === 'arrested' ? 'Gender Distribution' : 'Type Distribution'));
    document.getElementById('chartC_title').innerText = ds.title + " (Monthly)";

    // destroy existing charts to avoid duplicates
    if (chartBar) chartBar.destroy();
    if (chartPie) chartPie.destroy();
    if (chartLine) chartLine.destroy();

    const barCtx = document.getElementById('chartBar').getContext('2d');
    const pieCtx = document.getElementById('chartPie').getContext('2d');
    const lineCtx = document.getElementById('chartLine').getContext('2d');

    chartBar = createBar(barCtx, ds.bar.labels, ds.bar.data, ds.color, ds.title + ' (Yearly)');
    chartPie = createPie(pieCtx, ds.pie.labels, ds.pie.data, palette(ds.color), ds.title + ' (Distribution)');
    chartLine = createLine(lineCtx, ds.line.labels, ds.line.data, ds.color, ds.title + ' (Monthly)');
}

/* --- switcher buttons logic --- */
document.querySelectorAll('.switcher button').forEach(btn => {
    btn.addEventListener('click', (e) => {
        document.querySelectorAll('.switcher button').forEach(b=>b.classList.remove('active'));
        e.currentTarget.classList.add('active');
        const key = e.currentTarget.getAttribute('data-type') || e.currentTarget.innerText.trim().toLowerCase();
        // normalization: 'arrested' button text may be 'Arrested' while dataset key is 'arrested'
        const mapKey = key.toLowerCase();
        renderDataset(mapKey);
        // small fade effect
        document.querySelectorAll('.chart-card').forEach(c => { c.style.opacity = 0.0; setTimeout(()=>c.style.opacity=1, 150); });
    });
});

/* --- initialize default view --- */
window.addEventListener('load', () => {
    renderDataset('complaints');
});

setInterval(() => {
    fetch('fetch_reminder.php')
    .then(res => res.text())
    .then(html => document.getElementById('reminders').innerHTML = html);
}, 10000); // every 10 seconds




</script>
</body>
</html>
