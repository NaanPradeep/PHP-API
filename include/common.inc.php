<?php
	require '../config/config.php';
	
    // Logger Config Begins
    $log = "";

    Logger::configure(''.$CONFIG_XML_PATH);

    $LOG = Logger::getLogger('app_logger');
    // Logger Config Ends

    // Messaging system begins
    $msg = "";
    // Messaging system ends

    include (MAIN_PATH.'include/i18n.inc.php');
?> 