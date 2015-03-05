<?php
interface IConfigurationFile
{
    public function open($file);
    
    public function getSection(Section $section);
    public function set(Section $section, Parameter $param);
    public function get(Section $section, Parameter $param);
    public function addSection(Section $section);
    public function removeSection(Section $section);
    
    public function close();
}
?>
