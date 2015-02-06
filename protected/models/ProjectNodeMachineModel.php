<?php

/**
 * This is the model class for table "project_node_machine_table".
 *
 * The followings are the available columns in table 'project_node_machine_table':
 * @property integer $id
 * @property integer $project_id
 * @property string $node_name
 * @property string $machine_name
 * @property integer $priority
 *
 * The followings are the available model relations:
 * @property NodeTable $nodeName
 * @property ProjectTable $project
 */
class ProjectNodeMachineModel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectNodeMachineModel the static model class
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
		return 'project_node_machine_table';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('node_name, machine_name', 'required'),
			array('project_id, priority', 'numerical', 'integerOnly'=>true),
			array('node_name', 'length', 'max'=>128),
			array('machine_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_id, node_name, machine_name, priority', 'safe', 'on'=>'search'),
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
			'nodeName' => array(self::BELONGS_TO, 'NodeTable', 'node_name'),
			'project' => array(self::BELONGS_TO, 'ProjectTable', 'project_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'project_id' => 'Project',
			'node_name' => 'Node Name',
			'machine_name' => 'Machine Name',
			'priority' => 'Priority',
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
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('node_name',$this->node_name,true);
		$criteria->compare('machine_name',$this->machine_name,true);
		$criteria->compare('priority',$this->priority);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}