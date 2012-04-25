<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LDAPSearcher
 *
 * @author Brad
 */
require_once 'User.php';

class LDAPSearcher {
    //put your code here
    protected $conn;
    public function getUser($username){
        //code here
        $user = new User();
        $user->setUserName($username);
        $user->setUserType(0);
        return $user;
    }
    
    public function isAdmin($username, $password){
        $this->conn = ldap_connect("ad.acm.cs");
        if ($this->conn){
            $bind = ldap_bind($this->conn, $username, $password);
            if (!$bind){
                return 1;
            }else{
                return 1;
            }
        }
    }
    
}

?>
