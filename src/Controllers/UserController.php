<?php

class UserController
{
    public function getRegistrate()
    {
        require_once '../Views/registration.php';
    }

    public function registrate()
    {
        $errors = $this->validateRegistrate($_POST);

        if (empty($errors)) {
            $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['psw'];
            $passwordRep = $_POST['psw-repeat'];
            $password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password]);

            $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $statement->execute(['email' => $email]);
            $data = $statement->fetch();
            print_r($data);
        }

        require_once '../Views/registration.php';
    }

    private function validateRegistrate(array $data): array
    {
        $errors = [];

        if (isset($data['name'])) {
            $name = $data['name'];
            if (strlen($name) <= 2) {
                $errors['name'] = 'Имя должно содержать 2 или более символов';
            }
        } else {
            $errors['name'] = 'Имя должно быть заполнено';
        }

        if (isset($data['email'])) {
            $email = $data['email'];
            if (strlen($email) < 2) {
                $errors['email'] = 'Email должен содержать более двух символов';
            } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $errors['email'] = 'Email некорректный';
            } else {
                $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $quantityEmail = $stmt->fetch();
                if ($quantityEmail > 0) {
                    $errors['email'] = 'Такой Email уже существует';
                }
            }
        } else {
            $errors['email'] = 'Email должен быть заполнен';
        }

        if (isset($data['psw'])) {
            $password = $data['psw'];
            if (strlen($password) < 2) {
                $errors['psw'] = 'Пароль должен содержать более двух символов';
            }
            if (isset($data['psw-repeat'])) {
                $passwordRep = $data['psw-repeat'];
                if ($password !== $passwordRep) {
                    $errors['psw-rep'] = 'Пароли не совпадают';
                }
            } else {
                $errors['psw-rep'] = 'Подтверждение пароля должно быть заполнено';
            }
        } else {
            $errors['psw'] = 'Пароль должен быть заполнен';
        }
        return $errors;
    }


    public function getLogin()
    {
        require_once '../Views/login.php';
    }

    public function login()
    {
        $errors = $this->validateLogin($_POST);

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
                    header('Location:/catalog');
                } else {
                    $errors['username'] = 'Email или пароль указаны неверно';
                }
            }
        }
        require_once '../Views/login.php';
    }

    private function validateLogin(array $data): array
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

    public function getProfile()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $userId = $_SESSION['user_id'];

        $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
        $stmt = $pdo->query("SELECT * FROM users WHERE id = $userId");
        $user = $stmt->fetch();

        require_once '../Views/profile.php';
    }

    public function getProfileEdit()
    {
        require_once '../Views/profile_edit.php';
    }

    public function profileEdit()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $errors = $this->validateProfileEdit($_POST);

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
        require_once '../Views/profile_edit.php';
    }

    private function validateProfileEdit(array $data): array
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
                if ($user) {
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
}