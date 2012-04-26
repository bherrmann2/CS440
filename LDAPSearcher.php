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
	public function getUser($username)
	{
		//check to see if username is in LDAP group.
		if(!isset($this->conn))
		{
			$this->conn = ldap_connect("ldap://ad.acm.cs") or die("Counld not contact LDAP Server!");
		}
		ldap_bind($this->conn, "apacheacm@acm.cs", "eiT2hoexchiel7Panoh7Eepu") or die("Could not bind to LDAP Server");

		$result = ldap_search($this->conn, "ou=ACMUsers,dc=acm,dc=cs", "(SAMAccountname={$username})", array("mail"));
		
		if($result == false)
		{
			ldap_unbind($this->conn);
			return 0;
		}
		
		$entries = ldap_get_entries($this->conn, $result);

		//if so, create user object. if not, return 0
		$user = new User();
		$user->setUserName($username);
		$user->setEmail($entries[0]['mail']);
		$user->setUserType(0);
		ldap_unbind($this->conn);
		return $user;
	}

	public function isAdmin($username, $password){
		if(!isset($this->conn))
		{
			$this->conn = ldap_connect("ldap://ad.acm.cs") or die("Could not contact LDAP server");
		}
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
//$myLDAP = new LDAPSearcher();
//var_dump($myUser);
?>
