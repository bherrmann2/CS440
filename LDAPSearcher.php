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
    }
    
    public function isAdmin($username, $password){
        $this->conn = ldap_connect("ad.acm.cs");
        if ($this->conn){
            $bind = ldap_bind($conn, $username, $password);
            if (!$bind){
                return 0;
            }else{
                return 1;
            }
        }
    }
    
}

?>
