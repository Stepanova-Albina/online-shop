<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}
function validate(array $data) : array
{
    $errors = [];

    if (isset($data['name'])) {
        $name = $data['name'];
        if (strlen($name) <= 2) {
            $errors['name'] = 'Имя должно содержать 2 или более символов';
        }
    }

    if (isset($data['email'])) {
        $email = $data['email'];
        if (strlen($email) < 2) {
            $errors['email'] = 'Email должен содержать более двух символов';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email некорректный';
        } else {
            $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            if($user) {
                $userId = $_SESSION['user_id'];
                if ($userId !== $user['id']) {
                    $errors['email'] = 'Такой Email уже существует';
                }
            }
        }
    }

    if (!empty($data['psw'])) {
        $password = $data['psw'];
        if (strlen($password) < 2) {
            $errors['psw'] = 'Пароль должен содержать более двух символов';
        }
        if (isset($data['psw-repeat'])) {
            $passwordRep = $data['psw-repeat'];
            if ($password !== $passwordRep) {
                $errors['psw-repeat'] = 'Пароли не совпадают';
            }
        }
    }
    return $errors;
}

$errors = validate($_POST);

if (empty($errors)) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $userId = $_SESSION['user_id'];

    $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
    $stmt = $pdo->query("SELECT * FROM users WHERE id = $userId");
    $user = $stmt->fetch();

    if ($user['name'] !== $name) {
        $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = $userId");
        $stmt->execute(['name' => $name]);
    }

    if ($user['email'] !== $email) {
        $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = $userId");
        $stmt->execute(['email' => $email]);
    }

    if (!empty($_POST['psw'])) {
        $password = password_hash($_POST['psw'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = $userId");
        $stmt->execute(['password' => $password]);
    }


    header('Location: /profile');
    exit;
}




require_once './profileEdit/profile_edit.php';
