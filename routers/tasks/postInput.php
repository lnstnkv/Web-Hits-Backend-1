<?php

function postInput($id)
{

    $input = $_FILES["input"]; 
    echo json_encode($input);

    if ($input["type"] != "text/plain") {
        setHTTPStatus("400", "Wrong file format");
        exit;
    }
   

    if ($input["error"] != 0) {
        setHTTPStatus("400", "Error in file");
        exit;
    }

    $uploadsDirectory = "uploads";
    $temporaryName = $input["tmp_name"];

    move_uploaded_file($temporaryName, $uploadsDirectory . "/tasks" . $id . "input");
}
