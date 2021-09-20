<?php

require '../vendor/autoload.php';


if(isset($user_lang)){
		$locale = $user_lang;

		// Load compatibility layer
		PhpMyAdmin\MoTranslator\Loader::loadFunctions();

		_setlocale(LC_ALL, $locale);
		$domain = 'messages';
		_textdomain($domain);
		_bindtextdomain($domain, '../locales');
		_bind_textdomain_codeset($domain, 'UTF-8');
		$LOG->info("Locale changed to ".$locale);
		
} else {
	$locale = "en";

	PhpMyAdmin\MoTranslator\Loader::loadFunctions();

	_setlocale(LC_ALL, $locale);
	$domain = 'messages';
	_textdomain($domain);
	_bindtextdomain($domain, '../locales');
	_bind_textdomain_codeset($domain, 'UTF-8');
	$LOG->info("Locale changed to ".$locale);
}