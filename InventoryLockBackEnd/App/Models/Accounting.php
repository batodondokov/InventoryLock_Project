<?php

namespace App\Models;

use App\Database\Database;

class Accounting{
    public ?int $container_id;
    public ?int $auth_id;
    public ?string $token = '';
    public ?int $status;

    protected $error = '';
    
    public function __construct(array $data=[]){      
        $this->fill($data);
    }

    public function fill(array $data = []){
        if ($data){        
            $this->container_id = $data['container_id'] ?? 0;
            $this->token = $data['token'] ?? '';
            $this->status = $data['status'] ?? 0;
        } 
    }

    public function validate() : bool{  
        if (!$this -> container_id || !$this -> token){
            $this -> error='Неверный запрос';
        }
        return ! $this->hasError();
    }

    
    public function hasError() : bool{
        return ! empty($this->error);
    }
    
    public function getError(): string{
        return $this->error; 
    }

    public function getAuthId($token): int{
        $result = Database::query("
        SELECT `id`
        FROM `authentications`
        WHERE `token` = '" . $token . "';");
        return $result['id'];
    }

    public function save(){
        $sql = Database::prepare('
            INSERT INTO `accounting_records` (`container_id`, `authentication_id`, `status`, `recorded_at`) 
            VALUES (:container_id, :authentication_id, :status, NOW());');
                $sql->execute([
                    'container_id' => $this->container_id,
                    'authentication_id' => $this->getAuthId($this->token),
                    'status' => $this->status,
                ]);
    }
}