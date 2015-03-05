<?php
    /**
     * Mini class for managing $_FILE
     * ------------------------------
     * @method boolean isUploaded tells if the file was uploaded
     * @method string name get the original filename
     * @method string type get the filetype
     * @method int size get the filesize
     * @method string read return the content 
     */
    class SessionFile
    {
        static function isUploaded()
        {
            if ( isset($_FILES))
            {
                return $_FILES["file"]["error"] === 0;
            }
            else
                return false;
        }
        
        static function name()
        {
            return $_FILES["file"]["name"];
        }
        
        
        static function type()
        {
            return $_FILES["file"]["type"];
        }
        
        static function size()
        {
            return $_FILES["file"]["size"];
        }
        static function tmp_name()
        {
            return $_FILES["file"]["tmp_name"];
        }
        
        static function error_code()
        {
            return $_FILES["file"]["error"];
        }
        
        static function saveFile($destination)
        {
            return move_uploaded_file($_FILES["file"]["tmp_name"], $destination);
        }
        
        static function read()
        {
            return file_get_contents($_FILES["file"]["tmp_name"]);
        }
        
        static function remove()
        {
            return unlink(SessionFile::tmp_name());
        }
        
    }
?>
