<?php

include_once "tasks/postInput.php";
include_once "tasks/getInput.php";
include_once "tasks/deleteInput.php";
include_once "tasks/getOutput.php";
include_once "tasks/deleteOutput.php";
include_once "tasks/postOutput.php";

function route($method, $urlList, $requestData)
{

    global $Link;
    switch ($method) {
        case 'POST':
            if ($urlList[1]) {
                if ($urlList[2]) {
                    switch ($urlList[2]) {
                        case 'input':
                            postInput($urlList[1]);
                            break;
                        case 'output':
                            postOutput($urlList[1]);
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            } else {

                $name = $requestData->body->name;
                $topicId = $requestData->body->topicId;
                $description = $requestData->body->description;
                $price = $requestData->body->price;
                $description = str_replace("'", "\'", $description);
                $tasksInsertRezult = $Link->query("INSERT INTO tasks(name, topicId, description, price, isDraft) VALUES('$name', $topicId, '$description', $price, 0)");
                if (!$tasksInsertRezult) {
                    echo json_encode("400");
                    echo json_encode($Link->errno);
                } else {
                    setHTTPStatus("200", "ОК");
                }
            }
            break;
        case 'GET':
            if ($urlList[1]) {
                if ($urlList[2]) {
                    switch ($urlList[2]) {
                        case 'input':
                            getInput($urlList[1]);
                            break;
                        case 'output':
                            getOutput($urlList[1]);
                            break;
                        default:
                            # code...
                            break;
                    }
                } else {
                    $tasksId = $urlList[1];
                    $taskIdRezult = $Link->query("SELECT * FROM tasks WHERE id = '$tasksId'")->fetch_assoc();
                    if (!is_null($taskIdRezult)) {
                        echo json_encode($taskIdRezult);
                    } else {
                        setHTTPStatus('400', "Something went wrong in method $method/tasks");
                    }
                }
            } else {


                $tasksIdsql = mysqli_query($Link, "SELECT * FROM tasks");
                $tasksSelectRezult = mysqli_fetch_all($tasksIdsql, MYSQLI_ASSOC);
                if (!is_null($tasksSelectRezult)) {
                    echo json_encode($tasksSelectRezult);
                } else {
                    setHTTPStatus('400', "Something went wrong in method $method/tasks");
                }
            }
            break;
        case 'PATCH':
            if ($urlList[1]) {
                $sql = "SELECT * FROM tasks WHERE id=$urlList[1]"; //PATCH /users//{userId}
                $findTaskResult = mysqli_query($Link, $sql);

                $name = $requestData->body->name;
                $topicId = $requestData->body->topicId;
                $description = $requestData->body->description;
                $price = $requestData->body->price;
                $description = str_replace("'", "\'", $description);
             
                if ($findTaskResult) {
                    $taskUpdate = $Link->query("UPDATE tasks SET name= '$name', topicId=$topicId, description='$description', price=$price WHERE id=$urlList[1]");
                   
                    if ($taskUpdate) {
                        $taskSelectIntoUpdate = mysqli_fetch_all($findTaskResult, MYSQLI_ASSOC);
                        echo json_encode($taskSelectIntoUpdate);
                    } else {
                        setHTTPStatus('400', "Something went wrong in method $method/tasks");
                        echo json_encode($Link->error);
                    }
                } else {
                    setHTTPStatus('400', "This is no task with id = $urlList[1] ");
                }
            }
            break;
        case 'DELETE':

            if ($urlList[1]) {
                if ($urlList[2]) {
                    switch ($urlList[2]) {
                        case 'input':
                            deleteInput($urlList[1]);
                            break;
                        case 'output':
                            deleteOutput($urlList[1]);
                            break;
                        default:
                            # code...
                            break;
                    }
                } else {
                    $findTaskRezult = mysqli_query($Link, "SELECT * FROM tasks WHERE id= $urlList[1] ");
                    $taskFindForDelete = mysqli_fetch_all($findTaskRezult, MYSQLI_ASSOC);
                    if (is_null($taskFindForDelete)) {
                        setHTTPStatus("400", "ПРИДУМАЙ!");
                    } else {
                        $sql = "DELETE FROM tasks WHERE id= $urlList[1]";
                        $taskDelete = $Link->query($sql);
                        if (!$taskDelete) {
                            echo json_encode($Link->error);
                        } else {
                            setHTTPStatus("200", "ОК");
                        }
                    }
                }
            }
            break;
        default:
            # code...
            break;
    }
}
