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

                    if ($urlList[1]) {
                        $result = mysqli_query($Link, "SELECT * FROM topics WHERE id= $urlList[1] ");
                        $topic = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        if (is_null($topic)) {
                            setHTTPStatus("400", "ПРИДУМАЙ!");
                        } else {
                            echo json_encode($topic);
                        }
                    } else {
                        $result = mysqli_query($Link, "SELECT * FROM topics");
                        $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        if (is_null($topics)) {
                            setHTTPStatus("400", "ПРИДУМАЙ!");
                        } else {
                            echo json_encode($topics);
                        }
                    }
                }
            } else {
                setHTTPStatus("403", "Permission denied. Authorization token are invalid");
            }


            break;

        case 'POST':

            $name = $requestData->body->name;
            $parentId = $requestData->body->parentId;

            $taskInsertRezult = $Link->query("INSERT INTO topics(name, parentId) VALUES('$name', '$parentId')");

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
        case 'PATCH':

            if ($urlList[1]) {
                $findTopicRezult = mysqli_query($Link, "SELECT * FROM topics WHERE id= $urlList[1] ");
                $topic = mysqli_fetch_all($findTopicRezult, MYSQLI_ASSOC);
                if (is_null($topic)) {
                    setHTTPStatus("400", "ПРИДУМАЙ!");
                } else {

                    $name = $requestData->body->name;
                    $parentId = $requestData->body->parentId;

                    if (!empty($name) && (!empty($parentId))) {
                        $name = "name='$name',";
                        $parentId = "parentId='$parentId'";
                    } elseif (!empty($name)) {
                        $name = "name='$name'";
                    } elseif (!empty($parentId)) {
                        $parentId = "parentId='$parentId'";
                    }
                    $userUpdate = $Link->query("UPDATE topics SET $name $parentId WHERE id= $urlList[1]");
                    if (!$userUpdate) {
                        echo json_encode("400");
                        echo json_encode($Link -> error);
                    } else {
                        echo json_encode($topic);
                    }
                }
            }
            break;

        case 'DELETE':
        
            if ($urlList[1]) {
                $findTopicRezult = mysqli_query($Link, "SELECT * FROM topics WHERE id= $urlList[1] ");
                $topic = mysqli_fetch_all($findTopicRezult, MYSQLI_ASSOC);
                if (is_null($topic)) {
                    setHTTPStatus("400", "ПРИДУМАЙ!");
                } else {
                    $sql = "DELETE FROM topics WHERE id= $urlList[1]";
                    $topicDelete = $Link->query($sql);
                    if (!$topicDelete) {
                        echo json_encode($Link->error);
                    } else {
                        setHTTPStatus("200", "ОК");
                    
                    }
                   
                }
            }
        
        
        
        
        
            break;
        default:

            break;
    }
}
