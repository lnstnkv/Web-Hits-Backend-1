<?php

include_once "headers/authorization.php";

function getSolution($userId, $taskId)
{
    global $Link;
    if (!empty($userId) && !empty($taskId)) {
        $result = mysqli_query($Link, "SELECT * FROM solutions WHERE taskId = '$taskId' AND authorId='$userId'");
        $solutions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } elseif (!empty($userId) && empty($taskId)) {
        $result = mysqli_query($Link, "SELECT * FROM solutions WHERE  authorId= '$userId'");
        $solutions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } elseif (empty($userId) && !empty($taskId)) {
        $result = mysqli_query($Link, "SELECT * FROM solutions WHERE  taskId= '$taskId'");
        $solutions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $result = mysqli_query($Link, "SELECT * FROM solutions");
        $solutions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    if (is_null($solutions)) {
        setHTTPStatus("400", "Bad request. If some data are strange");
    } else {
        echo json_encode($solutions);
    }
}

function route($method, $urlList, $requestData)
{
    $userId = $requestData->parameters["user"];
    $taskId = $requestData->parameters["task"];
    global $Link;
    if (checkToken() == false) {
        setHTTPStatus("403", "Permission denied. Authorization token are invalid");
        return;
    }
    switch ($method) {
        case 'GET':
            if (!$urlList[1]) {
                getSolution($userId, $taskId);
            }
            break;
        case 'POST':
            if (!isAdmin()) {
                setHTTPStatus("403", "Available only for admin");
                return;
            }
            if ($urlList[1]) {
                if ($urlList[2] == "postmoderation") {
                    $verdict = $requestData->body->verdict;
                    $postmoderationRezult = $Link->query("UPDATE solutions SET verdict= '$verdict' WHERE id='$urlList[1]'");
                    if (!$postmoderationRezult) {
                        setHTTPStatus("400", "Bad request. If some data are strange");
                    } else {
                        getSolution($urlList[1],'');
                    }
                }
                else
                {
                    setHTTPStatus("400", "Bad request. If some data are strange");
                }
            }
            break;
        default:
            setHTTPStatus("400", "Bad request. If some data are strange");
            break;
    }
}
