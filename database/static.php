<?php
function mysqlType($var, $typeNo)
{
    
    switch ($typeNo )
    {

            case 1:
                    return strval($var);
            case 9:
            case 5:
            case 8:
            case 2:
            case 3:
                    return intval($var);
            case 246:
            case 4:
            case 0:
                    return floatval($var);
            case 10:
            case 11:
            case 12:
                    return DateTime::createFromFormat('Y-m-d',$var);
            case 13:
                    return DateTime::createFromFormat('Y',$var);
            case 6:
                    return NULL;
            case 249:
            case 250:
            case 251:
                return base64_encode($var);
            default:
                    return utf8_encode($var);
    }
}
function is_date($value, $format = 'd/m/Y')
{
    # Par Frédéric FAYS, www.blue-invoice.com source:http://blue-invoice.com/wp/?p=91
    $format=strtolower($format);
    if(strlen($value)>7 && strlen($format)==5){
            # Trouver le séparateur
            $sep = str_replace(array('m','d','y'),'', $format);
            if(strlen($sep)==2 && $sep[0]==$sep[1]){
                    # création du regexp
                    $regexp = str_replace('m','[0-1]?[0-9]', $format);
                    $regexp = str_replace('d','[0-3]?[0-9]', $regexp);
                    $regexp = str_replace('y','[0-9]{4}', $regexp);
                    $regexp = str_replace(']'.$sep[0].'[', ']\\' . $sep[0].'[', $regexp);
                    if(preg_match('#'.$regexp.'#', $value)){
                            # Trouver les éléments de la date
                            $fmd=str_replace($sep[0],'',$format);
                            $DtExplode=explode($sep[0],$value);
                            # Tester la date
                           $d = $DtExplode[strpos($fmd,'d')];
                           $m = $DtExplode[strpos($fmd,'m')];
                           $y = $DtExplode[strpos($fmd,'y')];
                           if(@checkdate($m, $d, $y)) return true;
                    }
            }
    }
    return false;
}

function is_assoc($var)
{
        return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
}

?>
