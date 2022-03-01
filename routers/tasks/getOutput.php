<?php
function getOutput($id)
{
    $path = "uploads" . "/tasks" . $id . "output";

    if (file_exists($path)) {
        setHTTPStatus("200", "Ok");
        readfile($path);
    } else {
        setHTTPStatus("400", "File dosnt't exists");
    }
}
