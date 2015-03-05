<?php
    require_once '../../configuration/INIConfigurationFile.php';
    function testConfiguationParser()
    {
        $a = new INIConfigurationFile("config.ini");
        $a->addSection("TEST");
        $a->get("TEST", "dd");
        print_r ( $a->getConfiguration() );
    }
    testConfiguationParser();
?>
