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
				$user->setEmail($entries[$i][LDAPUserMailAttribute]);
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
			$this->conn = ldap_connect(LDAPHost) or die("Could not contact LDAP server");
		}
		if(ldap_bind($this->conn, $username.'@'.LDAPDomain, $password))
		{

			$result = ldap_search($this->conn, LDAPBaseDN, "(".LDAPUserAccountAttribute."=$username)", array(LDAPUserAccountAttribute, LDAPUserMailAttribute));

			if($result == false)
			{
				ldap_unbind($this->conn);
				return 0;
			}

			$entries = ldap_get_entries($this->conn, $result);
			//var_dump($entries);
			if($entries['count'] <= 0)
			{
				return 0;
			}
			//loop through results and manually check account  name attribute returned
			//this is done because samaccountname in AD does not do a case sensity search and might mess up
			for($i = 0; $i < $entries['count']; $i++)
			{
				if($username == $entries[$i][LDAPUserAccountAttribute]['0'])
				{
					$userDN = $entries[$i]['dn'];
					$groupResult = ldap_read($this->conn, $userDN, "(memberof=".LDAPUserAdminGroup.")", array("members"));
					
					if($groupResult == false)
					{
						printf("Group results false\n");
						return 0;
					}
					
					$groupEntries = ldap_get_entries($this->conn, $groupResult);
					if($groupEntries['count'] > 0)
					{
						return 1;
					}
				}
			}
		}
		else
		{
			//login failure
			return -1;
		}
		return 0;
	}

}
?>
