<?php

include_once "user/user_helper.php";
include_once "headers/authorization.php";

function route($method, $urlList, $requestData)
{

    if ($method == "POST") {

        global $Link;


        $login = $requestData->body->username;
        $password = hash("sha1", $requestData->body->password);
        $name = $requestData->body->name;
        $username = $requestData->body->username;
       

        $surname = $requestData->body->surname;

        $userInsertRezult = $Link->query("INSERT INTO users( username, password, surname, name, roleId) VALUES('$username', '$password' , '$surname', '$name' , NULL)");

        if (!$userInsertRezult) {

            setHTTPStatus("400", "Bad request. If some data are strange");
        } else {
            $userRegister = $Link->query("SELECT userId from users where username ='$login' AND password = '$password'")->fetch_assoc();
            $token = bin2hex(random_bytes(20));
            $userId = $userRegister['userId'];
            $tokenInsertRezult = $Link->query("INSERT INTO tokens( value, userId ) VALUES('$token', '$userId')");

            if (!$tokenInsertRezult) {
                setHTTPStatus("403", "Perm");
            } else {

                echo json_encode(['token' => $token]);
            }
        }

    } else {

        setHTTPStatus("400", "Something went wrong in method $method.");
    }
}
