<?php

include_once "tasks/postInput.php";

function route($method, $urlList, $requestData)
{

    global $Link;
    switch ($method) {
        case 'POST':
            if($urlList[1]){
                if ($urlList[2]){
                   postInput($urlList[1]); 
                }
            }
            else{
                $name = $requestData->body->name;
                $topicId = $requestData->body->topicId;
                $description = $requestData->body->description;
                $price = $requestData->body->price;
                $description = str_replace("'", "\'",$description);
                $tasksInsertRezult = $Link->query("INSERT INTO tasks(name, topicId, description, price, isDraft) VALUES('$name', $topicId, '$description', $price, 0)");
                if(!$tasksInsertRezult)
                {
                    echo json_encode("400");
                    echo json_encode($Link -> errno);
                }else{
                    setHTTPStatus("200", "ОК");
                }

            }
            break;
        case 'GET':
            if ($urlList[1]) {
                $tasksId = $urlList[1];
                $taskIdRezult = $Link->query("SELECT * FROM tasks WHERE id = '$tasksId'")->fetch_assoc();
                if (!is_null($taskIdRezult)) {
                    echo json_encode($taskIdRezult);
                } else {
                    setHTTPStatus('400', "Something went wrong in method $method/tasks");
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
            # code...
            break;
        case 'DELETE':
            
            if ($urlList[1]) {
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
            break;
        default:
            # code...
            break;
    }
}
