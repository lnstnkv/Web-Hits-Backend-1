<?php
include_once "headers/authorization.php";
include_once "user/user_helper.php";
function route($method, $urlList, $requestData)
{
    global $Link;
    if (checkToken() == false) {
        setHTTPStatus("403", "Permission denied. Authorization token are invalid");
        return;
    }
    switch ($method) {
        case 'GET':

            $userId = $urlList[1];

            if (is_null($userId)) {
                if (!isAdmin()) {
                    setHTTPStatus("403", "Available only for admin");
                    return;
                } else {
                    $sql = "SELECT * FROM users"; //GET /users//{userId}
                }
            } else {
                if (checkToken() != $userId) {
                    setHTTPStatus("403", "Available only for admin");
                    return;
                }
                $sql = "SELECT userId, username,roleId,name,surname FROM users WHERE userId = '$userId'"; //GET /users//{userId}
            }


            $result = mysqli_query($Link, $sql);
            $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if (!is_null($users)) {

                echo json_encode($users);
            } else {

                setHTTPStatus("400", "Bad request. If some data are strange");
                return;
            }

            return;


            $user = $Link->query("SELECT * FROM users WHERE userId = '$userId")->fetch_assoc();

            if (!is_null($user)) {
                echo json_encode($user);
            } else {
                setHTTPStatus("400", "Something went wrong in method /users/{userId}");
            }



            break;
        case 'POST':
            if (!isAdmin()) {
                setHTTPStatus("403", "Available only for admin");
                return;
            }
            if ($urlList[1]) {
                $roleId = $requestData->body->roleId;
                $userRoleIdRezult = $Link->query("UPDATE users SET roleId=$roleId WHERE userId=$urlList[1]");
                if (!$userRoleIdRezult) {
                    setHTTPStatus("400", "Something went wrong in method /users/{userId}");
                } else {
                    setHTTPStatus("200", "ОК");
                }
            }


            break;
        case 'PATCH':
            if ($urlList[1]) {
                $sql = "SELECT userId, username,roleId,name,surname FROM users WHERE userId = $urlList[1]"; //PATCH /users//{userId}
                $findUserResult = mysqli_query($Link, $sql);

                $name = $requestData->body->name;
                $surname = $requestData->body->surname;
                $password = hash("sha1", $requestData->body->password);

                if (!empty($name) && (!empty($surname) || !empty($password))) {
                    $name = "name='$name',";
                } elseif (!empty($name)) {
                    $name = "name='$name'";
                }
                if (!empty($surname) && (!empty($password))) {
                    $surname = "surname='$surname',";
                } elseif (empty($password)) {
                    $surname = "surname='$surname'";
                }

                if (!empty($password)) {
                    $password = "password='$password'";
                }

                $userUpdate = $Link->query("UPDATE users SET $name $surname $password WHERE userId=$urlList[1]");
                if (!$userUpdate) {
                    setHTTPStatus("400", "Bad request. If some data are strange");
                } else {
                    $userSelectIntoUpdate = mysqli_fetch_all($findUserResult, MYSQLI_ASSOC);
                    echo json_encode($userSelectIntoUpdate);
                }
            }
            break;
        case 'DELETE':
            if (!isAdmin()) {
                setHTTPStatus("403", "Available only for admin");
                return;
            }
            if ($urlList[1]) {
                $sql = "DELETE FROM users WHERE userId=$urlList[1]";
                $userDelete = $Link->query($sql);
                if (!$userDelete) {
                    echo json_encode($Link->error);
                } else {
                    setHTTPStatus("200", "ОК");
                }
            }
            break;
        default:
            setHTTPStatus("400", "Something went wrong in method $method.");
            break;
    }
}
