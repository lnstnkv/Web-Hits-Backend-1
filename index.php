<?php
    header('Content-type:application/json');
  
    $link = mysqli_connect("127.0.0.1", "backend", "password", "backend");

    if (!$link) {
        echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
        echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }

    $message=[];
    $message["users"]=[];

    $res = $link->query("SELECT id, name, login FROM users ORDER BY id ASC");
    if (!$res) //SQL
    {
        echo "Не удалось выполнить запрос: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    else
    {
        while ($row = $res->fetch_assoc()) 
        {
            $message["users"][]=[
                "id"=>$row['id'],
                "login"=>$row['login'],
                "name"=>$row['name']


            ];

        }
    }   
    echo json_encode($_GET);


//mysqli_close($link); 

?>