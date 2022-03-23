<?php
    require_once('./config.php');

    /// API авторизации и регистрации

    function answerJson($answer) {
        echo json_encode($answer);
    }

    function equalPasswords($password, $passwordRepeat) {
        $isEqual = true;

        if ($password !== $passwordRepeat) {
            $isEqual = false;
            $answer = array(
                'body' => 'error',
                'message' => 'Пароли не совпадают'
            );
            answerJson($answer);
        }

        return $isEqual;
    }

    function detectUniqueMail($mail) {
        $isUnique = true;
        try {
            $db = new mysqli();
            $db->connect(HOSTNAME, USERNAME, PASSWORD, DATABASE, PORT);
            $result = $db->query("SELECT `mail` FROM `Users` WHERE `mail` LIKE '$mail'");
            $db->close();
        } catch (Exception $ex) {
            $answer = array(
                'body' => 'error',
                'message' => 'Произошла внутренняя ошибка при регистрации'
            );
            answerJson($answer);
        }


        if ($result->num_rows > 0) {
            $isUnique = false;
        }

        if (!$isUnique) {
            $answer = array(
                'body' => 'error',
                'message' => 'Такой Email уже используется в системе'
            );
            answerJson($answer);
        }

        return $isUnique;
    }

    function formIsEmpty() {
        $answer = array(
            'body' => 'error',
            'message' => 'Вы заполнили не все поля'
        );
        answerJson($answer);
        return false;
    }

    function registerNewUser($login, $hashPassword, $mail) {
        try {
            $db = new mysqli();
            $db->connect(HOSTNAME, USERNAME, PASSWORD, DATABASE, PORT);
            $result = $db->query("INSERT INTO `Users` (`username`, `password`, `mail`) VALUES ('$login', '$hashPassword', '$mail')");
            $db->close();
            $answer = array(
                'body' => 'success',
                'message' => 'Успешная регистрация'
            );
        } catch (Exception $ex) {
            $answer = array(
                'body' => 'error',
                'message' => 'Произошла внутренняя ошибка при регистрации'
            );
        } finally {
            answerJson($answer);
        }
    }
    function findUserInSystem($login, $hashPassword) {
        try {
            $db = new mysqli();
            $db->connect(HOSTNAME, USERNAME, PASSWORD, DATABASE, PORT);
            $result = $db->query("SELECT * FROM `Users` WHERE `username` LIKE '$login' AND `password` LIKE '$hashPassword'");
            $db->close();
            if ($result->num_rows == 1) {
                $answer = array(
                    'body' => 'success',
                    'message' => 'Успешная авторизация'
                );
            } else {
                $answer = array(
                    'body' => 'error',
                    'message' => 'Такого пользователя нет'
                );
            }

        } catch (Exception $ex) {
            $answer = array(
                'body' => 'error',
                'message' => 'Произошла внутренняя ошибка при регистрации'
            );
        }
        answerJson($answer);
    }

    function registration() {
        $login = $_POST['username'];
        $mail = $_POST['user-mail'];
        $password = $_POST['user-password'];
        $passwordRepeat = $_POST['repeat-password'];

        $fieldIsEmpty = empty($login) && empty($mail) && empty($password) && empty($passwordRepeat);

        if (!$fieldIsEmpty) {
            /// Проведение регистрации
            $passwordSame = equalPasswords($password, $passwordRepeat); // Совпадают ли пароли

            if ($passwordSame) {  // Модуль проверки уникальности mail
                $mailUnique = detectUniqueMail($mail);
            }

            if ($passwordSame && $mailUnique) {  // Проведение регистрации
                $hashPassword = hash('sha256', $password);
                registerNewUser($login, $hashPassword, $mail);
            }

        } else {  /// Случай, когда не заполнены все поля
            formIsEmpty();
        }
    }

    function auth() {  // Авторизация
        $login = $_POST['username'];
        $password = $_POST['user-password'];
        $fieldIsEmpty = empty($login) && empty($password);
        $hashPassword = hash('sha256', $password);

        if (!$fieldIsEmpty) {
            findUserInSystem($login, $hashPassword);
        } else {
            formIsEmpty();
        }
    }

    if (isset($_POST['handler'])) {
        $handler = $_POST['handler'];  // Переход к действию

        switch ($handler) {
            case 'auth':
                auth();
                break;

            case 'registration':
                registration();
                break;
        }
    } else {
        $answer = array('body' => 'error', 'message' => "Нет обработчика" . $_POST['handler']);
        answerJson($answer);
    }



    return false;