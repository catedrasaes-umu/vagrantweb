<?php

/**
 * This is the model class for table "cached_data_table".
 *
 * The followings are the available columns in table 'cached_data_table':
 * @property integer $id
 * @property string $node_name
 * @property string $vm_name
 * @property string $provider
 * @property string $status
 * @property string $expiration
 * @property integer $priority
 */
class CachedData extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CachedData the static model class
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
		return 'cached_data_table';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('node_name, vmname, provider, status', 'required'),
			array('node_name, vm_name, provider, status', 'length', 'max'=>128),			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, node_name, vm_name, provider, status', 'safe', 'on'=>'search'),
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
			'node_name' => 'Node Name',
			'vm_name' => 'Vmname',
			'provider' => 'Provider',
			'status' => 'Status',
			
			//'' => 'Created At',
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
		$criteria->compare('vm_name',$this->vm_name,true);
		$criteria->compare('provider',$this->provider,true);
		$criteria->compare('status',$this->status,true);
		
		//$criteria->compare('expiration',$this->expiration,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}