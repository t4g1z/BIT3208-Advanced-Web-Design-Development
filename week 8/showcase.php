<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Showcase</title>
    <style>
        :root {
            --primary-blue: #1A2B4C;
            --teal-accent: #008080;
            --bg-light: #f4f6f9;
            --card-white: #ffffff;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-light);
            color: #333333;
        }

        .nav-header {
            background-color: var(--primary-blue);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-header a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            background: rgba(255,255,255,0.1);
            padding: 8px 16px;
            border-radius: 4px;
        }

        .showcase-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .showcase-title {
            text-align: center;
            color: var(--primary-blue);
            margin-bottom: 10px;
        }

        .showcase-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 14px;
        }

        /* MOBILE-FIRST GRID ARCHITECTURE */
        .product-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }

        /* Product Catalog Display */
        .product-card {
            background: var(--card-white);
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.04);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid #eef1f6;
            transition: transform 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
        }

        /* Responsive Product Image Container */
        .product-image-container {
            width: 100%;
            height: 200px;
            background-color: #eef1f6;
            overflow: hidden;
            position: relative;
        }

        .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-info h4 {
            margin: 0 0 8px 0;
            color: var(--primary-blue);
            font-size: 18px;
        }

        .product-desc {
            font-size: 13px;
            color: #666666;
            line-height: 1.5;
            margin: 0 0 20px 0;
            flex-grow: 1;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f4f6f9;
            padding-top: 15px;
        }

        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: var(--teal-accent);
        }

        .btn-buy {
            background-color: var(--primary-blue);
            color: white;
            border: none;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }

        /* BREAKPOINT 1: TABLET VIEWPORTS */
        @media (min-width: 600px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr); /* 2 Products per row */
            }
        }

        /* BREAKPOINT 2: DESKTOP VIEWPORTS */
        @media (min-width: 992px) {
            .product-grid {
                grid-template-columns: repeat(4, 1fr); /* 4 Products per row */
            }
        }
    </style>
</head>
<body>

    <header class="nav-header">
        <span style="color:#fff; font-weight:bold; font-size:16px;">Books on sale</span>
        <a href="profile.php">← Back to View Profile</a>
    </header>

    <div class="showcase-wrapper">
        <h1 class="showcase-title">Product Catalogue</h1>
        <div class="product-grid">
            
            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ2YTSCOwH-f36hXOAkmq700b80RQ2kIOjC3JQw3zBYlduyJUzGo0Tvt2o&s=10" alt="Classics">
                </div>
                <div class="product-info">
                    <h4>Classic Books</h4>
                    <p class="product-desc">BOOKS BY JEREMY</p>
                    <div class="product-footer">
                        <span class="product-price">1500</span>
                        <button class="btn-buy">BUY</button>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTs421QU4s82q9ajaQwnpCMmfDT0FmOV5fSAehA5reZiw&s=10" alt="Dark Profile">
                </div>
                <div class="product-info">
                    <h4>Dark Humour</h4>
                    <p class="product-desc">An intense dark profile book by Jay</p>
                    <div class="product-footer">
                        <span class="product-price">2500</span>
                        <button class="btn-buy">BUY</button>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSvyAS6-ZebMF2bt_jvTj6fwl7puoKzg0gpH8hQv-clcg&s=10" alt="COMEDY">
                </div>
                <div class="product-info">
                    <h4>Comedy</h4>
                    <p class="product-desc">Books by jay.</p>
                    <div class="product-footer">
                        <span class="product-price">2400</span>
                        <button class="btn-buy">BUY</button>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSkz4kyion5H2Je0diwixk_Ytgyr1iUj3B6WCRxsLUogw&s=10" alt="Decaf Edition">
                </div>
                <div class="product-info">
                    <h4>Fantasy</h4>
                    <p class="product-desc">Immerse yourself in magical worlds and epic adventures.</p>
                    <div class="product-footer">
                        <span class="product-price">1750</span>
                        <button class="btn-buy">BUY</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>