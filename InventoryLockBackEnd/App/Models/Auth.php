<?php

namespace App\Models;

use App\Database\Database;

class Auth{
    public ?string $user_email = '';
    public ?string $user_password = '';
    public ?string $token = '';

    protected $error = '';

    public function __construct(array $data=[]){      
        $this->fill($data);
    }

    public function fill(array $data = []){
        if ($data){        
            $this->user_email = $data['user_email'] ?? '';
            $this->user_password = $data['user_password'] ?? '';
        } 
    }

    public function validate() : bool{

        $this -> error = '';
        //проверка на заполненности полей   
        if (!$this -> user_password || !$this -> user_email){
            $this -> error='Поля не заполнены';
        }
        if ($this -> user_email && !preg_match('~^[a-z\d\-_\.]+@[a-z\d\-]+\.[a-z]{2,65}$~',$this -> user_email)){
            //Регулярка email
            $this -> error='Введен некоретный электронный адрес';
        }
        if ($this ->checkUser($this -> user_password, $this -> user_email) != 1){
            $this -> error='Неверные данные';
        }
        return empty($this->error);
    }

    public function checkUser($password, $email){
        $result = Database::query("
        SELECT COUNT(*) 
        FROM `users`
        WHERE `user_email` = '" . $email . "' AND `user_password` = '" . $password . "';");
        return $result['COUNT(*)'];
    }

    public function hasError() : bool{
        return ! empty($this->error);
    }
    
    public function getError(): string{
        return $this->error; 
    }

    public function save(): string{
        $this->token = uniqid();

        $sql = Database::prepare('INSERT INTO `authentications` (`user_email`, `token`, `authentificated_at`) 
        VALUES (:user_email, :token, NOW());');
        $sql->execute([
            'user_email' => $this->user_email,
            'token' => $this->token,
        ]);
        return $this->token;
    }

    public static function checkToken($token): bool{
        $result = Database::query("
        SELECT COUNT(*) 
        FROM `authentications`
        WHERE `token` = '" . $token . "';");
        if ($result['COUNT(*)'] == 1){
            return true;
        }else{
            return false;
        }
    }
}