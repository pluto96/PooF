<?php
	/****
		*	File: boot.php
		*	Descrizione: File di inizializzazione della configurazione 
		*
		*	Author: D4ng3R <mich.loris@gmail.com>
	*****/	
	
	include _SITE_PATH . '/configuration/' . 'config.php';				// Load global configuration
	include _SITE_PATH . '/app/' . 'system.php';						// Load system class
	include _SITE_PATH . '/app/' . 'language.php';						// Load multilang
	include _SITE_PATH . '/app/' . 'controller.php';					// Load controller class
	include _SITE_PATH . '/app/' . 'registry.php';						// Load registry class
	include _SITE_PATH . '/app/' . 'router.php';						// Load router class
	include _SITE_PATH . '/app/' . 'model.php';							// Load model class
	include _SITE_PATH . '/app/' . 'view.php';							// Load view class
	include _SITE_PATH . '/app/' . 'log.php';							// Load log class
	include _SITE_PATH . '/app/' . 'databaseDriver.php';				// Load database driver
	include _SITE_PATH . '/app/' . 'table.php';							// Load database table class
	
	error_reporting(_ENABLE_ERROR);										// Show error
	session_start();													// Start the sessione
	register_shutdown_function('_shutdown');							// Shutdown function
	
	$_system = system::getInstance(); 									// New system object (Singleton)	
	$_system->loadDatabase();											// Load database
	$_system->loadConfiguration();										// Load configuration
	$_system->registry->loadTime = microtime(true);				    	// Analyze load time
	$_system->setPOSTonRegistry($_POST);								// Save POST data
	$_system->setCOOKIEonRegistry($_COOKIE);							// Save COOKIE data
	$_system->setFILESonRegistry($_FILES);								// Save FILES data
	$_system->loadGlobalObject();										// Load globalOject
	$_system->loadVirtualFunction();									// Load virtualFunction
	$_system->loadLanguage();											// Load multilang
		
	$_system->callEvent("bootEvent", array("idbrowser" => $_SERVER['HTTP_USER_AGENT']));
	
	if(_STATISTICS_ENABLE and  _STATISTICS_ENABLE == true) {
		if(isset($_SESSION[_STATISTICS_LASTVIST_SESSION]) and $_SESSION[_STATISTICS_LASTVIST_SESSION] != "") {
			if((((time() - $_SESSION[_STATISTICS_LASTVIST_SESSION])/60)/60) <= _STATISTICS_LASTVIST_SESSION_VALIDE) {
				$_system->callEvent("timeVisit", array("timeVisit" => (time() - $_SESSION[_STATISTICS_LASTVIST_SESSION])));	
			}
		}
		$_SESSION[_STATISTICS_LASTVIST_SESSION] = time();															
	}
	
	/** 	
		Autoload class
	**/	
	function __autoload($class_name) {	
		$filename = $class_name.'.php';
		if (in_array($class_name, $GLOBALS["_globalObject"])) {
			$file = _GLOBAL_OBJECT_PATH.$filename;
			include ($file);
			return;
		}
		else {
			$file = _MODELS_PATH.$filename;
			if (file_exists($file)) {
				include ($file);
				return;
			}
			
			$file = _GLOBAL_OBJECT_PATH.$filename;
			if (file_exists($file)) {
				include ($file);
				return;
			}			
		}	
		$GLOBALS["_system"]->log->error("Boot: Modello o Gloabal Object non trovato ($file)", __LINE__);
	}	

?>
