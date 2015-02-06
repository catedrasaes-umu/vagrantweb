<?php

/**
 * This is the model class for table "user_virtual_machine_table".
 *
 * The followings are the available columns in table 'user_virtual_machine_table':
 * @property integer $id
 * @property string $node_name
 * @property string $machine_name
 * @property integer $user_id
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property NodeTable $nodeName
 */
class AssignedVM extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AssignedVM the static model class
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
		return 'user_virtual_machine_table';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('node_name, machine_name, user_id', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('node_name', 'length', 'max'=>128),
			array('machine_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, node_name, machine_name, user_id', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'nodeName' => array(self::BELONGS_TO, 'NodeModel', 'node_name'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'node_name' => 'Node Name',
			'machine_name' => 'Machine Name',
			'user_id' => 'User',
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
		$criteria->compare('node_name',$this->node_name,true);
		$criteria->compare('machine_name',$this->machine_name,true);
		$criteria->compare('user_id',$this->user_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}