<?php
    require_once '../../lib/configuration/INIConfigurationFile.php';
    function testConfiguationParser()
    {
        $a = new INIConfigurationFile("config.ini");
        $a->addSection("TEST");
        
        
        
        
        $a->set("TEST", "abc", "true");
        $a->writeConfig(false);
        var_dump($a->get("TYPED_TEST", "int"));
    }
    testConfiguationParser();
?>
