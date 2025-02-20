<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

function validate(array $data): array
{
    $errors = [];

    if (!empty($data['name'])) {
        $name = $data['name'];
        if (strlen($name) <= 2) {
            $errors['name'] = 'Имя должно содержать 2 или более символов';
        }
    }

    if (!empty($data['email'])) {
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

            $userId = $_SESSION['user_id'];
            if ($user && $user['id'] !== $userId) {
                $errors['email'] = 'Такой Email уже существует';
            }
        }
    }

    if (!empty($data['psw'])) {
        $password = $data['psw'];
        if (strlen($password) < 2) {
            $errors['psw'] = 'Пароль должен содержать более двух символов';
        }
        if (!empty($data['psw-repeat'])) {
            $passwordRep = $data['psw-repeat'];
            if ($password !== $passwordRep) {
                $errors['psw-rep'] = 'Пароли не совпадают';
            }
        } else {
            $errors['psw-rep'] = 'Подтверждение пароля должно быть заполнено';
        }
    }

    return $errors;
}

$errors = validate($_POST);

// Проверяем, существуют ли данные перед их использованием
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['psw'] ?? null;
$userId = $_SESSION['user_id'];

if (empty($errors)) {
    $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    if (!empty($name) && $user['name'] !== $name) {
        $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
        $stmt->execute([':name' => $name, ':id' => $userId]);
    }

    if (!empty($email) && $user['email'] !== $email) {
        $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
        $stmt->execute([':email' => $email, ':id' => $userId]);
    }

    if (!empty($password) && !password_verify($password, $user['psw'])) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET psw = :psw WHERE id = :id");
        $stmt->execute([':psw' => $hashedPassword, ':id' => $userId]);
    }

    header('Location: /profile');
    exit;
}

require_once './profileEdit/profile_edit.php';



//мой старый код
/*
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

function getPDO() {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
    return $pdo;
}

function validate(array $data) : array
{
    $errors = [];

    if (isset($data['name']) && strlen($data['name']) <= 2) {
        $errors['name'] = 'Имя должно содержать 2 или более символов';
    }

    if (isset($data['email'])) {
        $email = $data['email'];
        if (strlen($email) < 2) {
            $errors['email'] = 'Email должен содержать более двух символов';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email некорректный';
        } else {
            $pdo = getPDO();
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $stmt->execute([':email' => $email, ':id' => $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $errors['email'] = 'Такой Email уже существует';
            }
        }
    }

    if (isset($data['psw'])) {
        if (strlen($data['psw']) < 2) {
            $errors['psw'] = 'Пароль должен содержать более двух символов';
        }
        if (!isset($data['psw-repeat']) || $data['psw'] !== $data['psw-repeat']) {
            $errors['psw-rep'] = 'Пароли не совпадают';
        }
    }

    return $errors;
}

$errors = validate($_POST);

if (empty($errors)) {
    $pdo = getPDO();
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    $updates = [];
    $params = [':id' => $userId];

    if ($user['name'] !== $_POST['name']) {
        $updates[] = "name = :name";
        $params[':name'] = $_POST['name'];
    }
    if ($user['email'] !== $_POST['email']) {
        $updates[] = "email = :email";
        $params[':email'] = $_POST['email'];
    }
    if (!password_verify($_POST['psw'], $user['psw'])) {
        $updates[] = "psw = :psw";
        $params[':psw'] = password_hash($_POST['psw'], PASSWORD_DEFAULT);
    }

    if ($updates) {
        $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id");
        $stmt->execute($params);
    }

    header('Location: /profile');
    exit;
}

require_once './profileEdit/profile_edit.php';

 */
