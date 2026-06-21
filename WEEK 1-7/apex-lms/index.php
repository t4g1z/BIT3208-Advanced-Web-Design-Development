<?php
// 1. Enforce strict gateway session perimeter checks
require_once __DIR__ . '/includes/auth_check.php';

// Block non-administrators from inspecting dashboard operational views
if ($_SESSION['role'] !== 'Admin') {
    header("Location: views/catalog.php?error=insufficient_clearance_level");
    exit();
}

// 2. Connect to the local persistence engine instance
require_once __DIR__ . '/config/connection.php';

$systemSuccessFeedback = "";

// 3. COMPLETE CRUD EXECUTION LAYER: CREATE OPERATION
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['executeCreateBook'])) {
    $bookTitle = htmlspecialchars(trim($_POST['bookTitle']));
    $bookAuthor= htmlspecialchars(trim($_POST['bookAuthor']));
    $bookIsbn  = htmlspecialchars(trim($_POST['bookIsbn']));
    $shelfCode = htmlspecialchars(trim($_POST['shelfCode']));

    if (!empty($bookTitle) && !empty($bookAuthor) && !empty($bookIsbn)) {
        try {
            $insertSql = "INSERT INTO books (title, author, isbn, shelf_code, status) VALUES (:title, :author, :isbn, :shelf, 'Available')";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                ':title'  => $bookTitle,
                ':author' => $bookAuthor,
                ':isbn'   => $bookIsbn,
                ':shelf'  => $shelfCode
            ]);
            $systemSuccessFeedback = "Book logged securely into inventory.";
        } catch (\PDOException $e) {
            $systemSuccessFeedback = "Database Contradiction Alert: Transaction canceled. Exception: " . $e->getMessage();
        }
    }
}

// 4. COMPLETE CRUD EXECUTION LAYER: DELETE OPERATION
if (isset($_GET['action']) && $_GET['action'] === 'deleteRecord' && isset($_GET['id'])) {
    $targetId = (int)$_GET['id'];
    try {
        $deleteSql = "DELETE FROM books WHERE book_id = :id";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute([':id' => $targetId]);
        $systemSuccessFeedback = "Book removed from inventory.";
    } catch (\PDOException $e) {
        $systemSuccessFeedback = "Data Constraint Exception: Book in active circulation tracking. Operation blocked.";
    }
}

// 5. COMPLETE CRUD EXECUTION LAYER: READ OPERATION
// Extract real-time metrics dynamically from your relational database engine
$totalBooksCount = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$activeLoansCount = $pdo->query("SELECT COUNT(*) FROM circulation WHERE status = 'Active'")->fetchColumn();
$overdueAlertsCount = $pdo->query("SELECT COUNT(*) FROM circulation WHERE status = 'Overdue'")->fetchColumn();

