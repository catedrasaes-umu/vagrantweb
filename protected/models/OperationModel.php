<?php

/**
 * This is the model class for table "operation_table".
 *
 * The followings are the available columns in table 'operation_table':
 * @property integer $id
 * @property string $operation_command
 * @property string $node_name
 * @property integer $operation_status
 * @property string $operation_result
 */
class OperationModel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OperationModel the static model class
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
		return 'operation_table';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('node_name, operation_status, operation_result', 'required'),
			array('operation_status', 'numerical', 'integerOnly'=>true),
			array('operation_command, operation_specific', 'length', 'max'=>255),
			array('node_name', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, operation_command,operation_specific, node_name, operation_status, operation_result', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'operation_command' => 'Operation Command',
			'operation_specific' => 'Operation Specific',
			'node_name' => 'Node Name',
			'operation_status' => 'Operation Status',
			'operation_result' => 'Operation Result',
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
		$criteria->compare('operation_command',$this->operation_command,true);
		$criteria->compare('operation_specific',$this->operation_specific,true);
		$criteria->compare('node_name',$this->node_name,true);
		$criteria->compare('operation_status',$this->operation_status);
		$criteria->compare('operation_result',$this->operation_result,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}