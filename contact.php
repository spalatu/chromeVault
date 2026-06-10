<?php
require_once 'php/functions.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('contact'); ?> - chromeVault</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="contact-page">
    <nav class="navbar">
        <div class="logo">
            <svg viewBox="0 0 24 24" width="24" height="24" style="fill: var(--text-primary); vertical-align: middle; margin-right: 8px;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            chromeVault
        </div>
        <ul class="nav-links">
            <li><a href="index.php"><?php echo t('home'); ?></a></li>
            <li><a href="contact.php"><?php echo t('contact'); ?></a></li>
            <?php if(is_logged_in()): ?>
                <li><a href="dashboard.php" class="btn btn-primary" style="margin-left:20px;"><?php echo t('dashboard'); ?></a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn btn-outline" style="margin-left:20px;"><?php echo t('login'); ?></a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="auth-container" style="margin-top: 100px;">
        <h2><?php echo t('contact'); ?></h2>
        <div id="contact-message" class="message"></div>
        <form id="contact-form">
            <div class="form-group">
                <label for="name"><?php echo t('name'); ?></label>
                <input type="text" id="name" name="name" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label for="email"><?php echo t('email'); ?></label>
                <input type="email" id="email" name="email" placeholder="hello@example.com" required>
            </div>
            <div class="form-group">
                <label for="msg"><?php echo t('message'); ?></label>
                <textarea id="msg" name="msg" required rows="4" placeholder="How can we help you?"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-full"><?php echo t('send'); ?></button>
        </form>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
