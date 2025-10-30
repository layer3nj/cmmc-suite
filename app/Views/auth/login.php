<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= $e($app_name ?? 'Layer3 | Trident Cyber OneComply') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, <?= $e($primary_color ?? '#667eea') ?> 0%, <?= $e($secondary_color ?? '#764ba2') ?> 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, <?= $e($primary_color ?? '#667eea') ?> 0%, <?= $e($secondary_color ?? '#764ba2') ?> 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .login-logo {
            max-width: 120px;
            max-height: 80px;
            margin-bottom: 16px;
        }

        .login-body {
            padding: 40px 30px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fff5f5;
            color: #742a2a;
            border-left: 4px solid #f56565;
        }

        .alert-success {
            background: #f0fff4;
            color: #22543d;
            border-left: 4px solid #48bb78;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: <?= $e($primary_color ?? '#667eea') ?>;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, <?= $e($primary_color ?? '#667eea') ?> 0%, <?= $e($secondary_color ?? '#764ba2') ?> 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(<?= hexdec(substr($primary_color ?? '#667eea', 1, 2)) ?>, <?= hexdec(substr($primary_color ?? '#667eea', 3, 2)) ?>, <?= hexdec(substr($primary_color ?? '#667eea', 5, 2)) ?>, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #4a5568;
            border: 2px solid #e2e8f0;
            margin-top: 12px;
        }

        .btn-secondary:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }

        .divider {
            text-align: center;
            margin: 24px 0;
            color: #718096;
            font-size: 14px;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #e2e8f0;
        }

        .divider::before { left: 0; }
        .divider::after { right: 0; }

        .footer {
            text-align: center;
            padding: 20px;
            background: #f7fafc;
            color: #718096;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <?php if (!empty($app_logo)): ?>
                <img src="<?= $url($app_logo) ?>" alt="<?= $e($app_name ?? 'Layer3 | Trident Cyber OneComply') ?>" class="login-logo">
            <?php endif; ?>
            <h1>Welcome Back</h1>
            <p><?= $e($app_name ?? 'Layer3 | Trident Cyber OneComply') ?></p>
        </div>

        <div class="login-body">
            <?php if ($errorMessage = $error()): ?>
                <div class="alert alert-error"><?= $e($errorMessage) ?></div>
            <?php endif; ?>

            <?php if ($success = $success()): ?>
                <div class="alert alert-success"><?= $e($success) ?></div>
            <?php endif; ?>

            <?php if ($saml_enabled): ?>
                <form action="<?= $url('saml/login') ?>" method="GET">
                    <button type="submit" class="btn btn-primary">
                        <svg style="display: inline-block; width: 18px; height: 18px; margin-right: 8px; vertical-align: middle;" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                        </svg>
                        Sign in with Microsoft
                    </button>
                </form>

                <div class="divider">OR</div>
            <?php endif; ?>

            <form action="<?= $url('login') ?>" method="POST">
                <?= $csrf() ?>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= $e($old('email')) ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>
        </div>

        <div class="footer">
            &copy; <?= date('Y') ?> Layer3 &amp; Trident Cyber. All rights reserved.
        </div>
    </div>
</body>
</html>
