<?php
//GET /users//{userId}
  function route($method, $urlList, $requestData) { 
      //  echo json_encode($urlList);
     
    
            $userId = $urlList[1];

           // echo json_encode($urlList[1]);
            
            $link = mysqli_connect("127.0.0.1", "backend", "password", "backend");
            $sql = "SELECT userId, username,roleId,name,surname FROM users WHERE userId= '$userId'";
            $result = mysqli_query($link, $sql);
            $message["users"]=[];
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                foreach ($rows as $row) {
                
                    $message["users"][]=[
                        "userId"=>$row['userId'],
                        "username"=>$row['username'],
                        "roleId"=>$row['roleId'],
                        "name"=>$row['usernnameame'],
                        "surname"=>$row['surname']];
               
                
                }

                echo json_encode( $message);
    
            return;
    

}
