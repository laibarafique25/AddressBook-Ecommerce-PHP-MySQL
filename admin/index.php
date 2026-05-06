<?php
include 'config.php';
include 'header.php';

// 1. Live counts from DB
$products  = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) c FROM product"))['c'] ?? 0;
$customers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) c FROM users WHERE role_id != 1"))['c'] ?? 0; // Admin ko nikal kar
$orders    = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) c FROM orders"))['c'] ?? 0;
$revenue   = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(total_amount) s FROM orders"))['s'] ?? 0;

// 2. Recent orders with Customer Name
$recent_q = "SELECT o.order_id, o.total_amount, o.status, o.order_date, u.name
             FROM orders o 
             LEFT JOIN users u ON o.user_id = u.user_id
             ORDER BY o.order_id DESC LIMIT 6";
$recent = mysqli_query($con, $recent_q);

// 3. Sales chart data — Last 7 Days Dynamic Revenue
$chart_labels = []; 
$chart_data = [];
$sq = mysqli_query($con, "SELECT DATE(order_date) d, SUM(total_amount) s 
                          FROM orders
                          WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                          GROUP BY DATE(order_date) 
                          ORDER BY d ASC");

if ($sq && mysqli_num_rows($sq) > 0) {
    while ($r = mysqli_fetch_assoc($sq)) {
        $chart_labels[] = date('M d', strtotime($r['d']));
        $chart_data[] = (float)$r['s'];
    }
} else {
    // Agar data na ho toh empty states dikhane ke liye fallback
    $chart_labels = ['No Data'];
    $chart_data = [0];
}

// 4. Category distribution (Main Category wise products)
$cat_q = mysqli_query($con, "SELECT m.mcat_name, COUNT(p.p_id) c 
                             FROM main_cat m
                             LEFT JOIN sub_cat s ON s.mcat_id = m.mcat_id
                             LEFT JOIN product p ON p.scat_id = s.sub_cat_id
                             GROUP BY m.mcat_id 
                             ORDER BY c DESC LIMIT 6");
$cat_labels = []; 
$cat_data = [];
while ($cat_q && $r = mysqli_fetch_assoc($cat_q)) {
    $cat_labels[] = $r['mcat_name'];
    $cat_data[] = (int)$r['c'];
}
?>

<div class="page-header">
  <div>
    <h1>Dashboard Overview</h1>
    <div class="crumb">Welcome back, here's what's happening today</div>
  </div>
  <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-download"></i> Print Report</button>
</div>

<div class="stats-grid">
  <div class="stat-card purple">
    <div class="label">Total Products</div>
    <div class="value"><?= number_format($products) ?></div>
    <div class="delta">Active in Catalog</div>
    <div class="icon-bg"><i class="fas fa-box"></i></div>
  </div>
  <div class="stat-card orange">
    <div class="label">Total Revenue</div>
    <div class="value">Rs. <?= number_format($revenue) ?></div>
    <div class="delta">Lifetime Earnings</div>
    <div class="icon-bg"><i class="fas fa-rupee-sign"></i></div>
  </div>
  <div class="stat-card blue">
    <div class="label">Total Customers</div>
    <div class="value"><?= number_format($customers) ?></div>
    <div class="delta">Registered Users</div>
    <div class="icon-bg"><i class="fas fa-users"></i></div>
  </div>
  <div class="stat-card green">
    <div class="label">Total Orders</div>
    <div class="value"><?= number_format($orders) ?></div>
    <div class="delta">Successful Checkouts</div>
    <div class="icon-bg"><i class="fas fa-receipt"></i></div>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h3 class="card-title">Revenue Trend</h3>
        <div class="card-sub">Daily revenue for the last 7 days</div>
      </div>
      <span class="pill purple">Real-time</span>
    </div>
    <canvas id="salesChart" height="110"></canvas>
  </div>
  <div class="card">
    <h3 class="card-title">Inventory Spread</h3>
    <div class="card-sub">Products count per category</div>
    <canvas id="catChart" height="200"></canvas>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="card-title">Recent Orders</h3>
      <a href="view_orders.php" class="btn btn-soft btn-sm">Manage All <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="table-wrap">
      <table class="data">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($recent && mysqli_num_rows($recent) > 0): while($r = mysqli_fetch_assoc($recent)): ?>
          <tr>
            <td><strong>#<?= $r['order_id'] ?></strong></td>
            <td><?= htmlspecialchars($r['name'] ?? 'Guest') ?></td>
            <td><strong>Rs. <?= number_format($r['total_amount']) ?></strong></td>
            <td>
              <?php 
                $st = strtolower($r['status'] ?? 'pending'); 
                $cls = ($st=='paid' || $st=='completed' || $st=='delivered') ? 'green' : (($st=='pending') ? 'orange' : 'red');
              ?>
              <span class="pill <?= $cls ?>"><?= ucfirst($st) ?></span>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="4" class="text-center text-muted py-4">No recent orders found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <h3 class="card-title">Quick Tasks</h3>
    <div class="card-sub">Operations overview</div>
    
    <?php
    // Kuch dynamic tasks generate karte hain based on data
    $low_stock = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) c FROM product WHERE p_id NOT IN (SELECT p_id FROM order_details)"))['c'];
    ?>
    
    <div class="todo-item">
      <div style="flex:1">
        <div style="font-weight:600;font-size:13px">Orders to Process</div>
        <div class="progress orange"><span style="width:100%"></span></div>
      </div>
      <span class="pill orange"><?= $orders ?> Total</span>
    </div>
    
    <div class="todo-item">
      <div style="flex:1">
        <div style="font-weight:600;font-size:13px">Products with No Sales</div>
        <div class="progress red"><span style="width:<?= ($products > 0) ? ($low_stock/$products)*100 : 0 ?>%"></span></div>
      </div>
      <span class="pill red"><?= $low_stock ?> Items</span>
    </div>

    <div class="todo-item">
      <div style="flex:1">
        <div style="font-weight:600;font-size:13px">Average Product Rating</div>
        <?php 
            $rating_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT AVG(rating_value) r FROM product_ratings"));
            $avg_r = round($rating_data['r'] ?? 0, 1);
        ?>
        <div class="progress green"><span style="width:<?= ($avg_r/5)*100 ?>%"></span></div>
      </div>
      <span class="pill green"><?= $avg_r ?> / 5</span>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Line Chart for Sales
const sc = document.getElementById('salesChart').getContext('2d');
new Chart(sc, {
  type: 'line',
  data: {
    labels: <?= json_encode($chart_labels) ?>,
    datasets: [{
      label: 'Revenue (Rs.)',
      data: <?= json_encode($chart_data) ?>,
      borderColor: '#7c3aed',
      backgroundColor: 'rgba(124,58,237,0.1)',
      tension: 0.4,
      fill: true,
      borderWidth: 3,
      pointRadius: 4,
      pointBackgroundColor: '#7c3aed'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, grid: { color: '#f0edf7' } },
      x: { grid: { display: false } }
    }
  }
});

// Bar Chart for Categories
new Chart(document.getElementById('catChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($cat_labels) ?>,
    datasets: [{
      data: <?= json_encode($cat_data) ?>,
      backgroundColor: ['#7c3aed', '#ec4899', '#3b82f6', '#f59e0b', '#10b981', '#06b6d4'],
      borderRadius: 8,
      barThickness: 20
    }]
  },
  options: {
    indexAxis: 'y',
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: '#f0edf7' } },
      y: { grid: { display: false } }
    }
  }
});
</script>

<?php include 'footer.php'; ?>