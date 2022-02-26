<?php
function route($method, $urlList, $requestData)
{
    global $Link;
    switch ($method) {
        case 'GET':

            $token = substr(getallheaders()['Authorization'], 7);
            $userFromToken = $Link->query("SELECT userId from tokens where value='$token'")->fetch_assoc();


            if (!is_null($userFromToken)) {

                $userId = $userFromToken['userId'];
                $user = $Link->query("SELECT * FROM users WHERE userId = '$userId'")->fetch_assoc();

                if (!is_null($user)) {
                    
                    $result = mysqli_query($Link, "SELECT * FROM `topics`");
                    $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    if (is_null($topics)){ 
                         setHTTPStatus("400", "ПРИДУМАЙ!"); 
                    }
                    else
                    {
                        echo json_encode( $topics);
                      
                    }
                    
                } else {
                    setHTTPStatus("400", "ПРИДУМАЙ!");
                }

            } else {
                echo "404: input data incorrect";
            }


            break;

        case 'POST':

            $name = $requestData->body->name;
            $parentId = $requestData->body->parentId;
           
            $taskInsertRezult = $Link->query("INSERT INTO topics( name, parentId) VALUES('$name', '$parentId')");

            if (!$taskInsertRezult) {
            
                if ($Link->errno == 1062) {
                    setHTTPStatus("409", "Login '$name' is taken");
                    return;
                }
            } else {
                
                setHTTPStatus("200", "User '$name' was succesfully created");
            }

           echo json_encode($requestData);

            break;

        default:
            
            break;
    }
}