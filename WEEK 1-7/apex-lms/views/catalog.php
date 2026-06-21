<?php
/**
 * Apex LMS - Student Catalog Explorer View Portal
 * Ref Layout Context: Figure 2.4 Responsive Mobile Blueprint
 */
// 1. Maintain active gateway perimeter security
require_once __DIR__ . '/../includes/auth_check.php';

// Enforce specific security policy access mapping layers (Week 7 Requirement)
restrictAccessTo(['Student', 'Admin']);

// 2. Attach system connectivity frameworks
require_once __DIR__ . '/../config/connection.php';

// 3. Process dynamic matching search parameters via parameterized SQL
$searchQueryText = "SELECT * FROM books WHERE 1=1";
$executionParametersArray = [];

if (isset($_GET['catalogSearchKey']) && !empty(trim($_GET['catalogSearchKey']))) {
    $searchToken = "%" . trim($_GET['catalogSearchKey']) . "%";
    $searchQueryText .= " AND (title LIKE :term1 OR author LIKE :term2 OR isbn LIKE :term3)";
    $executionParametersArray = [
        ':term1' => $searchToken,
        ':term2' => $searchToken,
        ':term3' => $searchToken
    ];
}

$searchQueryText .= " ORDER BY title ASC";
$stmt = $pdo->prepare($searchQueryText);
$stmt->execute($executionParametersArray);
$displayedBooksCollection = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apex LMS - Student Catalog</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f6f9; }
        .wrapper { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: #1A2B4C; color: white; padding: 20px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .search-bar { display: flex; gap: 10px; width: 100%; max-width: 400px; }
        .search-bar input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .search-bar button { background: #008080; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        main { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 30px; }
        .book-summary-card { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; gap: 15px; align-items: center; }
        .book-icon-box { background: #eef1f6; width: 50px; height: 50px; display: flex; justify-content: center; align-items: center; border-radius: 4px; font-size: 24px; color: #1A2B4C; }
        .book-details { display: flex; flex-direction: column; flex: 1; }
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; margin-top: 8px; width: max-content; }
        .status-available { background: #D4EDDA; color: #155724; }
        .status-loaned { background: #F8D7DA; color: #721C24; }
    </style>
</head>
<body>

    <div class="wrapper">
        <header>
            <div>
                <h1 style="margin: 0; font-size: 24px;">Apex Library Catalog</h1>
                <small>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong> (Tier: <?php echo htmlspecialchars($_SESSION['role']); ?>)</small>
            </div>
            
            <form method="GET" action="catalog.php" class="search-bar">
                <input type="text" name="catalogSearchKey" placeholder="Search title, author, or ISBN..." value="<?php echo htmlspecialchars($_GET['catalogSearchKey'] ?? ''); ?>">
                <button type="submit">Search</button>
            </form>
        </header>

        <main>
            <?php if (count($displayedBooksCollection) === 0): ?>
                <div style="grid-column: 1 / -1; text-align: center; background: white; padding: 40px; border-radius: 6px; color: #777;">
                    No matching publications found inside the catalog registry.
                </div>
            <?php else: ?>
                <?php foreach ($displayedBooksCollection as $bookItem): ?>
                    <div class="book-summary-card">
                        <div class="book-icon-box">📖</div>
                        <div class="book-details">
                            <h4 style="margin:0 0 4px 0; color:#1A2B4C; font-size:15px;"><?php echo htmlspecialchars($bookItem['title']); ?></h4>
                            <span style="font-size:13px; color:#555; margin-bottom:2px;">by <?php echo htmlspecialchars($bookItem['author']); ?></span>
                            <small style="color:#888; font-family:monospace;">Shelf: <?php echo htmlspecialchars($bookItem['shelf_code']); ?></small>
                            
                            <?php if ($bookItem['status'] === 'Available'): ?>
                                <span class="status-badge status-available">Available</span>
                            <?php else: ?>
                                <span class="status-badge status-loaned">Loaned Out</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
        
        <footer style="text-align:center; margin-top: 40px; padding:20px; border-top:1px solid #eee; background:#fff; border-radius: 6px;">
            <a href="../login.php?action=terminateSession" style="color:#dc3545; font-size:14px; text-decoration:none; font-weight:bold;">Exit Session Profile</a>
        </footer>
    </div>

</body>
</html>