// Fetch live books tracking loops out of the MySQL cluster records engine
$booksCatalogResultSet = $pdo->query("SELECT * FROM books ORDER BY book_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex LMS Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/system-vars.css">
    <style>
        body { font-family: Arial, sans-serif; margin:0; display:flex; background:#f4f6f9; }
        .sidebar { width:260px; background:#1A2B4C; color:white; min-height:100vh; padding:20px; box-sizing:border-box; }
        .sidebar h2 { margin-top:0; font-size:20px; border-bottom:1px solid #334466; padding-bottom:10px; }
        .sidebar a { color:#ccc; display:block; padding:12px; text-decoration:none; font-weight:bold; border-radius:4px; }
        .sidebar a.active { background:#008080; color:white; }
        .main-workspace { flex:1; padding:30px; box-sizing:border-box; }
        .metrics-container { display:flex; gap:20px; margin-bottom:30px; }
        .metric-card { flex:1; background:white; padding:20px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.05); border-left:5px solid #1A2B4C; }
        .metric-card.alert { border-left-color:#dc3545; }
        .data-table-container { background:white; padding:20px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th, td { padding:12px; text-align:left; border-bottom:1px solid #eee; }
        th { background:#f8f9fa; color:#1A2B4C; }
        .btn-danger { background:#dc3545; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:12px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Library</h2>
        <small style="color:#008080; display:block; margin-bottom:20px;"></small>
        <nav>
    <a href="index.php" class="active">Book Inventory</a>
    <a href="students_dashboard.php">Student Records</a>
    <a href="employees_dashboard.php">Employee System</a>
    <a href="login.php" style="color:#ff6b6b; margin-top:40px;">Logout</a>
</nav>
    </div>

    <div class="main-workspace">
        <header style="display:flex; justify-content:between; align-items:center; margin-bottom:25px;">
            <div>
                <h1 style="color:#1A2B4C; margin:0;">Overview  </h1>
                <br>
                <br>
                <small style="color:#666;">workspace tracker</small>
            </div>
            <div style="background:#1A2B4C; color:white; padding:10px 20px; border-radius:4px; font-weight:bold;">
                    Librarian: <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            </div>
        </header>

        <?php if (!empty($systemSuccessFeedback)): ?>
            <div style="background:#D4EDDA; color:#155724; padding:15px; border-radius:4px; margin-bottom:20px; border:1px solid #C3E6CB;">
                <strong>Current Status:</strong> <?php echo $systemSuccessFeedback; ?>
            </div>
        <?php endif; ?>

        <section class="metrics-container">
            <div class="metric-card">
                <span style="color:#666; text-transform:uppercase; font-size:12px; font-weight:bold;">Total Registered Books</span>
                <h2 style="font-size:28px; color:#1A2B4C; margin:10px 0 0 0;"><?php echo $totalBooksCount; ?></h2>
                <small style="color:#008080;">Inventory count</small>
            </div>
            <div class="metric-card">
                <span style="color:#666; text-transform:uppercase; font-size:12px; font-weight:bold;">Loans Tracker</span>
                <h2 style="font-size:28px; color:#1A2B4C; margin:10px 0 0 0;"><?php echo $activeLoansCount; ?></h2>
                <small style="color:#666;">Current outbound items</small>
            </div>
            <div class="metric-card alert">
                <span style="color:#666; text-transform:uppercase; font-size:12px; font-weight:bold;">Overdue Violation Alerts</span>
                <h2 style="font-size:28px; color:#dc3545; margin:10px 0 0 0;"><?php echo $overdueAlertsCount; ?></h2>
                <small style="color:#dc3545;">Action required.</small>
            </div>
        </section>

        <section class="data-table-container" style="margin-bottom: 30px;">
            <h3 style="color:#1A2B4C; margin-top:0;">Log New Book</h3>
            <form action="index.php" method="POST" style="display:grid; grid-template-columns: repeat(4, 1fr) auto; gap:10px; margin-top:15px;">
                <input type="text" name="bookTitle" placeholder="Resource Book Title" required style="padding:8px; border:1px solid #ccc; border-radius:4px;">
                <input type="text" name="bookAuthor" placeholder="Primary Author Name" required style="padding:8px; border:1px solid #ccc; border-radius:4px;">
                <input type="text" name="bookIsbn" placeholder="ISBN Barcode ID" required style="padding:8px; border:1px solid #ccc; border-radius:4px;">
                <input type="text" name="shelfCode" placeholder="Shelf Location Grid" required style="padding:8px; border:1px solid #ccc; border-radius:4px;">
                <button type="submit" name="executeCreateBook" style="background:#008080; color:white; border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer;">Commit Asset</button>
            </form>
        </section>

        <section class="data-table-container">
            <h3 style="color:#1A2B4C; margin-top:0;">Library Registry Data Records</h3>
            <table>
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Shelf</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($booksCatalogResultSet) === 0): ?>
                        <tr><td colspan="7" style="text-align:center; color:#999;">No real-time inventory records parsed. System container vacant.</td></tr>
                    <?php else: ?>
                        <?php foreach ($booksCatalogResultSet as $book): ?>
                            <tr>
                                <td><code>LMS-<?php echo $book['book_id']; ?></code></td>
                                <td style="font-weight:bold; color:#1A2B4C;"><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><code><?php echo htmlspecialchars($book['isbn']); ?></code></td>
                                <td><span style="background:#eee; padding:3px 6px; border-radius:3px; font-size:12px; font-weight:bold;"><?php echo htmlspecialchars($book['shelf_code']); ?></span></td>
                                <td>
                                    <span style="color: <?php echo ($book['status'] === 'Available') ? '#008080' : '#dc3545'; ?>; font-weight:bold;">
                                        ● <?php echo $book['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?action=deleteRecord&id=<?php echo $book['book_id']; ?>" class="btn-danger" onclick="return confirm('Critical Security Callout: Purge this catalog item permanently from database storage?');">Purge</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>

</body>
</html>