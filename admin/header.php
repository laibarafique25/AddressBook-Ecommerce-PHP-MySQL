<?php
if (!isset($con)) { include 'config.php'; }
$current_page = basename($_SERVER['PHP_SELF']);

function is_active($pages) {
    global $current_page;
    if (is_array($pages)) return in_array($current_page, $pages) ? 'active' : '';
    return $current_page == $pages ? 'active' : '';
}

function is_open($pages) {
    global $current_page;
    if (is_array($pages)) return in_array($current_page, $pages) ? 'open' : '';
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Address Book — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/admin/css/style.css" rel="stylesheet">
</head>
<body>

<aside class="sidebar" id="sidebar">
  <div class="brand">
    <div class="logo">A</div>
    <h2>Address Book</h2>
  </div>

  <div class="menu-label">Main</div>
  <a href="index.php" class="menu-item <?= is_active('index.php') ?>"><i class="fas fa-th-large"></i> Dashboard</a>

  <div class="menu-label">Catalog</div>
  <a href="javascript:void(0)" class="menu-item has-arrow <?= is_active(['view_main_cat.php','main_cat.php','view_sub_cat.php','view_child_cat.php','edit_main_cat.php','edit_sub_cat.php','edit_child_cat.php']) ?>" onclick="toggleSub('cat')"><i class="fas fa-layer-group"></i> Categories</a>
  <div class="submenu <?= is_open(['view_main_cat.php','main_cat.php','view_sub_cat.php','view_child_cat.php','edit_main_cat.php','edit_sub_cat.php','edit_child_cat.php']) ?>" id="sub-cat">
    <a href="view_main_cat.php">Main Categories</a>
    <a href="view_sub_cat.php">Sub Categories</a>
    <a href="view_child_cat.php">Child Categories</a>
  </div>
  <a href="view_product.php" class="menu-item <?= is_active(['view_product.php','edit_product.php']) ?>"><i class="fas fa-box"></i> Products</a>
  <a href="view_shades.php" class="menu-item <?= is_active('view_shades.php') ?>"><i class="fas fa-palette"></i> Shades</a>

  <div class="menu-label">Sales</div>
  <a href="view_orders.php" class="menu-item <?= is_active('view_orders.php') ?>"><i class="fas fa-receipt"></i> Orders</a>
  <a href="view_carts.php" class="menu-item <?= is_active('view_carts.php') ?>"><i class="fas fa-shopping-cart"></i> Carts</a>

  <div class="menu-label">Users</div>
  <a href="viewusers.php" class="menu-item <?= is_active('viewusers.php') ?>"><i class="fas fa-users"></i> Customers</a>
  <a href="javascript:void(0)" class="menu-item has-arrow <?= is_active(['addrole.php','editrole.php']) ?>" onclick="toggleSub('role')"><i class="fas fa-user-shield"></i> Roles</a>
  <div class="submenu <?= is_open(['addrole.php','editrole.php']) ?>" id="sub-role">
    <a href="addrole.php">Manage Roles</a>
  </div>
</aside>

<div class="main">
  <header class="topbar">
    <button class="icon-btn d-md-none" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button>
    
    <div class="search" style="position:relative;">
      <i class="fas fa-search"></i>
      <input type="text" id="search_input" placeholder="Search anything on this page..." autocomplete="off">
    </div>

    <div class="top-right">
      <button class="icon-btn"><i class="far fa-envelope"></i><span class="dot"></span></button>
      <button class="icon-btn"><i class="far fa-bell"></i><span class="dot"></span></button>
      <div class="profile">
        <div class="avatar">A</div>
        <div>
          <div class="name">Admin</div>
          <div class="role">Super Admin</div>
        </div>
      </div>
    </div>
  </header>

  <main class="content">

<script>
// Sidebar Toggle
function toggleSub(id){
    document.getElementById('sub-'+id).classList.toggle('open');
}

// --- DYNAMIC LIVE SEARCH SCRIPT ---
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search_input');
    
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        // Page par kisi bhi table ko dhoondo
        const tables = document.querySelectorAll('table');
        
        tables.forEach(table => {
            const rows = table.getElementsByTagName('tr');

            // Header skip karke (i=1) baaki saari rows check karo
            for (let i = 1; i < rows.length; i++) {
                let rowData = rows[i].textContent.toLowerCase();
                
                // Agar word match kare to dikhao, warna hide kar do
                if (rowData.indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        });
    });
});
</script>