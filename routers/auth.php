<?php
/*
include_once "user/user_helper.php";
include_once "headers/authorization.php";

function route($method, $urlList, $requestData)
{

     if ($method == "POST") {

          global $Link;
          switch ($urlList[1]) {
               case 'login':
                    $login = $requestData->body->username;
                    $password = hash("sha1", $requestData->body->password);

                    $user = $Link->query("SELECT userId from users where username ='$login' AND password = '$password'")->fetch_assoc();


                    if (!is_null($user)) {
                         $token = bin2hex(random_bytes(20));
                         $userId = $user['userId'];
                         $tokenInsertRezult = $Link->query("INSERT INTO tokens( value, userId ) VALUES('$token', '$userId')");

                         if (!$tokenInsertRezult) {
                              //400
                              echo json_encode($Link->error);
                         } else {

                              echo json_encode(['token' => $token]);
                         }
                    } else {
                         setHTTPStatus("404", "There is no such path as 'auth/$urlList[1].'");
                    }

                   
                    break;
               case 'logout':
                    if (checkToken()) {
                         $userId= checkToken();
                         $sql = "DELETE FROM tokens WHERE userId='$userId'";
                         $tokensDelete = $Link->query($sql);
                         if (!$tokensDelete) {
                              echo json_encode($Link->error);
                         } else {
                              setHTTPStatus("200", "ОК");
                         }
                    }
                    break;
               case 'register':
                    $login = $requestData->body->username;
                    if (!validatePassword($requestData->body->password)) {

                         setHTTPStatus("403", "Password is less then 8 characters");
                         return;
                    }
                    $password = hash("sha1", $requestData->body->password);
                    $name = $requestData->body->name;
                    $username = $requestData->body->username;
                    if (!validateStringNotLess($username, 3)) {

                         setHTTPStatus("403", "Username is less then 3 characters");
                         return;
                    }

                    $surname = $requestData->body->surname;
                    
                    $userInsertRezult = $Link->query("INSERT INTO users( username, password, surname, name, roleId) VALUES('$username', '$password' , '$surname', '$name' , NULL)");

                    if (!$userInsertRezult) {

                         if ($Link->errno == 1062) {
                              setHTTPStatus("409", "Login '$login' is taken");
                              return;
                         }
                    } else {
                         $userRegister = $Link->query("SELECT userId from users where username ='$login' AND password = '$password'")->fetch_assoc();
                         $token = bin2hex(random_bytes(20));
                         $userId = $userRegister['userId'];
                         $tokenInsertRezult = $Link->query("INSERT INTO tokens( value, userId ) VALUES('$token', '$userId')");

                         if (!$tokenInsertRezult) {
                              setHTTPStatus("404", "There is no such path as 'auth/$urlList[1].'");
                         } else {

                              echo json_encode(['token' => $token]);
                         }
                    }


                    break;

               default:
                    setHTTPStatus("404", "There is no such path as 'auth/$urlList[1].'");
                    break;
          }
     } else {
          
          setHTTPStatus("400", "Something went wrong in method $method.");
     }
}
*/