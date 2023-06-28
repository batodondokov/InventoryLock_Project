<?php

include 'init.php';

use App\Models\Container;
use App\Models\Auth;

if ($_SERVER['HTTP_AUTHORIZATION']){
    if (Auth::checkToken($_SERVER['HTTP_AUTHORIZATION'])){
        if ($_GET){
            if (!empty($_GET['container_id'])){
                $response = Container::getContainerInfo($_GET['container_id']);
                echo json_encode($response);
            }
            else if(!empty($_GET['load_all'])){
                if($_GET['load_all'] = 1){
                    $containers = Container::loadAll();
                    $response['containers'] = $containers;
                    echo json_encode($response);
                }
            }
            else {
                $response['errore_message'] = "Неверный запрос";
                echo json_encode($response);
            }
        }
    }
    else{
        $response['errore_message'] = "Неверный токен";
        echo json_encode($response);
    }
}
else{
    $response['errore_message'] = "Токен отсутствует";
    echo json_encode($response);
}
