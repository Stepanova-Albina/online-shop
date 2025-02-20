<?php

class User
{
    public function registrate ()
    {
        $errors = $this->validate($_POST);

        if (empty($errors)) {
            $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['psw'];
            $passwordRep = $_POST['psw-repeat'];
            $password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password]);

            $statement = $pdo->prepare( "SELECT * FROM users WHERE email = :email");
            $statement->execute(['email' => $email]);
            $data = $statement->fetch();
            print_r($data);
        }

        require_once './registration/registration_form.php';
    }

    private function validate(array $data) : array
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
}