<?php

include_once "tasks/postInput.php";
include_once "tasks/getInput.php";
include_once "tasks/deleteInput.php";
include_once "tasks/getOutput.php";
include_once "tasks/deleteOutput.php";
include_once "tasks/postOutput.php";
include_once "headers/authorization.php";

function getTasks($nameTask, $topic)
{
    global $Link;
    if (!empty($nameTask) && !empty($topic)) {
        $result = mysqli_query($Link, "SELECT * FROM tasks WHERE topicId = '$topic' AND name='$nameTask'");
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } elseif (!empty($nameTask) && empty($topic)) {
        $result = mysqli_query($Link, "SELECT * FROM tasks WHERE  name= '$nameTask'");
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } elseif (empty($nameTask) && !empty($topic)) {
        $result = mysqli_query($Link, "SELECT * FROM tasks WHERE  topicId= '$topic'");
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $result = mysqli_query($Link, "SELECT * FROM tasks");
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    if (is_null($tasks)) {
        setHTTPStatus("400", "Bad request. If some data are strange");
    } else {
        echo json_encode($tasks);
    }
}

function createSolution($taskId, $sourseCode, $programmingLanguage, $userId)
{
    global $Link;
    $sourseCode = str_replace("'", "\'", $sourseCode);

    $createSolutionRezult = $Link->query("INSERT INTO solutions(sourseCode, programmingLanguage, verdict, authorId, taskId) VALUES('$sourseCode', '$programmingLanguage', 'Pending', $userId, $taskId)");


    if (!$createSolutionRezult) {

       setHTTPStatus("400", "Bad request. If some data are strange");
    } else {
        $result = mysqli_query($Link, "SELECT * FROM solutions WHERE taskId = '$taskId' AND authorId='$userId'");
        $solutions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($solutions);
    }
}

function route($method, $urlList, $requestData)
{
    $name = $requestData->parameters["name"];
    $topicId = $requestData->parameters["topic"];
    global $Link;
    $userId = checkToken();
    if ($userId == false) {
        setHTTPStatus("403", "Permission denied. Authorization token are invalid");
        return;
    }
    switch ($method) {
        case 'POST':
            if (!isAdmin()) {
                setHTTPStatus("403", "Available only for admin");
                return;
            }
            if ($urlList[1]) {
                if ($urlList[2]) {
                    switch ($urlList[2]) {
                        case 'input':
                            postInput($urlList[1]);
                            $tasksId = $urlList[1];
                            $taskIdRezult = $Link->query("SELECT * FROM tasks WHERE id = '$tasksId'")->fetch_assoc();
                            if (!is_null($taskIdRezult)) {
                                echo json_encode($taskIdRezult);
                            } else {
                                setHTTPStatus('400', "Something went wrong in method $method/tasks");
                            }
                        break;
                        case 'output':
                            postOutput($urlList[1]);
                            $tasksId = $urlList[1];
                            $taskIdRezult = $Link->query("SELECT * FROM tasks WHERE id = '$tasksId'")->fetch_assoc();
                            if (!is_null($taskIdRezult)) {
                                echo json_encode($taskIdRezult);
                            } else {
                                setHTTPStatus('400', "Something went wrong in method $method/tasks");
                            }
                            break;
                        case 'solution':
                            $sourseCode = $requestData->body->sourseCode;
                            $programmingLanguage = $requestData->body->programmingLanguage;
                            createSolution($urlList[1], $sourseCode, $programmingLanguage, $userId);
                            break;
                        default:
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
                    setHTTPStatus('400', "Something went wrong in method $method/tasks");
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


                getTasks($name, $topicId);
            }
            break;
        case 'PATCH':
            if (!isAdmin()) {
                setHTTPStatus("403", "Available only for admin");
                return;
            }
            if ($urlList[1]) {
                $sql = "SELECT * FROM tasks WHERE id=$urlList[1]"; //PATCH /users//{userId}
                $findTaskResult = mysqli_query($Link, $sql);

                $name = $requestData->body->name;
                $topicId = $requestData->body->topicId;
                $description = $requestData->body->description;
                $price = $requestData->body->price;
                $description = str_replace("'", "\'", $description);

                if ($findTaskResult) {
                    $taskUpdate = $Link->query("UPDATE tasks SET name= '$name', topicId=$topicId, description='$description', price=$price WHERE id='$urlList[1]'");

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
            if (!isAdmin()) {
                setHTTPStatus("403", "Available only for admin");
                return;
            }
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
                            break;
                    }
                } else {
                    $findTaskRezult = mysqli_query($Link, "SELECT * FROM tasks WHERE id= '$urlList[1]' ");
                    $taskFindForDelete = mysqli_fetch_all($findTaskRezult, MYSQLI_ASSOC);
                    if (is_null($taskFindForDelete)) {
                        setHTTPStatus("400", "Bad request. If some data are strange");
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
            setHTTPStatus("400", "Bad request. If some data are strange");
            break;
    }
}
