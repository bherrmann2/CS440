<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Brad
 */
class User {
    protected $username;
    protected $usertype;
    protected $email;
    
    public function getUserName(){
        return $this->username;
    }
    
    public function setUserName($newname){
        $this->username = $newname;
        return 1;
    }
    
    public function getUserType(){
        return $this->usertype;
    }
    
    public function setUserType($type){
        $this->usertype = $type;
        return 1;
    }
    
    public function setEmail($email){
        $this->email = $email;
    }
    
    public function getEmail(){
        return $this->email;
    }
}

?>
