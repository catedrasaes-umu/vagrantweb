<?php

/**
 * This is the model class for table "node_table".
 *
 * The followings are the available columns in table 'node_table':
 * @property string $node_name
 * @property string $node_address
 * @property integer $node_port
 */
class NodeModel extends CActiveRecord
{	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return NodeModel the static model class
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
		return 'node_table';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('node_name, node_address, node_port, node_password', 'required'),
			array('node_port', 'numerical', 'integerOnly'=>true, 'allowEmpty'=>false),
			array('node_password', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('node_name, node_address, node_port', 'safe', 'on'=>'search'),
			
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
			'node_name' => 'Node Name',
			'node_address' => 'Node Address',
			'node_port' => 'Node Port',
			'node_password' => 'Node Password',											
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

		$criteria->compare('node_name',$this->node_name,true);
		$criteria->compare('node_address',$this->node_address,true);
		$criteria->compare('node_port',$this->node_port);
		

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	
	public function beforeSave()
	{
		$saved = NodeModel::model() -> findByPk($this->node_name);
		
		
		//FIXME TODO encriptar contraseña
		//debug(crypt($this->node_password, $this->generateSalt()));
		
		if (empty($saved) || $saved->node_password!=$this->node_password)		
			$this->node_password=md5($this->node_password);
		
		return true;
	}
	
	
}