<?php
function validate(array $data) : array
{
    $errors = [];
    if (!isset($data['email'])) {
        $errors['email'] = 'Email должен быть заполнен';
    }

    if (!isset($data['psw'])) {
        $errors['psw'] = 'Пароль должен быть заполнен';
    }

    return $errors;
}
$errors = validate($_POST);

if (empty($errors)) {
    $email = $_POST['email'];
    $password = $_POST['psw'];

    $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    if ($user === false) {
        $errors['username'] = 'Email или пароль указаны неверно';
    } else {
        $passwordDb = $user['password'];
        if (password_verify($password, $passwordDb)) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header('Location: catalog.php');
        } else {
            $errors['username'] = 'Email или пароль указаны неверно';
        }
    }
}

require_once './login_form.php';