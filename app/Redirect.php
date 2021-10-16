<?php

namespace App;

class Redirect
{
    public static function url($path)
    {
        header("Location: $path");
        exit;
    }
}