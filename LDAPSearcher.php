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
        //check to see if username is in LDAP group.
        //if so, create user object. if not, return 0
        $user = new User();
        $user->setUserName($username);
        $user->setEmail($email);
        $user->setUserType(0);
        return $user;
    }
    
    public function isAdmin($username, $password){
        $this->conn = ldap_connect("ad.acm.cs");
        if ($this->conn){
            $bind = ldap_bind($this->conn, $username, $password);
            if (!$bind){
                return 1; //change to 0
            }else{ //check to see if this person is in the admin group. if so, return 1, otherwise 0.
                return 1;
            }
        }
    }
    
}

?>
