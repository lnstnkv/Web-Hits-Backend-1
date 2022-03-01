<?php

function checkToken():bool{
    global $Link;
    $token = substr(getallheaders()['Authorization'], 7);
    $userFromToken = $Link->query("SELECT userId from tokens where value='$token'")->fetch_assoc();


    if (!is_null($userFromToken)) {

        $userId = $userFromToken['userId'];
        $user = $Link->query("SELECT * FROM users WHERE userId = '$userId'")->fetch_assoc();
        if(!is_null($user))
        {
            return true;
        }
    }

return false;
}