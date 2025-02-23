<?php

require_once '../Model/User.php';
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
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['psw'];
            $passwordRep = $_POST['psw-repeat'];
            $password = password_hash($password, PASSWORD_DEFAULT);


            $userModel = new User();

            $userModel->insertAll($name, $email, $password);

            $result = $userModel->getByEmail($email);

            print_r($result);
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
                $userModel = new User();
                $user = $userModel->getByEmail($email);
                if ($user !== false) {
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

            $userModel = new User();
            $user = $userModel->getByEmail($email);
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

        $userModel = new User();
        $user = $userModel->getById($userId);

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

            $userModel = new User();
            $user = $userModel->getById($userId);

            if ($user['name'] !== $name) {
                $userModel->updateNameById($name, $userId);
            }

            if ($user['email'] !== $email) {
                $userModel->updateEmailById($email, $userId);
            }

            if (!empty($_POST['psw'])) {
                $password = password_hash($_POST['psw'], PASSWORD_DEFAULT);
                $userModel->updatePasswordById($password, $userId);
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
                $userModel = new User();
                $user = $userModel->getByEmail($email);
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

    public function logout()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_destroy();
        header('Location: /login');
    }
}