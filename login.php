<?php
require_once 'php/functions.php';
if(is_logged_in()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('login'); ?> - chromeVault</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h2><?php echo t('login'); ?></h2>
        <div id="message" class="message"></div>
        <form id="login-form">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="email"><?php echo t('email'); ?></label>
                <input type="email" id="email" name="email" placeholder="hello@example.com" required>
            </div>
            <div class="form-group">
                <label for="password"><?php echo t('password'); ?></label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full"><?php echo t('login'); ?></button>
        </form>
        <p class="auth-switch">Nu ai cont? <a href="register.php"><?php echo t('register'); ?></a></p>
        <div class="lang-switch-auth">
            <a href="?lang=ro">RO</a> &middot; <a href="?lang=en">EN</a> &middot; <a href="?lang=ru">RU</a>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
