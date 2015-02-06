<?php

/**
 * This is the model class for table "Users".
 *
 * The followings are the available columns in table 'Users':
 * @property integer $id
 * @property string $username
 * @property string $password 
 * @property string $email
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username','unique', 'message'=>'This username already exists.'),
			array('username, password', 'required'),
			array('username, password, email', 'length', 'max'=>128),			
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			 // 'projects' => array(self::MANY_MANY, 'ProjectUserModel', 'user_id'),
			'projects' => array(self::MANY_MANY, 'ProjectModel', 'project_user_table(user_id,project_id)'),
			'machines' => array(self::HAS_MANY, 'AssignedVM', 'user_id'),						
			//'machines' => array(self::MANY_MANY, 'ProjectModel', 'project_user_table(user_id,project_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(
)	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',			
			'email' => 'Email',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);		
		$criteria->compare('email',$this->email,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function searchprojects($project)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

			

		$criteria = new CDbCriteria();

		$criteria-> select = "id,username ";
		$criteria-> join = " LEFT JOIN project_user_table p ON p.user_id = t.id ";
		$criteria->condition="p.project_id=:project";
		$criteria->params = array(
							':project' => $project,							
							); 

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function searchfree($project)
	{
		$criteria=new CDbCriteria;

		// select * from users t WHERE id NOT IN (SELECT user_id FROM project_user_table WHERE project_id=1);
		
		$criteria = new CDbCriteria();

		$criteria-> select = "id,username ";
		// $criteria-> join = " LEFT JOIN project_user_table p ON p.user_id = t.id ";

		
		$criteria->condition=" t.id NOT IN ( select user_id FROM project_user_table WHERE project_id=:project)";
		if (empty($project))
		{
			$criteria->condition.=" AND 1=2";
		}
		$criteria->params = array(
							':project' => $project,							
							); 

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function validatePassword($password)
    {   

        // return CPasswordHelper::verifyPassword($password,$this->password);
        return crypt($password,$this->password)===$this->password;
    }
 
    public function hashPassword($password)
    {
        return crypt($password, $this->generateSalt());
    }


    public function findAssociatedVmByProject() {
    	
    	$result=Yii::app()->db->createCommand()->select('p.project_name,pnm.project_id,pnm.node_name,pnm.machine_name')
												  ->from('project_node_machine_table pnm')												  
												  ->leftJoin('project_user_table pu', 'pu.project_id=pnm.project_id')
												  ->leftJoin('project_table p', 'p.id=pnm.project_id')
												  ->where('pu.user_id = :user', array(':user'=>$this->id))
												  ->order('pnm.project_id ASC,pnm.machine_name DESC')
												  ->queryAll();	

		return $result;
    }

    public function findAssociatedVmByProjectDistinct() {
    	$result=Yii::app()->db->createCommand()->selectDistinct('pnm.node_name,pnm.machine_name')
											  ->from('project_node_machine_table pnm')												  
											  ->leftJoin('project_user_table pu', 'pu.project_id=pnm.project_id')
											  ->where('pu.user_id = :user', array(':user'=>$this->id))
											  ->queryAll();		

		return $result;
    }

 	protected function generateSalt($cost=10)
    {
            if(!is_numeric($cost)||$cost<4||$cost>31){
                    throw new CException(Yii::t('Cost parameter must be between 4 and 31.'));
            }
            // Get some pseudo-random data from mt_rand().
            $rand='';
            for($i=0;$i<8;++$i)
                    $rand.=pack('S',mt_rand(0,0xffff));
            // Add the microtime for a little more entropy.
            $rand.=microtime();
            // Mix the bits cryptographically.
            $rand=sha1($rand,true);
            // Form the prefix that specifies hash algorithm type and cost parameter.
            $salt='$2a$'.str_pad((int)$cost,2,'0',STR_PAD_RIGHT).'$';
            // Append the random salt string in the required base64 format.
            $salt.=strtr(substr(base64_encode($rand),0,22),array('+'=>'.'));
            return $salt;
    }


	public function beforeSave() {
		if(parent::beforeSave())
        {
            if($this->isNewRecord)
            { 

                $newPassword = $this->hashPassword($this->password);
                $this->password = $newPassword;
            }
            // else if (!empty($this->password)){
            // 	$newPassword = $this->hashPassword($this->password);
            // 	$this->password = $newPassword;	     
            // }
            return true;
        }
        else
            return false;
		
		 // if (!empty($this->password))
		 // {

		 // 	$newPassword = $this->hashPassword($this->password);
   //          $this->password = $newPassword;	                    
	  //    }

	     return true;
	 }

	 public function afterFind()
    {
        //reset the password to null because we don't want the hash to be shown.
        //$this->initialPassword = $this->password;
        // $this->password = null;
 
        // parent::afterFind();
    }
   
}