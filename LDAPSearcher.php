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
require_once 'LDAPSearcherConf.php';

class LDAPSearcher {
	//put your code here
	protected $conn;
	public function getUser($username)
	{
		//check to see if username is in LDAP group.
		if(!isset($this->conn))
		{
			$this->conn = ldap_connect(LDAPHost) or die("Counld not contact LDAP Server!");
		}
		ldap_bind($this->conn, LDAPServiceAccount, LDAPServiceAccountPassword) or die("Could not bind to LDAP Server");

		$result = ldap_search($this->conn, LDAPBaseDN, "(".LDAPUserAccountAttribute."=$username)", array(LDAPUserAccountAttribute, LDAPUserMailAttribute));

		if($result == false)
		{
			ldap_unbind($this->conn);
			return 0;
		}

		$entries = ldap_get_entries($this->conn, $result);
		if($entries['count'] <= 0)
		{
			return 0;
		}
		//loop through results and manually check account  name attribute returned
		//this is done because samaccountname in AD does not do a case sensity search and might mess up
		$user = new User();
		for($i = 0; $i < $entries['count']; $i++)
		{
			if($username == $entries[$i][LDAPUserAccountAttribute]['0'])
			{
				$user->setUserName($username);
				$user->setEmail($entries['0'][LDAPUserMailAttribute]);
				$user->setUserType(0);
				ldap_unbind($this->conn);
				return $user;
			}
		}
		return 0;
	}

	public function isAdmin($username, $password){
		if(!isset($this->conn))
		{
			$this->conn = ldap_connect("ldap://172.29.0.254") or die("Could not contact LDAP server");
		}
		if(ldap_bind($this->conn, $username."@acm.cs", $password))
		{
			$userDN = getDN($this->conn, $username, "ou=ACMUsers,dc=acm,dc=cs");
			$result = ldap_read($this->conn, $userDN, "(memberof={ACMLib})", array('members'));
			if($result == false)
			{
				return 0;
			}
			$entries = ldap_get_entries($this->conn, $result);
			if($entries['count'] > 0)
			{
				return 1;
			}
		}
		else
		{
			return 0;
		}
		return 0;
	}

}
//$myLDAP = new LDAPSearcher();
//$search = "walter";
//$myUser = $myLDAP->getUser($search);
//printf("Admin: %d", $myLDAP->isAdmin("walter", "W4l735_42786322")); 
//if($myUser != false)
//{
//	var_dump($myUser);
//}
//else
//{
//	printf("User %s not found\n", $search);
//}
?>
