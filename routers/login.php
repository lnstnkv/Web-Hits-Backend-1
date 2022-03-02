<?php

include_once "user/user_helper.php";
include_once "headers/authorization.php";

function route($method, $urlList, $requestData)
{

    if ($method == "POST") {

        global $Link;


        $login = $requestData->body->username;
        $password = hash("sha1", $requestData->body->password);

        $user = $Link->query("SELECT userId from users where username ='$login' AND password = '$password'")->fetch_assoc();


        if (!is_null($user)) {
            $token = bin2hex(random_bytes(20));
            $userId = $user['userId'];
            $tokenInsertRezult = $Link->query("INSERT INTO tokens( value, userId ) VALUES('$token', '$userId')");

            if (!$tokenInsertRezult) {
                setHTTPStatus("400", "Bad request. If some data are strange");
            } else {

                echo json_encode(['token' => $token]);
            }
        } else {
            setHTTPStatus("403", "Permision");//NB
        }
    } else {

        setHTTPStatus("400", "Something went wrong in method $method.");
    }
}
