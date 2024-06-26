<?php
// Start session
session_start();

// Include database connection
include 'db_connection.php';

// Fetch the 3 lowest stock products
$sql = "SELECT prod_Name, ProdStock FROM products ORDER BY ProdStock ASC LIMIT 3";
$result = $conn->query($sql);

// Fetch the 5 most purchased products
$product_sql = "SELECT prod_Name, SUM(total_profit) as total_profit FROM products GROUP BY prod_name ORDER BY total_profit DESC LIMIT 5";
$product_result = mysqli_query($conn, $product_sql);

// Fetch the 5 most paying customers
$customer_sql = "SELECT fname, lname, SUM(total_spent) as total_spent FROM customers GROUP BY fname ORDER BY total_spent DESC LIMIT 5";
$customer_result = mysqli_query($conn, $customer_sql);

// Fetch sales data by day
$sales_sql = "SELECT date, SUM(totalcost) AS total_sales FROM orders GROUP BY date";
$sales_result = mysqli_query($conn, $sales_sql);

// Initialize arrays for labels and sales data
$labels = [];
$data = [];

// Process sales data
if ($sales_result && mysqli_num_rows($sales_result) > 0) {
    while ($row = mysqli_fetch_assoc($sales_result)) {
        // Format date for label
        $labels[] = $row['date'];

        // Add total sales for the day
        $data[] = $row['total_sales'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Page</title>
    <!-- Include global CSS -->
    <link rel="stylesheet" href="css/global.css">
    <!-- Include header-specific CSS -->
    <link rel="stylesheet" href="css/header.css">
    <!-- Include navbar-specific CSS -->
    <link rel="stylesheet" href="css/navbar.css">
    <!-- Include footer-specific CSS -->
    <link rel="stylesheet" href="css/footer.css">
    <!-- Include reports-specific CSS -->
    <link rel="stylesheet" href="css/Preports.css">
    <!-- Include Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <body>
    <!-- Include header -->
    <?php include 'include/Pheader.php'; ?>

    <!-- Include navbar -->
    <?php include 'include/Pnavbar.php'; ?>
    
    <!-- Welcome message -->
    <div class="welcome">
        <h1>&nbsp&nbspWelcome, <?php echo isset($_SESSION['firstName']) && isset($_SESSION['lastName']) ? $_SESSION['firstName'] . ' ' . $_SESSION['lastName'] : ''; ?>!</h1>
        <h2>&nbsp&nbsp&nbspReports</h2>
    </div>
    
    <!-- Content -->
    <div class="content">
        <!-- Metrics containers -->
        <!-- Display most purchased products -->
        <div class="report-container">
            <h3>Most Purchased Products</h3>
            <ul class="report-list">
                <?php if ($product_result && mysqli_num_rows($product_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($product_result)): ?>
                        <li><?php echo $row['prod_Name']; ?> - Total Profit: RM<?php echo $row['total_profit']; ?></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No data available.</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <!-- Display most paying customers -->
        <div class="report-container">
            <h3>Most Paying Customers</h3>
            <ul class="report-list">
                <?php if ($customer_result && mysqli_num_rows($customer_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($customer_result)): ?>
                        <li><?php echo $row['fname']; ?> <?php echo $row['lname']; ?> - Total Spent: RM<?php echo $row['total_spent']; ?></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No data available.</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <!-- Display low stock products -->
        <div class="report-container">
            <h3>Products Low on Stock</h3>
            <ul class="report-list">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li><?php echo $row['prod_Name']; ?> - Stock Remaining: <?php echo $row['ProdStock']; ?></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No low stock products found.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Sales Trends Over Time -->
        <div class="report-container">
            <h3>Sales Trends Over Time</h3>
            <!-- Create Canvas Element for Chart -->
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <!-- Include footer -->
    <?php include 'include/footer.php'; ?>

    <!-- JavaScript for Sales Trends Chart -->
    <script>
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Sales Trends',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
</body>
