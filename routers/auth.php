<?php
function route($method, $urlList, $requestData)
{

     if ($method == "POST") {

          $link = mysqli_connect("127.0.0.1", "backend", "password", "backend");
          switch ($urlList[1]) {
               case 'login':
                    $login = $requestData->body->username;
                    $password = hash("sha1", $requestData->body->password);

                    $user = $link->query("SELECT userId from users where username='$login' AND password= '$password'")->fetch_assoc();
          
          
          if(!is_null($user)){
               $token = bin2hex(random_bytes(20));
               $userId = $user['userId'];
               $tokenInsertRezult = $link->query("INSERT INTO tokens( value, userId ) VALUES('$token', '$userId')");
               
               if (!$tokenInsertRezult) {
                    //400
                    echo json_encode($link -> error);
               
               }else {
                   
                    echo json_encode(['token' => $token]);
               }
          }
          else{
               echo "404: input data incorrect";
          }
          
          
         // echo json_encode($userId);
          break;
               case 'logout':
                    # code...
                    break;
               default:
                    # code...
                    break;
          }
     } else {
          //400
          echo "bas request";
     }
}
