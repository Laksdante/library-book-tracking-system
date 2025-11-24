<?php
//staff reports
include('../../includes/staff_auth.php');
include('../../db_connect.php');
include('../../includes/path_helper.php');

//Helpers
function getMonthName($m) {
    return date ('M', mktime(0, 0, 0, $m, 1));
}

// 1. Totals

$totalBooksQ = $conn->query("SELECT COUNT(*) AS total FROM books");
$totalBooks  = $totalBooksQ->fetch_assoc()['total']?? 0;

$totalMembersQ = $conn->query("SELECT COUNT(*) AS total FROM members WHERE status='active'");
$totalMembers = $totalMembersQ->fetch_assoc()['total'] ?? 0;

$totalBorrowedQ = $conn->query("SELECT COUNT(*) AS total FROM borrow_records");
$totalBorrowed = $totalBorrowedQ->fetch_assoc()['total'] ?? 0;

$totalReturnedQ = $conn->query("SELECT COUNT(*) AS total FROM borrow_records WHERE returned_at IS NOT NULL");
$totalReturned = $totalReturnedQ->fetch_assoc()['total'] ?? 0;

$notReturned = max(0, $totalBorrowed - $totalReturned);

// Borrowing trend (monthly for current year)
$year = date('Y');
$month = [];
$monthCounts = [];
for ($m = 1; $m <= 12; $m++){
    $months[] = getMonthName($m);
    $start = "$year-" . str_pad($m,2,'0',STR_PAD_LEFT) . "-01 00:00:00";
    $end = date('Y-m-t 23:59:59', strtotime($start));
    $q = $conn->query("SELECT COUNT(*) AS total FROM borrow_records WHERE borrowed_at BETWEEN '$start' AND '$end'");
    $monthCounts[] = (int)($q->fetch_assoc()['total'] ?? 0);
}

//Returned vs not returned
$pieLabels = ['Returned', 'Not Returned'];
$pieData  = [(int)$totalReturned, (int)$notReturned];

