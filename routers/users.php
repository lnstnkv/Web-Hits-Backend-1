<?php

include_once "user/user_helper.php";
function route($method, $urlList, $requestData)
{
    global $Link;
    switch ($method) {
        case 'GET':

            $token = substr(getallheaders()['Authorization'], 7);
            $userFromToken = $Link->query("SELECT userId from tokens where value='$token'")->fetch_assoc();


            if (!is_null($userFromToken)) {
                $userId = $urlList[1];

                echo json_encode($urlList[1]);

                if (is_null($userId)) {
                    $sql = "SELECT * FROM users"; //GET /users//{userId}
                } else {

                    $sql = "SELECT userId, username,roleId,name,surname FROM users WHERE userId = '$userId'"; //GET /users//{userId}
                }


                $result = mysqli_query($Link, $sql);
                $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
                if (!is_null($users)) {

                    echo json_encode($users);
                } else {

                    setHTTPStatus("400", "Something went wrong in method /users");
                    return;
                }

                return;

                $userIdToken = $userFromToken['userId'];
                $user = $Link->query("SELECT * FROM users WHERE userId = '$userIdToken'")->fetch_assoc();

                if (!is_null($user)) {
                    echo json_encode($user);
                } else {
                    setHTTPStatus("400", "Something went wrong in method /users/{userId}");
                }
            } else {
                echo "404: input data incorrect";
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
                    echo json_encode("400");
                    // echo json_encode($Link -> error);
                } else {
                    $userSelectIntoUpdate = mysqli_fetch_all($findUserResult, MYSQLI_ASSOC);
                    echo json_encode($userSelectIntoUpdate);
                }
            }
            break;
        case 'DELETE':
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
            # code...
            break;
    }
}

                /* $name = $requestData->body->name;
                    if(empty($name))
                    {
                        echo "ehfff";
                    }
                    $surname = $requestData->body->surname;
                    $password = hash("sha1", $requestData->body->password);
                    $userUpdate = $Link->query("UPDATE users SET name='$name', surname='$surname', password='$password' WHERE userId=$urlList[1]");
                   
                    if($userUpdate){
                        $userSelectIntoUpdate = mysqli_fetch_all($findUserResult, MYSQLI_ASSOC);
                        echo json_encode($userSelectIntoUpdate);
                    }
                    else
                    {
                        echo json_encode($Link -> error);
                    }
                    */