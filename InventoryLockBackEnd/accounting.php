<?php

include 'init.php';

use App\Models\Accounting;
use App\Models\Auth;

if ($_SERVER['HTTP_AUTHORIZATION']){
    if (Auth::checkToken($_SERVER['HTTP_AUTHORIZATION'])){
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        
        $accounting = new Accounting($data);
        if ($accounting->validate()){
            $response['result'] = "validated";
            $accounting->save();
        }else{
            $response['errore_message'] = $accounting->getError();
            echo json_encode($response);
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