<?php
    
    function getData($method)
    {
        $data= new stdClass();
        if($method!="GET")
        {
            $data-> body = json_decode(file_get_contents('php://input'));
                    
        }
        $data->parameters = [];
            $dataGet= $_GET;
            foreach ($dataGet as $key => $value) {
                if($key!="q")
                {
                    $data->parameters[$key]= $value;
                }
            }
        return $data;
    }
   
    function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
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

    
    $res = $link->query("SELECT userId, userName, roleId FROM users ORDER BY userId ASC"); 
    if (!$res) //SQL
    {
        echo "Не удалось выполнить запрос: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    else
    {
        while ($row = $res->fetch_assoc()) 
        {
            $message["users"][]=[
                "userId"=>$row['userId'],
                "userName"=>$row['userName'],
                "roleId"=>$row['roleId']


            ];

        }
    }   


   $url=isset($_GET['q']) ? $_GET['q'] : '';
   $url=rtrim($url,'/');
   $urlList=explode('/',$url);

  // echo json_encode($urlList);

   $router=$urlList[0];
   $requestData=getData(getMethod());
   
    if(file_exists(realpath(dirname(__FILE__)).'/routers/' . $router . '.php'))
    {
        include_once 'routers/' . $router . '.php'; 
        route($method, $urlList, $requestData);
    }
    else{
        echo "NOPE 404";
    }
   

?>