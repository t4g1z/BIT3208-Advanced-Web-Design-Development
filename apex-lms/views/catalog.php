<?php
/**
 * Apex LMS - Student Catalog Explorer View Portal
 * Ref Layout Context: Figure 2.4 Responsive Mobile Blueprint
 */
// 1. Maintain active gateway perimeter security
require_once __DIR__ . '/../includes/auth_check.php';

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
    <title>Apex LMS - Book Exploration Catalog</title>
    <link rel="stylesheet" href="../assets/css/system-vars.css">
    <style>
        body { background:#fafafa; font-family:Arial, sans-serif; margin:0; padding:0; display:flex; justify-content:center; }
        .mobile-viewport-container { width:100%; max-width:480px; background:white; min-height:100vh; box-shadow:0 0 20px rgba(0,0,0,0.05); display:flex; flex-direction:column; }
        .sticky-header { background:#1A2B4C; color:white; padding:15px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .search-box-wrapper { padding:15px; background:#f4f6f9; border-bottom:1px solid #eef0f5; }
        .search-box-wrapper input { width:100%; height:42px; border:1px solid #ccc; border-radius:6px; padding-left:35px; box-sizing:border-box; font-size:14px; }
        .catalog-scrollable-stream { padding:15px; flex:1; }
        .book-summary-card { display:flex; gap:15px; background:white; border:1px solid #eef0f5; padding:15px; border-radius:8px; margin-bottom:15px; box-shadow:0 2px 4px rgba(0,0,0,0.02); }
        .book-icon-box { width:50px; height:65px; background:#E0F2F1; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:24px; color:#008080; border:1px solid #B2DFDB; }
        .book-details { flex:1; display:flex; flex-direction:column; justify-content:center; }
        .status-badge { display:inline-block; align-self:flex-start; font-size:11px; font-weight:bold; padding:3px 8px; border-radius:12px; margin-top:5px; }
        .status-available { background:#E0F2F1; color:#008080; }
        .status-loaned { background:#FFEBEE; color:#D32F2F; }
    </style>
</head>
<body>

    <div class="mobile-viewport-container">
        <header class="sticky-header">
            <span style="font-size:20px; cursor:pointer;">☰</span>
            <h2 style="margin:0; font-size:18px; letter-spacing:0.5px;">Library Catalog</h2>
            <span style="font-size:14px; background:#008080; padding:4px 8px; border-radius:3px;">Student</span>
        </header>

        <div class="search-box-wrapper" style="position:relative;">
            <span style="position:absolute; left:25px; top:27px; color:#999;">🔍</span>
            <form method="GET" action="catalog.php">
                <input type="text" name="catalogSearchKey" value="<?php echo htmlspecialchars($_GET['catalogSearchKey'] ?? ''); ?>" placeholder="Search by Title, Author, or ISBN..." onchange="this.form.submit()">
            </form>
        </div>

        <main class="catalog-scrollable-stream">
            <small style="color:#666; font-weight:bold; display:block; margin-bottom:10px;">
                Parsing Matches: <?php echo count($displayedBooksCollection); ?> Systems Resources Available
            </small>

            <?php if (count($displayedBooksCollection) === 0): ?>
                <div style="text-align:center; padding:40px; color:#999;">
                    <p style="font-size:40px; margin:0;">📭</p>
                    <p>No matching assets located in database tracking indexes.</p>
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
        
        <footer style="text-align:center; padding:15px; border-top:1px solid #eee; background:#fff;">
            <a href="../login.php" style="color:#dc3545; font-size:13px; text-decoration:none; font-weight:bold;">Exit Session Profile</a>
        </footer>
    </div>

</body>
</html>