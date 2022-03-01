<?php
/* 
        В данном файле собраны вспомогательные функции для HTTP-статусов
    */

function setHTTPStatus($status = '200', $message = null)
{

    switch ($status) {

        default:
        case '200':
            $status = 'HTTP/1.0 200 OK';
            break;
        case '400':
            $status = 'HTTP/1.0 400 Bad request';
            break;
        case '404':
            $status = 'HTTP/1.0 404 Not found';
            break;
        case '403':
            $status = 'HTTP/1.0 403 Permission denied';
            break;
        case '403':
            $status = 'HTTP/1.0 403 Permission denied';
            break;
        case '500':
            $status = 'HTTP/1.0 500 Unexpected error';
            break;
    }
    header($status);
    if (!is_null($message)) {

        echo json_encode(['message' => $message]);
    }
}

function isAdmin(): bool
{
    global $Link;
    $user = $Link->query("SELECT roleId from users join tokens ON users.userId = tokens.userId ")->fetch_assoc();

    foreach($user as $k=>$row){
        $nr = $row[0];
      } 
    if ($nr==1){
        return true;
    }
    return false;
}
