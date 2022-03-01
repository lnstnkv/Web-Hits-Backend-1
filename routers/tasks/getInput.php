
<?php
function getInput($id)
{
    $path = "uploads" . "/tasks" . $id . "input";

    if (file_exists($path)) {
        setHTTPStatus("200", "Ok");
        readfile($path);
    } else {
        setHTTPStatus("400", "File dosnt't exists");
    }
}
