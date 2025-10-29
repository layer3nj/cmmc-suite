<?php
// Simple diagnostic page
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

?>
<!DOCTYPE html>
<html>
<head>
    <title>CSS Diagnostic</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        .diagnostic {
            font-family: monospace;
            padding: 20px;
            background: #f0f0f0;
            margin: 20px;
        }
    </style>
</head>
<body>
    <h1>CSS Diagnostic Page</h1>

    <div class="diagnostic">
        <h2>Path Information:</h2>
        <p><strong>CSS File Path:</strong> <?= BASE_PATH ?>/public/assets/css/app.css</p>
        <p><strong>CSS File Exists:</strong> <?= file_exists(BASE_PATH . '/public/assets/css/app.css') ? 'YES' : 'NO' ?></p>
        <p><strong>CSS File Size:</strong> <?= file_exists(BASE_PATH . '/public/assets/css/app.css') ? filesize(BASE_PATH . '/public/assets/css/app.css') . ' bytes' : 'N/A' ?></p>
        <p><strong>CSS URL:</strong> <a href="/assets/css/app.css" target="_blank">/assets/css/app.css</a></p>
    </div>

    <div class="card" style="margin: 20px;">
        <div class="card-header">
            <h3>Test Card (Should be styled if CSS loaded)</h3>
        </div>
        <div class="card-body">
            <p>If you see this card with shadows, borders, and proper styling, the CSS is loading.</p>
            <button class="btn btn-primary">Test Button</button>
        </div>
    </div>

    <div class="alert alert-success" style="margin: 20px;">
        This is a success alert - should have green background
    </div>

    <script>
        // Check if CSS loaded
        const testEl = document.querySelector('.card');
        const styles = window.getComputedStyle(testEl);
        const hasStyles = styles.backgroundColor !== 'rgba(0, 0, 0, 0)' && styles.backgroundColor !== 'transparent';

        if (!hasStyles) {
            alert('CSS IS NOT LOADING! Check the network tab in DevTools (F12).');
        } else {
            console.log('CSS loaded successfully!');
        }
    </script>
</body>
</html>
