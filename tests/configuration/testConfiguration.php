<?php
    require_once '../../configuration/INIConfigurationFile.php';
    function testConfiguationParser()
    {
        $a = new INIConfigurationFile("config.ini");
        $a->addSection("TEST");
        $a->set("TEST", "abc", "true");
        $a->writeConfig(false);
        print_r ( $a->getConfiguration() );
    }
    testConfiguationParser();
?>
