<?php

  function route($method, $urlList, $requestData) { 
      //  echo json_encode($urlList);
     
    
            $userId = $urlList[1];

            //echo json_encode($urlList[1]);

            if($userId==null)
            {
                $sql = "SELECT * FROM users"; //GET /users//{userId}
            }
            else{

                $sql = "SELECT userId, username,roleId,name,surname FROM users WHERE userId= '$userId'"; //GET /users//{userId}
            }
            $link = mysqli_connect("127.0.0.1", "backend", "password", "backend");
            
                                 
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
    

}
