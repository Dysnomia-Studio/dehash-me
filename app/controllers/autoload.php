<?php
/**
 * Autoloading classes when needed
 */
spl_autoload_register(function($class){
	$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

	$paths = array(
		DIR_LIB,
		DIR_MODEL,
		DIR_CTRL,
	);
	
	foreach($paths as $path) {
		$file = join(DIRECTORY_SEPARATOR, [$path, $class.'.php']); // FOLDER/Class.php
		if(file_exists($file)) {
			return require_once $file;
		}

		$file = join(DIRECTORY_SEPARATOR, [$path, $class, $class.'.php']) ; // FOLDER/Class/Class.php
		if(file_exists($file)) {
			return require_once $file;
		}
	}
});