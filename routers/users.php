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

                //echo json_encode($urlList[1]);
    
                if($userId==null)
                {
                    $sql = "SELECT * FROM users"; //GET /users//{userId}
                }
                else{
    
                    $sql = "SELECT userId, username,roleId,name,surname FROM users WHERE userId = '$userId'"; //GET /users//{userId}
                }
                    
                                     
                $result = mysqli_query($Link, $sql);
                $output["users"]=[];
                $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
                if ($rows==null){ 
                echo "Пользователи не найдены!";
                }
                else
                {
    
                    foreach ($rows as $row) {
                    
                        $output["users"][]=[
                            "userId"=>$row['userId'],
                            "username"=>$row['username'],
                            "roleId"=>$row['roleId'],
                            "name"=>$row['name'],
                            "surname"=>$row['surname']];
                   
                    
                    } 
                    echo json_encode( $output);
        
                }
                   
                return;

                $userId = $userFromToken['userId'];
                $user = $Link->query("SELECT * FROM users WHERE userId = '$userId'")->fetch_assoc();

                if (!is_null($user)) {
                    echo json_encode($user);
                } else {
                    echo "400";
                }
            } else {
                echo "404: input data incorrect";
            }


            break;
        case 'POST':

            $login = $requestData->body->username;
            if(!validatePassword($requestData->body->password)){
                
                setHTTPStatus("403", "Password is less then 8 characters");
                return; 

            }
            $password = hash("sha1", $requestData->body->password);
            $name = $requestData->body->name;
            $username = $requestData->body->username;
            if(!validateStringNotLess($username, 3)){
                
                setHTTPStatus("403", "Username is less then 3 characters");
                return; 

            }
     
            $surname = $requestData->body->surname;
            $roleId = $requestData->body->roleId;
            $userInsertRezult = $Link->query("INSERT INTO users( username, password, surname, name, roleId) VALUES('$username', '$password' , '$surname', '$name' , '$roleId')");

            if (!$userInsertRezult) {

                if ($Link->errno == 1062) {
                    setHTTPStatus("409", "Login '$login' is taken");
                    return;
                }
            } else {
                setHTTPStatus("200", "User '$login' was succesfully created");
            }

            echo json_encode($requestData);

            break;

        default:
            # code...
            break;
    }
    /*$userId = $urlList[1];

            //echo json_encode($urlList[1]);

            if($userId==null)
            {
                $sql = "SELECT * FROM users"; //GET /users//{userId}
            }
            else{

                $sql = "SELECT userId, username,roleId,name,surname FROM users WHERE userId = '$userId'"; //GET /users//{userId}
            }
                
                                 
            $result = mysqli_query($link, $sql);
            $output["users"]=[];
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if ($rows==null){ 
            echo "Пользователи не найдены!";
            }
            else
            {

                foreach ($rows as $row) {
                
                    $output["users"][]=[
                        "userId"=>$row['userId'],
                        "username"=>$row['username'],
                        "roleId"=>$row['roleId'],
                        "name"=>$row['name'],
                        "surname"=>$row['surname']];
               
                
                } 
                echo json_encode( $output);
    
            }
               
            return;
            */
}
