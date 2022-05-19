<?php
namespace Damon\migrations;
final class Conf
{
    private $env;
    public function __construct($env)
    {
        $this->env = $env;
    }

    public function getConf()
    {
        if ($this->env == 'dev') {
            //TODO...
        } else {
            //TODO...
        }
        return array(
            'host'=>'127.0.0.1',
            'username'=>'homestead',
            'password'=>'secret',
            'db'=>'homestead',
            'port'=>'3306'
        );
    }
}