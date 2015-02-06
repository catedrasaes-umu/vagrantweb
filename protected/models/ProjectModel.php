<?php

/**
 * This is the model class for table "project_table".
 *
 * The followings are the available columns in table 'project_table':
 * @property integer $id
 * @property string $project_name
 *
 * The followings are the available model relations:
 * @property ProjectNodeMachineTable[] $projectNodeMachineTables
 */
class ProjectModel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectModel the static model class
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
		return 'project_table';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_name', 'required'),
			array('project_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_name', 'safe', 'on'=>'search'),
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
			'machines' => array(self::HAS_MANY, 'ProjectNodeMachineModel', 'project_id','order'=>'priority ASC'),
			'launcher' => array(self::HAS_MANY, 'Launcher', 'project_id'),
			'operations' => array(self::HAS_MANY, 'ProjectPendingOperations', 'project_id'),
			// 'users' => array(self::MANY_TO_MANY, 'ProjectUserModel', 'project_id'),
			'users' => array(self::MANY_MANY, 'User', 'project_user_table(user_id,project_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'project_name' => 'Group Name',
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
		$criteria->compare('project_name',$this->project_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	public function searchAllowed()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		
		if (Yii::app()->user->isSuperUser)		
		{	
			$criteria->compare('id',$this->id);
			$criteria->compare('project_name',$this->project_name,true);
		}else{
			$criteria-> select = "id,project_name ";
			$criteria-> join = " LEFT JOIN project_user_table p ON p.project_id = t.id ";

			
			$criteria->condition=" p.user_id=:user_id ";
			
			$criteria->params = array(
								':user_id' => Yii::app()->user->id,							
								); 
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	
						
		



}