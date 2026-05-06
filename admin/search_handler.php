<?php
include 'config.php';
header('Content-Type: application/json');

$search = isset($_GET['q']) ? mysqli_real_escape_string($con, trim($_GET['q'])) : '';
$page = isset($_GET['page']) ? $_GET['page'] : '';
$results = [];

if (empty($search) || strlen($search) < 1) {
    echo json_encode(['success' => false, 'message' => 'Search query too short']);
    exit;
}

$search_term = "%$search%";

// Search Products
if ($page === 'products' || $page === '') {
    $query = "SELECT p.*, s.sub_cat_name, m.mcat_name
              FROM product p
              LEFT JOIN sub_cat s ON p.scat_id=s.sub_cat_id
              LEFT JOIN main_cat m ON s.mcat_id=m.mcat_id
              WHERE p.p_name LIKE '$search_term' OR p.pro_description LIKE '$search_term'
              ORDER BY p.p_id DESC LIMIT 20";
    
    $rows = mysqli_query($con, $query);
    while ($r = mysqli_fetch_assoc($rows)) {
        $results[] = [
            'type' => 'product',
            'id' => $r['p_id'],
            'name' => $r['p_name'],
            'description' => substr($r['pro_description'], 0, 50) . '...',
            'category' => $r['mcat_name'],
            'price' => $r['p_price'],
            'page' => 'view_product.php'
        ];
    }
}

// Search Users
if ($page === 'users' || $page === '') {
    $query = "SELECT u.*, r.name role_name 
              FROM users u 
              LEFT JOIN role r ON u.role_id=r.id
              WHERE u.name LIKE '$search_term' OR u.email LIKE '$search_term' OR u.phone LIKE '$search_term'
              ORDER BY u.user_id DESC LIMIT 20";
    
    $rows = mysqli_query($con, $query);
    while ($r = mysqli_fetch_assoc($rows)) {
        $results[] = [
            'type' => 'user',
            'id' => $r['user_id'],
            'name' => $r['name'],
            'email' => $r['email'],
            'phone' => $r['phone'] ?? '-',
            'page' => 'viewusers.php'
        ];
    }
}

// Search Orders
if ($page === 'orders' || $page === '') {
    $query = "SELECT o.*, u.name, u.email
              FROM orders o 
              LEFT JOIN users u ON o.user_id=u.user_id
              WHERE o.order_id LIKE '$search_term' OR u.name LIKE '$search_term' OR u.email LIKE '$search_term'
              ORDER BY o.order_id DESC LIMIT 20";
    
    $rows = mysqli_query($con, $query);
    while ($r = mysqli_fetch_assoc($rows)) {
        $results[] = [
            'type' => 'order',
            'id' => $r['order_id'],
            'name' => $r['name'],
            'email' => $r['email'],
            'total' => $r['total_amount'],
            'status' => $r['status'],
            'page' => 'view_orders.php'
        ];
    }
}

// Search Main Categories
if ($page === 'categories' || $page === '') {
    $query = "SELECT * FROM main_cat WHERE mcat_name LIKE '$search_term' LIMIT 20";
    $rows = mysqli_query($con, $query);
    while ($r = mysqli_fetch_assoc($rows)) {
        $results[] = [
            'type' => 'main_category',
            'id' => $r['mcat_id'],
            'name' => $r['mcat_name'],
            'page' => 'view_main_cat.php'
        ];
    }
}

// Search Sub Categories
if ($page === 'categories' || $page === '') {
    $query = "SELECT s.*, m.mcat_name FROM sub_cat s LEFT JOIN main_cat m ON s.mcat_id=m.mcat_id WHERE s.sub_cat_name LIKE '$search_term' LIMIT 20";
    $rows = mysqli_query($con, $query);
    while ($r = mysqli_fetch_assoc($rows)) {
        $results[] = [
            'type' => 'sub_category',
            'id' => $r['sub_cat_id'],
            'name' => $r['sub_cat_name'],
            'parent' => $r['mcat_name'],
            'page' => 'view_sub_cat.php'
        ];
    }
}

// Search Shades
if ($page === 'shades' || $page === '') {
    $query = "SELECT * FROM shades WHERE shade_name LIKE '$search_term' LIMIT 20";
    $rows = mysqli_query($con, $query);
    while ($r = mysqli_fetch_assoc($rows)) {
        $results[] = [
            'type' => 'shade',
            'id' => $r['shade_id'],
            'name' => $r['shade_name'],
            'code' => $r['shade_code'] ?? '#000',
            'page' => 'view_shades.php'
        ];
    }
}

echo json_encode([
    'success' => count($results) > 0,
    'results' => $results,
    'count' => count($results)
]);
?>