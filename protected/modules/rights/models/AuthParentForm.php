<?php

class AuthParentForm extends CFormModel
{
	public $itemname;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('itemname', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'itemname' => Rights::t('core', 'Authorization item'),
		);
	}
}
