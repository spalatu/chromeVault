<?php
$error   = isset($_GET['error'])   ? htmlspecialchars($_GET['error'])   : '';
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>
<!DOCTYPE html>
<html lang="ro" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare — chromeVault</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: radial-gradient(circle at center, var(--bg-surface) 0%, var(--bg-base) 100%);
            padding: 20px;
        }
        .auth-container {
            background-color: var(--bg-elevated);
            padding: 48px;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            width: 100%;
            max-width: 460px;
            border: 1px solid var(--border-light);
        }
        .auth-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            margin-bottom: 28px;
        }
        .auth-logo span {
            font-weight: 700;
            font-size: 18px;
            letter-spacing: -0.04em;
        }
        h2 {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
            text-align: center;
        }
        .auth-subtitle {
            text-align: center;
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 32px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-secondary);
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--border-light);
            background-color: var(--bg-surface);
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
            border-radius: 8px;
            transition: border-color 0.2s, background-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--border-strong);
            background-color: var(--bg-base);
        }
        .btn-full {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border-radius: 8px;
            margin-top: 8px;
            font-family: inherit;
        }
        .auth-switch {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: var(--text-secondary);
        }
        .auth-switch a {
            font-weight: 600;
            color: var(--text-primary);
        }
        .msg {
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
        }
        .msg-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }
        .msg-error {
            background-color: rgba(239, 68, 68, 0.08);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .note-box {
            margin-top: 20px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 12px;
            color: var(--text-tertiary);
            border: 1px dashed var(--border-light);
            text-align: center;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: var(--text-tertiary);
        }
    </style>
</head>
<body>

<div class="auth-page">
    <div class="auth-container">

        <div class="auth-logo">
            <svg viewBox="0 0 24 24" width="22" height="22" style="fill: var(--text-primary);">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <span>chromeVault</span>
        </div>

        <h2>Autentificare</h2>
        <p class="auth-subtitle">Conectează-te la contul tău chromeVault.</p>

        <?php if ($success): ?>
            <div class="msg msg-success">✓ <?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="msg msg-error">✕ <?= $error ?></div>
        <?php endif; ?>

        <form action="php/auth.php" method="POST" novalidate>
            <input type="hidden" name="action" value="login">

            <div class="form-group">
                <label for="email">Adresă de email</label>
                <input type="email" id="email" name="email"
                       placeholder="exemplu@email.com" required>
            </div>

            <div class="form-group">
                <label for="password">Parolă</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Conectează-te</button>
        </form>

        <div class="note-box">
            🔒 Autentificarea completă va fi implementată în etapa următoare (Ziua 8).
        </div>

        <div class="auth-switch">
            Nu ai un cont? <a href="register.php">Înregistrează-te</a>
        </div>
        <a href="index.php" class="back-link">← Înapoi la pagina principală</a>
    </div>
</div>

</body>
</html>
