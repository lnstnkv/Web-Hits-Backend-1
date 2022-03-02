<?php

include_once "headers/authorization.php";
function route($method, $urlList, $requestData)
{
    global $Link;
    if (checkToken() == false) {
        setHTTPStatus("403", "Permission denied. Authorization token are invalid");
        return;
    }
    switch ($method) {
        case 'GET':
            if ($urlList[1]) {
                $roleId = $urlList[1];
                $roleIdRezult = $Link->query("SELECT * FROM roles WHERE roleId = '$roleId'")->fetch_assoc();
                if (!is_null($roleIdRezult)) {
                    echo json_encode($roleIdRezult);
                } else {
                    setHTTPStatus('400', "Something went wrong in method $method/roles");
                }
            } else {
                
                
                $roleIdsql = mysqli_query($Link, "SELECT * FROM roles");
                $roleSelectRezult = mysqli_fetch_all($roleIdsql, MYSQLI_ASSOC);
                if (!is_null($roleSelectRezult)) {
                    echo json_encode($roleSelectRezult);
                } else {
                    setHTTPStatus('400', "Something went wrong in method $method/roles");
                }
            }
            break;

        default:
            setHTTPStatus("400", "Something went wrong in method $method.");
            break;
    }
}