// Most borrowed books (top 5)
$topBooks = [];
$topBooksCounts = [];
$tb = $conn->query("SELECT b.title, COUNT(*) AS times
    FROM borrow_records br
    JOIN books b ON br.book_id = b.id
    GROUP BY br.book_id
    ORDER BY times DESC
    LIMIT 5");
    while($r = $tb->fetch_assoc()){
        $topBooks[] = $r['title'];
        $topBooksCounts[] = (int)$r['times'];
    }

    // Most active Members (top 5) === Fetching from database

    $topMembers = [];
    $topMembersCounts = [];
    $tm = $conn->query(" SELECT m.full_name, COUNT(*) AS times
    FROM borrow_records br
    JOIN members m ON br.member_id = m.id
    GROUP BY br.member_id
    ORDER BY times DESC
    LIMIT 5");
    while($r = $tm->fetch_assoc()){
        $topMembers[] = $r['full_name'];
        $topMembersCounts[] = (int)$r['times'];
    }

    //prepare JSON for JS
    $months_json = json_encode($months);
    $monthCounts_json = json_encode($monthCounts);
    $pieData_json = json_encode($pieData);
    $topBooks_json = json_encode($topBooks);
    $topBooksCounts_json = json_encode($topBooksCounts);
    $topMembers_json = json_encode($topMembers);
    $topMembersCounts_json = json_encode($topMembersCounts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reports | Staff Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <style>
        /* small dashboard cards layout adjustments */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }
        .report-card {
            background: white;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        .report-card h3 { margin:0 0 8px 0; font-size:14px; color:#033B5C; }
        .report-card .value { font-size:22px; font-weight:700; color:#033B5C; }
        .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 18px; }
        @media (max-width:900px) { .charts-row { grid-template-columns: 1fr; } }
        .chart-box { background:white; padding:16px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
        .export-buttons { margin-top:12px; display:flex; gap:8px; flex-wrap:wrap; }
        .btn-export { background:#033B5C; color:#fff; padding:8px 12px; border-radius:6px; text-decoration:none; font-weight:600; }
        .small-note { color:#555; font-size:13px; margin-top:6px; }
    </style>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include('../layout/sidebar.php'); ?>
<?php include('../layout/topnav.php'); ?>

<div class="main-content">
    <h2>📊 Reports Dashboard</h2>

    <div class="reports-grid">
        <div class="report-card">
            <h3>Total Books</h3>
            <div class="value"><?= number_format($totalBooks); ?></div>
            <div class="small-note">All books in catalog</div>
        </div>

        <div class="report-card">
            <h3>Total Members</h3>
            <div class="value"><?= number_format($totalMembers); ?></div>
            <div class="small-note">Active members</div>
        </div>

        <div class="report-card">
            <h3>Total Borrowed</h3>
            <div class="value"><?= number_format($totalBorrowed); ?></div>
            <div class="small-note">All-time borrow records</div>
        </div>

        <div class="report-card">
            <h3>Currently Borrowed (Not Returned)</h3>
            <div class="value"><?= number_format($notReturned); ?></div>
            <div class="small-note">Active loans</div>
        </div>
    </div>

    <div class="export-buttons">
        <a class="btn-export" href="export_excel.php?report=overview">Export Overview (Excel)</a>
        <a class="btn-export" href="export_pdf.php?report=overview">Export Overview (PDF)</a>
    </div>

    <div class="charts-row">
        <div class="chart-box">
            <h3>Borrowing Trend (<?= $year ?>)</h3>
            <canvas id="lineTrend" height="200"></canvas>
        </div>

        <div class="chart-box">
            <h3>Returned vs Not Returned</h3>
            <canvas id="pieReturned" height="200"></canvas>
        </div>
    </div>

    <div class="charts-row" style="margin-top:18px;">
        <div class="chart-box">
            <h3>Most Borrowed Books (Top 5)</h3>
            <canvas id="barBooks" height="220"></canvas>
        </div>

        <div class="chart-box">
            <h3>Most Active Members (Top 5)</h3>
            <canvas id="barMembers" height="220"></canvas>
        </div>
    </div>

</div>

<script>
/* Palette (blue theme) */
const palette = {
    primary: '#033B5C',
    light: '#5595CC',
    mid: '#5CC4EC',
    navy: '#022D48'
};

/* Injected data from PHP */
const months = <?= $months_json ?>;
const monthCounts = <?= $monthCounts_json ?>;
const pieData = <?= $pieData_json ?>;
const topBooks = <?= $topBooks_json ?>;
const topBooksCounts = <?= $topBooksCounts_json ?>;
const topMembers = <?= $topMembers_json ?>;
const topMembersCounts = <?= $topMembersCounts_json ?>;

/* Line chart - Borrow trend */
const ctxLine = document.getElementById('lineTrend').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Borrows',
            data: monthCounts,
            fill: true,
            backgroundColor: 'rgba(85,149,204,0.12)',
            borderColor: palette.primary,
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: palette.primary
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision:0 } }
        }
    }
});

/* Pie chart - returned vs not */
const ctxPie = document.getElementById('pieReturned').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: ['Returned','Not Returned'],
        datasets: [{
            data: pieData,
            backgroundColor: [palette.light, palette.primary],
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});

/* Bar chart - top books */
const ctxBarBooks = document.getElementById('barBooks').getContext('2d');
new Chart(ctxBarBooks, {
    type: 'bar',
    data: {
        labels: topBooks,
        datasets: [{
            label: 'Times Borrowed',
            data: topBooksCounts,
            backgroundColor: palette.primary,
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});

/* Bar chart - top members */
const ctxBarMembers = document.getElementById('barMembers').getContext('2d');
new Chart(ctxBarMembers, {
    type: 'bar',
    data: {
        labels: topMembers,
        datasets: [{
            label: 'Borrows',
            data: topMembersCounts,
            backgroundColor: palette.light,
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});
</script>
</body>
</html>