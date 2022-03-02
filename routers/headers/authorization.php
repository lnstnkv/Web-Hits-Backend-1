<?php

function checkToken()
{
    global $Link;
    $token = substr(getallheaders()['Authorization'], 7);
    $userFromToken = $Link->query("SELECT userId from tokens where value='$token'")->fetch_assoc();


    if (!is_null($userFromToken)) {

        $userId = $userFromToken['userId'];
       
        $user = $Link->query("SELECT * FROM users WHERE userId = '$userId'")->fetch_assoc();
        if (!is_null($user)) {
            return $userId;
        }
    }

    return false;
}

function checkCurrentToken(){
    global $Link;
    $token = substr(getallheaders()['Authorization'], 7);
    $userFromToken = $Link->query("SELECT value from tokens where value='$token'")->fetch_assoc();
    echo ($Link->error);
    if (!is_null($userFromToken)) {
        echo ($Link->error);
        $userId = $userFromToken['userId'];
       
        $user = $Link->query("SELECT * FROM users WHERE userId = '$userId'")->fetch_assoc();
        if (!is_null($user)) {
            return $token;
        }
    }

    return false;

}


