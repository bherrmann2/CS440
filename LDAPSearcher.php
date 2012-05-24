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
	
	protected $conn;

	/*
	 * See if the supplied username exists in the LDAP server
	 * 
	 * returns 0 if not found
	 * returns a User object if successful
	 */
	public function getUser($username)
	{
		//check to see if username is in LDAP group.
		//if(!isset($this->conn))
		//{
		$this->conn = ldap_connect(LDAPHost) or die("Counld not contact LDAP Server!");
	//	}
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

	/*
	 * Checks if the user is an Admin for the Library based on group membership
	 * and checks the users username and password
	 *
	 * returns 1 if the user is an admin
	 * returns 0 if the user is NOT an admin
	 * returns -1 on a login failure
	 */
	public function isAdmin($username, $password){
		//if(!isset($this->conn))
		//{
		$this->conn = ldap_connect(LDAPHost) or die("Could not contact LDAP server");
		//}
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
				ldap_unbind($this->conn);
				return 0;
			}
			//loop through results and manually check account  name attribute returned
			//this is done because samaccountname in AD does not do a case sensity search and might mess up
			for($i = 0; $i < $entries['count']; $i++)
			{
				//User name match
				if($username == $entries[$i][LDAPUserAccountAttribute]['0'])
				{
					//Get user DN string
					$userDN = $entries[$i]['dn'];
					//Read LDAP properties for user DN, filtered for group membership
					$groupResult = ldap_read($this->conn, $userDN, "(memberof=".LDAPUserAdminGroup.")", array("members"));
					//Nothing returned from the read
					if($groupResult == false)
					{
						ldap_unbind($this->conn);
						return 0;
					}
					//Get LDAP entries 
					$groupEntries = ldap_get_entries($this->conn, $groupResult);
					if($groupEntries['count'] > 0)
					{
						ldap_unbind($this->conn);
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
