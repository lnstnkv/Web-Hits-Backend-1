<?php

include_once 'helpers/helpers.php';
include_once 'helpers/validation.php';

global $Link;


function getData($method)
{
    $data = new stdClass();
    if ($method != "GET") {
        $data->body = json_decode(file_get_contents('php://input'));
    }
    $data->parameters = [];
    $dataGet = $_GET;
    foreach ($dataGet as $key => $value) {
        if ($key != "q") {
            $data->parameters[$key] = $value;
        }
    }
    return $data;
}

function getMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}

header('Content-type:application/json');

$Link = mysqli_connect("127.0.0.1", "backend", "password", "backend");

if (!$Link) {
    /*echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;*/
    setHTTPStatus("500", "DB Connection error: " . mysqli_connect_error());
    exit;
}

$message = [];
$message["users"] = [];

$url = isset($_GET['q']) ? $_GET['q'] : '';
$url = rtrim($url, '/');
$urlList = explode('/', $url);

// echo json_encode($urlList[1]);

$router = $urlList[0];
$requestData = getData(getMethod());
$method = getMethod();

if (file_exists(realpath(dirname(__FILE__)) . '/routers/' . $router . '.php')) {
    include_once 'routers/' . $router . '.php';
    route($method, $urlList, $requestData);
} else {
    echo "NOPE 404";
}
mysqli_close($Link);
return;
// смерть БД  
