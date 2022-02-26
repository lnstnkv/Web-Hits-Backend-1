<?php

function route($method, $urlList, $requestData)
{
    $link = mysqli_connect("127.0.0.1", "backend", "password", "backend");
    switch ($method) {
        case 'GET':
            
            $token = substr(getallheaders()['Authorization'],7);
            $userFromToken = $link->query("SELECT userId from tokens where value='$token'")->fetch_assoc();
            
            
            if(!is_null($userFromToken)){

                $userId = $userFromToken['userId'];
                $user= $link -> query("SELECT * FROM users WHERE userId = '$userId'")->fetch_assoc();
               
                if(!is_null($user)){
                    echo json_encode( $user);
                }
                else{
                    echo "400";
                }


                
           }
           else{
                echo "404: input data incorrect";
           }
            

            break;
        case 'POST':

           
            $login = $requestData->body->username;
            $user = $link->query("SELECT userId from users where username='$login'")->fetch_assoc();

            if (is_null($user)) {
                $password = hash("sha1", $requestData->body->password);
                $name = $requestData->body->name;
                $username = $requestData->body->username;
                $surname = $requestData->body->surname;
                $roleId = $requestData->body->roleId;
                $userInsertRezult = $link->query("INSERT INTO users( username, password, surname, name, roleId) VALUES('$username', '$password' , '$surname', '$name' , '$roleId')");

                if (!$userInsertRezult) {
                    //400
                    echo "to bad";
                } else {
                    echo "success";
                }

                echo json_encode($requestData);
            } else {
                echo "EXIST";
            }
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
