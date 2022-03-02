<?php

function postOutput($id)
{

    $input = $_FILES["output"]; 

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

    move_uploaded_file($temporaryName, $uploadsDirectory . "/tasks" . $id . "output");
}
