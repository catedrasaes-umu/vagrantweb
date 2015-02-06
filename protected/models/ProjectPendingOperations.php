<?php

/**
 * This is the model class for table "project_pending_operations_table".
 *
 * The followings are the available columns in table 'project_pending_operations_table':
 * @property integer $id
 * @property integer $pnm_id
 * @property integer $project_id
 * @property string $command
 * @property integer $status
 * @property string $status_msg
 *
 * The followings are the available model relations:
 * @property ProjectTable $project
 * @property ProjectNodeMachineTable $pnm
 */
class ProjectPendingOperations extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectPendingOperations the static model class
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
		return 'project_pending_operations_table';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pnm_id, project_id', 'required'),
			array('pnm_id, project_id, status', 'numerical', 'integerOnly'=>true),
			array('command, status_msg', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, pnm_id, project_id, command, status, status_msg', 'safe', 'on'=>'search'),
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
			'project' => array(self::BELONGS_TO, 'ProjectTable', 'project_id'),
			'pnm' => array(self::BELONGS_TO, 'ProjectNodeMachineModel', 'pnm_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pnm_id' => 'Pnm',
			'project_id' => 'Project',
			'command' => 'Command',
			'status' => 'Status',
			'status_msg' => 'Status Msg',
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
		$criteria->compare('pnm_id',$this->pnm_id);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('command',$this->command,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('status_msg',$this->status_msg,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}