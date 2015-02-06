<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.



$iniconfig=dirname(__FILE__). DIRECTORY_SEPARATOR ."/config.ini";
$varconf = null;

if (file_exists($iniconfig))
	$varconf=parse_ini_file($iniconfig);


//Si no se ha cargado el fichero de configuración
//establecemos algunos parámetros por defecto

//TODO VER cómo poner los valores por defecto
$connectionString = 'mysql:host=localhost;dbname='.(empty($varconf)?"vagrantweb":$varconf["dbname"]);
$username = empty($varconf)?"root":$varconf["user"];
$password = empty($varconf)?"quest2000":$varconf["password"];



Yii::setPathOfAlias('booster', dirname(__FILE__) . DIRECTORY_SEPARATOR . '../components/yiibooster');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Vagrant Web',
	'defaultController' => 'site/controlpanel', 
	//'defaultController' => 'site/proxy', 

	// preloading 'log' component
	'preload'=>array('log','booster'),

	// 'aliases' => array(        
 //        'bootstrap' => realpath(__DIR__ . '/../extensions/yiistrap'), // change this if necessary
 //    ),
	// 'import' => array(      
 //        'bootstrap.helpers.TbHtml',
 //    ),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.modules.*',		
		'application.modules.rights.*', 
		'application.modules.rights.components.*', 
		'application.modules.rights.components.dataproviders.*',
		'application.components.geshi.*',
		'application.extensions.restclient.*',
		'application.extensions.crontab.*',
		
		
	),

	'modules'=>array(
		
		'installer'=>array(),

    	'rights'=>array(
        	'install'=>false, // Remove this line when the module has been installed.
        	'superuserName'=>'AdminRole',
        	'authenticatedName'=>'AuthenticatedRole',
        	// 'enableBizRule'=>true,
        	'enableBizRuleData'=>false,
        	'displayDescription'=>true,
        	'flashSuccessKey'=>'RightsSuccess',
        	'flashErrorKey'=>'RightsError',
        	// 'layout'=>'rights.views.layout.main',
        	'cssFile'=>'/css/rights.css'       	
        	
        	
    	),
		
		'gii'=>array(			
			'class'=>'system.gii.GiiModule',
			'password'=>'caputo.2013',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','155.54.190.227','::1'),
			// 'generatorPaths' => array('bootstrap.gii'),
		),
		
	),



	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'class' => 'RWebUser',
		),
		
		// uncomment the following to enable URLs in path-format
		
		// 'bootstrap' => array(
  //           'class' => 'bootstrap.components.TbApi',   
  //       ),

        'booster' => array(
		    'class' => 'booster.components.Booster',
		    'bootstrapCss' => false,
		    'fontAwesomeCss' => false,
		    'jqueryCss' => false,
		),
		
		'db'=>array(
			// 'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/test.db',
			// 'initSQLs'=>array(
   //              'PRAGMA foreign_keys = true',
   //          ),
			// 'connectionString'=>'mysql:host=localhost;dbname=vagrantweb',
		 //   	'username'=>'root',
		 //   	'password'=>'quest2000',
			'connectionString'=>$connectionString,
		   	'username'=>$username,
		   	'password'=>$password,

		),
		'authManager'=>array(            
            'class'=>'RDbAuthManager',
            'connectionID'=>'db',
        ),
		
		'errorHandler'=>array(		
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CWebLogRoute', //original 'class'=>'CFileLogRoute',
					'levels'=>'trace, error, warning', //eliminar el trace
					'categories'=>'frandeb', //Solo para debug, eliminar despues					
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),*/
				
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'fjsanpedro@gmail.com',
	),
);
