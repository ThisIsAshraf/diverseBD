<?php
// manage_portal_x92/analytics.php
require_once __DIR__ . '/../app/bootstrap.php';

admin_required();

// 1. Fetch Today's Overall Stats
$todayStats = db()->query("
    SELECT 
        (SELECT COUNT(*) FROM visitor_logs WHERE DATE(created_at) = CURDATE()) AS total_views,
        (SELECT COUNT(DISTINCT ip_address) FROM visitor_logs WHERE DATE(created_at) = CURDATE()) AS unique_visitors,
        (SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()) AS total_orders,
        (SELECT COALESCE(SUM(total), 0) FROM orders WHERE DATE(created_at) = CURDATE()) AS total_revenue
")->fetch(PDO::FETCH_ASSOC);

$viewsToday = (int)$todayStats['total_views'];
$uniquesToday = (int)$todayStats['unique_visitors'];
$ordersToday = (int)$todayStats['total_orders'];
$revenueToday = (float)$todayStats['total_revenue'];

// 2. Handle Manual "Send Email Snapshot" Button Click
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_snapshot_email') {
    verify_csrf();

    $adminEmail = trim(site_setting('admin_email', envv('MAIL_FROM_ADDRESS', '')));

    if ($adminEmail !== '') {
        $subject = '📊 Daily Analytics Snapshot - ' . date('d M, Y') . ' (' . store_name() . ')';
        $body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff;">
            <h2 style="color: #2563eb; margin-top: 0; border-bottom: 2px solid #2563eb; padding-bottom: 10px;">
                ' . e(store_name()) . ' - Analytics Snapshot
            </h2>
            <p style="color: #475569; font-size: 14px;">Here is your store performance snapshot for <b>' . date('l, d F Y') . '</b>:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr style="background: #f8fafc;">
                    <td style="padding: 12px; border: 1px solid #e2e8f0; font-weight: bold; color: #334155;">Total Page Views</td>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; text-align: right; font-weight: bold; color: #2563eb;">' . number_format($viewsToday) . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; font-weight: bold; color: #334155;">Unique Visitors</td>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; text-align: right; font-weight: bold; color: #0d9488;">' . number_format($uniquesToday) . '</td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td style="padding: 12px; border: 1px solid #e2e8f0; font-weight: bold; color: #334155;">Orders Placed Today</td>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; text-align: right; font-weight: bold; color: #16a34a;">' . number_format($ordersToday) . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; font-weight: bold; color: #334155;">Today\'s Total Revenue</td>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; text-align: right; font-weight: bold; color: #0f172a;">' . money($revenueToday) . '</td>
                </tr>
            </table>

            <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                Generated manually from Admin Portal at ' . date('h:i A') . '
            </p>
        </div>';

        $sent = send_store_email($adminEmail, 'Admin', $subject, $body);

        if ($sent) {
            flash('success', 'Analytics snapshot successfully emailed to ' . e($adminEmail) . '!');
        } else {
            flash('error', 'Failed to send email. Please check your SMTP settings in Site Settings or .env file.');
        }
    } else {
        flash('error', 'Admin email is not configured in site settings or .env file.');
    }

    redirect('manage_portal_x92/analytics.php');
}

// 3. Automated Hosting Threshold Logic
$hostingStatus = 'NORMAL';
$advisoryMessage = 'Your current traffic is well within free hosting limits.';

if ($viewsToday >= 500 || $uniquesToday >= 100 || $ordersToday >= 15) {
    $hostingStatus = 'WARNING';
    $advisoryMessage = '<b>Action Recommended:</b> Your traffic/order volume is approaching free hosting thresholds. Consider upgrading to standard shared hosting or a VPS to prevent site downtime or CPU throttling.';
}

// 4. Fetch Hourly Breakdown
$hourlyDataStmt = db()->query("
    SELECT 
        HOURS.hr AS hour,
        COUNT(v.id) AS visits,
        COUNT(DISTINCT v.ip_address) AS unique_visits,
        COUNT(DISTINCT o.id) AS orders
    FROM (
        SELECT 0 AS hr UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
        UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 
        UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 
        UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 
        UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23
    ) AS HOURS
    LEFT JOIN visitor_logs v 
        ON HOUR(v.created_at) = HOURS.hr AND DATE(v.created_at) = CURDATE()
    LEFT JOIN orders o 
        ON HOUR(o.created_at) = HOURS.hr AND DATE(o.created_at) = CURDATE()
    GROUP BY HOURS.hr
    ORDER BY HOURS.hr ASC
");
$hourlyData = $hourlyDataStmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$visits = [];
$uniques = [];
$orders = [];

foreach ($hourlyData as $row) {
    $labels[] = sprintf("%02d:00", $row['hour']);
    $visits[] = (int)$row['visits'];
    $uniques[] = (int)$row['unique_visits'];
    $orders[] = (int)$row['orders'];
}

$flashSuccess = flash('success');
$flashError = flash('error');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic & Sales Analytics - Admin Portal</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #0f172a;
            color: #fff;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            margin: 0 0 8px;
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        .card .value {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
        }

        .chart-box {
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .alert-banner {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-normal {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .alert-warning {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .flash-message {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .flash-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .flash-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Actions -->
        <div class="header">
            <h2>Real-Time Traffic & Order Monitoring</h2>
            <div class="btn-group">
                <!-- Test / Manual Email Snapshot Button -->
                <form method="POST" style="margin:0;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="send_snapshot_email">
                    <button type="submit" class="btn btn-secondary">
                        ✉️ Email Snapshot to Admin
                    </button>
                </form>

                <button onclick="location.reload()" class="btn btn-primary">🔄 Refresh Data</button>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($flashSuccess): ?>
            <div class="flash-message flash-success"><?= e($flashSuccess) ?></div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="flash-message flash-error"><?= e($flashError) ?></div>
        <?php endif; ?>

        <!-- Server Capacity Advisory Banner -->
        <div class="alert-banner <?= $hostingStatus === 'WARNING' ? 'alert-warning' : 'alert-normal' ?>">
            <?= $advisoryMessage ?>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid">
            <div class="card">
                <h3>Total Page Views (Today)</h3>
                <div class="value"><?= number_format((float)$todayStats['total_views']) ?></div>
            </div>
            <div class="card">
                <h3>Unique Visitors (Today)</h3>
                <div class="value"><?= number_format((float)$todayStats['unique_visitors']) ?></div>
            </div>
            <div class="card">
                <h3>Orders Placed (Today)</h3>
                <div class="value"><?= number_format((float)$todayStats['total_orders']) ?></div>
            </div>
            <div class="card">
                <h3>Today's Revenue</h3>
                <div class="value"><?= money($todayStats['total_revenue']) ?></div>
            </div>
        </div>

        <!-- Hourly Activity Graph -->
        <div class="chart-box">
            <h3 style="margin-top: 0; margin-bottom: 16px;">Hourly Activity Breakdown (24 Hours)</h3>
            <canvas id="hourlyChart" height="100"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('hourlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                        label: 'Page Views',
                        data: <?= json_encode($visits) ?>,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Unique Visitors',
                        data: <?= json_encode($uniques) ?>,
                        borderColor: '#0d9488',
                        backgroundColor: 'transparent',
                        borderDash: [5, 5],
                        tension: 0.3
                    },
                    {
                        label: 'Orders Placed',
                        data: <?= json_encode($orders) ?>,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.2)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>