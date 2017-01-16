<?php

class My_Util
{

    /**
     * @param string $path
     * @return bool
     */
    public static function deleteDir($path)
    {
        $function = array(__CLASS__, __FUNCTION__);

        return (
            is_file($path)
            ? @unlink($path)
            : array_map($function, glob($path . '/*')) == @rmdir($path)
        );
    }

}
