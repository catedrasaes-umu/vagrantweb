<?php

Yii::import('ext.ECSVExport');

class MigrationController extends Controller
{
	public $layout='//layouts/column2';

	private function exportNodes()
	{

        $csv = new ECSVExport($provider);
		$content = $csv->toCSV();                   
		Yii::app()->getRequest()->sendFile($filename, $content, "text/csv", false);
		

	}

	public function actionExport()
	{

		// if (Yii::app()->request->isPostRequest)
		// {
		// 	$data = array(
	 //    		array('key1'=>'value1', 'key2'=>'value2'),array('key1'=>'value3', 'key2'=>'value4')        
		// 	);

		// 	$csv = new ECSVExport($data);
		// 	$output = $csv->toCSV(); // returns string by default
			 

		// 	//echo $output;


		// 	//Yii::app()->getRequest()->sendFile("/tmp/test", $output, "text/csv", false);
		// }

		// $models = User::model()->findAll();
	 //    // if ($models){
	 //    // echo CJSON::encode($models);
	 //    // }

	 //    $dataProvider=new CActiveDataProvider('User');
		// $csv = new ECSVExport($dataProvider);	    
		// $content = $csv->toCSV(); 

		//echo $content;

// 		$csv = new ECSVExport($provider);
// $content = $csv->toCSV();                   
		//Yii::app()->getRequest()->sendFile("export", $content, "text/csv", false);

		$name='m'.gmdate('ymd_His').'_'.$name;
		$file=$this->migrationPath.DIRECTORY_SEPARATOR.$name.'.php';

        file_put_contents($file, $content);
            echo "New migration created successfully.\n";
        


		$this->render('export');
	}

	public function actionImport()
	{
		$this->render('import');
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}