<?php

include_once "user/user_helper.php";
include_once "headers/authorization.php";

function route($method, $urlList, $requestData)
{

    if ($method == "POST") {

        global $Link;
        if (checkToken()) {
            $userId = checkToken();
            $token=checkCurrentToken();
            $sql = "DELETE FROM tokens WHERE userId='$userId' AND value='$token'";
            $tokensDelete = $Link->query($sql);
            if (!$tokensDelete) {
                setHTTPStatus("400", "Something went wrong in method $method.");
            } else {
                setHTTPStatus("200", "ОК");
            }
        }
    
    } else {

        setHTTPStatus("400", "Something went wrong in method $method.");
    }
}
