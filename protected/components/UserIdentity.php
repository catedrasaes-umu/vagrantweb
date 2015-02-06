<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	// public function authenticate()
	// {
	// 	$users=array(
	// 		// username => password
	// 		'demo'=>'catedrasae$',
	// 		'admin'=>'caputo.2013',
	// 	);
	// 	if(!isset($users[$this->username]))
	// 		$this->errorCode=self::ERROR_USERNAME_INVALID;
	// 	elseif($users[$this->username]!==$this->password)
	// 		$this->errorCode=self::ERROR_PASSWORD_INVALID;
	// 	else		
	// 		$this->errorCode=self::ERROR_NONE;		
	// 	return !$this->errorCode;
	// }
	 private $_id;

        /**
         * Authenticates a user.
         * @return boolean whether authentication succeeds.
         */
        /*public function authenticate()
        {
                $user=User::model()->find('LOWER(username)=?',array(strtolower($this->username)));
                // if($user===null)
                //         $this->errorCode=self::ERROR_USERNAME_INVALID;
                // else if(!$user->validatePassword($this->password))
                //         $this->errorCode=self::ERROR_PASSWORD_INVALID;
                // else
                // {
                        
                        $this->_id=$user->id;
                        $this->username=$user->username;
                        $this->errorCode=self::ERROR_NONE;
                        //var_dump(Rights::getAssignedRoles($this->_id));exit;
                        // $authorizer = Yii::app()->getModule("rights")->getAuthorizer();
                        // $authorizer->authManager->assign('AuthenticatedRole', $this->getId());
                //}
                return $this->errorCode==self::ERROR_NONE;
        }*/

        public function authenticate()
        {
                
                $user=User::model()->find('LOWER(username)=?',array(strtolower($this->username)));
                
                if($user===null)
                        $this->errorCode=self::ERROR_USERNAME_INVALID;
                else if(!$user->validatePassword($this->password))
                        $this->errorCode=self::ERROR_PASSWORD_INVALID;
                else
                {
                        $this->_id=$user->id;
                        $this->username=$user->username;
                        $this->errorCode=self::ERROR_NONE;
                }
                return $this->errorCode==self::ERROR_NONE;
        }


        /**
         * @return integer the ID of the user record
         */
        public function getId()
        {
                return $this->_id;
        }
}
