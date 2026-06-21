<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apex LMS - Personal Profile Workspace</title>
    <style>
        :root {
            --primary-blue: #1A2B4C;
            --teal-accent: #008080;
            --bg-light: #f4f6f9;
            --text-dark: #333333;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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

        .profile-container {
            display: flex;
            flex-direction: column; /* Mobile-First: stack vertically */
            max-width: 900px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            width: 90%;
        }

        /* Profile Sidebar Element Panel */
        .profile-sidebar {
            background-color: var(--primary-blue);
            color: #ffffff;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
        }

        /* Responsive Image Frame */
        .image-frame {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 4px solid var(--teal-accent);
            overflow: hidden;
            background: #eef1f6;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-sidebar h2 {
            margin: 10px 0 5px 0;
            font-size: 24px;
        }

        .profile-sidebar .tag {
            background: var(--teal-accent);
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 12px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Profile Main Description*/
        .profile-content {
            padding: 40px;
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .profile-content h3 {
            color: var(--primary-blue);
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-top: 0;
        }

        .about-text {
            line-height: 1.6;
            color: #555555;
            margin-bottom: 30px;
        }

        /* Contact Details Flex Mapping Matrix */
        .contact-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .contact-icon {
            background: #e6f2f2;
            color: var(--teal-accent);
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
        }

        .contact-details span {
            display: block;
            font-size: 12px;
            color: #888888;
        }

        .contact-details strong {
            font-size: 14px;
            color: var(--primary-blue);
        }

        /* DESKTOP VIEW VIEWPORT */
        @media (min-width: 768px) {
            .profile-container {
                flex-direction: row; /* Switch layout axis seamlessly on wide viewports */
            }
            .profile-sidebar {
                border-right: 1px solid rgba(255,255,255,0.1);
                padding: 60px 40px;
            }
            .profile-content {
                padding: 50px;
            }
        }
    </style>
</head>
<body>

    <header class="nav-header">
        <span style="color:#fff; font-weight:bold; font-size:16px;">My profile</span>
        <a href="showcase.php">Go To Product Showcase →</a>
    </header>

    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="image-frame">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQDcYdClaMtHOQ_vwAeHKYuvX__VAyzqwR2XDIoGAV9sQ&s=10" alt="Profile Picture">
            </div>
            <h2>Collins Matagi</h2>
            <span class="tag">AI Intelligence Analyst</span>
        </div>

        <div class="profile-content">
            <h3>About</h3>
            <p class="about-text">
                A Student specializing in AI web architectures,
                 secure system programming interfaces, and database synchronization logic loops.
            </p>

            <h3>Contact</h3>
            <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-icon">✉</div>
                    <div class="contact-details">
                        <span>Email</span>
                        <strong>collins.matagi@GMAIL.com</strong>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">🖧</div>
                    <div class="contact-details">
                        <span>Repositories</span>
                        <strong>github.com/t4g1z</strong>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">🗺</div>
                    <div class="contact-details">
                        <span>Location</span>
                        <strong>Thika</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>