<?php
/**
 * auth.php — Ziua 7
 * Înregistrare utilizator cu validare și salvare în users.json
 */

$users_file = __DIR__ . '/../data/users.json';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // ─── REGISTER ────────────────────────────────────────────────────
    if ($action === 'register') {

        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
        $password = isset($_POST['password']) ? $_POST['password']       : '';
        $confirm  = isset($_POST['confirm'])  ? $_POST['confirm']        : '';

        // Validare câmpuri goale
        if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
            header("Location: ../register.php?error=Toate+câmpurile+sunt+obligatorii.");
            exit;
        }

        // Validare email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../register.php?error=Adresa+de+email+nu+este+validă.");
            exit;
        }

        // Validare lungime parolă
        if (strlen($password) < 6) {
            header("Location: ../register.php?error=Parola+trebuie+să+aibă+cel+puțin+6+caractere.");
            exit;
        }

        // Confirmare parolă
        if ($password !== $confirm) {
            header("Location: ../register.php?error=Parolele+nu+coincid.");
            exit;
        }

        // Citire utilizatori existenți
        $users = [];
        if (file_exists($users_file)) {
            $json = file_get_contents($users_file);
            $users = json_decode($json, true);
            if (!is_array($users)) $users = [];
        }

        // Verificare email duplicat
        foreach ($users as $u) {
            if (strtolower($u['email']) === strtolower($email)) {
                header("Location: ../register.php?error=Există+deja+un+cont+cu+acest+email.");
                exit;
            }
        }

        // Creare utilizator nou și salvare
        $new_user = [
            'id'         => count($users) + 1,
            'username'   => $username,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $users[] = $new_user;

        file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        header("Location: ../register.php?success=Contul+a+fost+creat+cu+succes!+Acum+te+poți+autentifica.");
        exit;
    }

    // ─── LOGIN (stub — va fi implementat în Ziua 8) ───────────────────
    if ($action === 'login') {
        $email    = isset($_POST['email'])    ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password']    : '';

        if (empty($email) || empty($password)) {
            header("Location: ../login.php?error=Completați+toate+câmpurile.");
            exit;
        }

        // Placeholder — autentificarea completă urmează în Ziua 8
        header("Location: ../login.php?error=Autentificarea+va+fi+implementată+în+etapa+următoare.");
        exit;
    }
}

header("Location: ../index.php");
exit;
?>