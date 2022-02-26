<?php
     
     

     function route($method, $urlList, $requestData)
     {

          if ($method == "POST") {

              global $Link;
               switch ($urlList[1]) {
                    case 'login':
                         $login = $requestData->body->username;
                         $password = hash("sha1", $requestData->body->password);

                         $user = $Link->query("SELECT userId from users where username='$login' AND password= '$password'")->fetch_assoc();
               
               
               if(!is_null($user)){
                    $token = bin2hex(random_bytes(20));
                    $userId = $user['userId'];
                    $tokenInsertRezult = $Link->query("INSERT INTO tokens( value, userId ) VALUES('$token', '$userId')");
                    
                    if (!$tokenInsertRezult) {
                         //400
                         echo json_encode($Link -> error);
                    
                    }else {
                    
                         echo json_encode(['token' => $token]);
                    }
               }
               else{
                    setHTTPStatus("404", "There is no such path as 'auth/$urlList[1].'");
               }
               
               
          // echo json_encode($userId);
               break;
                    case 'logout':
                         # code...
                         break;
                    default:
                         setHTTPStatus("404", "There is no such path as 'auth/$urlList[1].'");
                         break;
               }
          } else {
               //400
               setHTTPStatus("400", "Something went wrong in method $method.");
          }
}
