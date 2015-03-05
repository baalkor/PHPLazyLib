<?php
interface IConfigurationFile
{
    public function open($file);
    
    public function getSection($section);
    public function set($section, $key, $param);
    public function get($section, $param);
    public function addSection($section);
    public function removeSection($section);
    public function writeConfig($overwrite=true);
    public function close();
}
?>
