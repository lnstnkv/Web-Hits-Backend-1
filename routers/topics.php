<?php

include_once "headers/authorization.php";


function getTopics($nameTopic, $parentId)
{
    global $Link;
    if (!empty($nameTopic) && !empty($parentId)) {
        $result = mysqli_query($Link, "SELECT * FROM topics WHERE  name= '$nameTopic' AND parentId= '$parentId'");
        $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } elseif (!empty($nameTopic) && empty($parentId)) {
        $result = mysqli_query($Link, "SELECT * FROM topics WHERE  name= '$nameTopic'");
        $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } elseif (empty($nameTopic) && !empty($parentId)) {
        $result = mysqli_query($Link, "SELECT * FROM topics WHERE  parentId= '$parentId'");
        $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $result = mysqli_query($Link, "SELECT * FROM topics");
        $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    if (is_null($topics)) {
        setHTTPStatus("400", "Bad request. If some data are strange");
    } else {
        echo json_encode($topics);
    }
}
function getChilds($id)
{
    global $Link;
    $result = mysqli_query($Link, "SELECT * FROM topics where parentId= '$id' ");
    $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return ($topics);
}
function deleteChilds($arrayDelete, $id)
{
    global $Link;
    if (empty($arrayDelete)) {
        setHTTPStatus("400", "Bad request. If some data are strange");
        exit();
    }
    if (!is_null(getChilds($id)) && count($arrayDelete) != 0) {

        foreach ($arrayDelete as $value) {
            $sql = "UPDATE topics SET parentId=NULL WHERE id = '$value'";
            $topicDelete = $Link->query($sql);
            if (!$topicDelete) {
                echo json_encode($Link->error);
                setHTTPStatus("400", "Something went wrong in delete");
            } else {
                getTopic($id);
            }
        }
    }
}
function postChilds($arrayPost, $id)
{
    global $Link;
    if (empty($arrayPost)) {
        setHTTPStatus("400", "Bad request. If some data are strange");
        exit();
    }
    if (!is_null(getChilds($id)) && count($arrayPost) != 0) {

        foreach ($arrayPost as $value) {
            $sql = "UPDATE topics SET parentId= '$id' WHERE id = '$value'";
            $topicPost = $Link->query($sql);
            if (!$topicPost) {
                echo json_encode($Link->error);
                setHTTPStatus("400", "Something went wrong in post");
            } else {
                getTopic($id);
            }
        }
    }
}


function getTopic($userId)
{
    global $Link;
    $result = mysqli_query($Link, "SELECT * FROM topics WHERE id = '$userId'");
    $topic = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (is_null($topic)) {
        setHTTPStatus("400", "Something went wrong in topic request");
    } else {
        $topic[0]['childs'] = getChilds($userId);
        echo json_encode($topic[0]);
    }
}
function getTopicWithoitChild($userId)
{
    global $Link;
    $result = mysqli_query($Link, "SELECT * FROM topics WHERE id = '$userId'");
    $topic = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (is_null($topic)) {
        setHTTPStatus("400", "Something went wrong in topic request");
    } else {
        echo json_encode($topic[0]);
    }
}

function route($method, $urlList, $requestData)
{
    $nameTopic = $requestData->parameters["name"];
    $parentId = $requestData->parameters["parent"];
    global $Link;
    if (checkToken()) {
        switch ($method) {
            case 'GET':
                if ($urlList[1] && !$urlList[2]) {
                    getTopic($urlList[1]);
                }

                if ($urlList[1] && $urlList[2] == "childs") {
                    $getChild = getChilds($urlList[1]);
                    if (sizeof($getChild) > 0) {
                        echo json_encode($getChild);
                    } else {
                        setHTTPStatus("400", "Bad request. Some data are strange. There is not child");
                    }
                } elseif (!$urlList[1] && !$urlList[2]) {
                    getTopics($nameTopic, $parentId);
                }


                break;

            case 'POST':
                if (isAdmin()) {


                    if ($urlList[1] && $urlList[2] == "childs") { // childs
                        $arrayPost = ($requestData->body);
                        postChilds($arrayPost, $urlList[1]);
                    } else {
                        $name = $requestData->body->name;
                        $parentId = $requestData->body->parentId;
                        $taskInsertRezult = $Link->query("INSERT INTO topics(name, parentId) VALUES('$name', '$parentId')");

                        if (!$taskInsertRezult) {

                            setHTTPStatus("400", "Bad request. Some data are strange");
                        } else {
            
                            getTopicWithoitChild($Link->insert_id);
                        }
                    }
                } else {
                    setHTTPStatus("403", "Available only for admin");
                }
                break;
            case 'PATCH':
                if (!isAdmin()) {
                    setHTTPStatus("403", "Available only for admin");
                    return;
                }
                if ($urlList[1]) {
                    $findTopicRezult = mysqli_query($Link, "SELECT * FROM topics WHERE id= $urlList[1] ");
                    $topic = mysqli_fetch_all($findTopicRezult, MYSQLI_ASSOC);
                    if (is_null($topic)) {
                        setHTTPStatus("400", "There is not this topic with '$urlList[1]'");
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
                            setHTTPStatus("400", "Bad request. If some data are strange");
                        } else {
                            echo json_encode($topic);
                        }
                    }
                }
                break;

            case 'DELETE':
                if (!isAdmin()) {
                    setHTTPStatus("403", "Available only for admin");
                    return;
                }
                if ($urlList[2] == "childs") {
                    if ($urlList[1]) {
                        $arrayDelete = ($requestData->body);
                        deleteChilds($arrayDelete, $urlList[1]);
                    }
                } else {
                    if ($urlList[1]) {
                        $findTopicRezult = mysqli_query($Link, "SELECT * FROM topics WHERE id= $urlList[1] ");
                        $topic = mysqli_fetch_all($findTopicRezult, MYSQLI_ASSOC);
                        if (is_null($topic)) {
                            setHTTPStatus("400", "Bad request. If some data are strange");
                        } else {
                            $sql = "DELETE FROM topics WHERE  id=$urlList[1]";
                            $topicDelete = $Link->query($sql);
                            if (!$topicDelete) {
                                setHTTPStatus("400", "Bad request. If some data are strange");
                            } else {
                                setHTTPStatus("200", "ОК");
                            }
                        }
                    }
                }





                break;
            default:

                break;
        }
    } else {
        setHTTPStatus("403", "Permission denied. Authorization token are invalid");
    }
}
