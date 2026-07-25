<?php

namespace App\Core\Debug;

class Dumper
{
    public function dump($value)
    {
        if (class_exists(\Symfony\Component\VarDumper\VarDumper::class)) {
            \Symfony\Component\VarDumper\VarDumper::dump($value);
        } else {
            var_dump($value);
        }
    }
}
