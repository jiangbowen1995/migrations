<?php

namespace Damon\migrations;
use Damon\migrations\Conf;
class Base
{
    public $table;
    public $engine;
    public $charset;
    public $columns;
    public function __construct($table_name,$engine,$charset,$operate)
    {
        $this->table = $table_name;
        $this->engine = $engine;
        $this->charset = $charset;
        $this->operate = $operate;
    }

    public function string($column, $length = 255)
    {
        return $this->addColumn('string', $column, compact('length'));
    }

    public function timestamp($column)
    {
        return $this->addColumn('timestamp', $column);
    }

    public function timestampTz($column)
    {
        return $this->addColumn('timestampTz', $column);
    }

    public function primary($columns, $name = null)
    {
        return $this->indexCommand('primary', $columns, $name);
    }

    public function unique($columns, $name = null)
    {
        return $this->indexCommand('unique', $columns, $name);
    }


    public function index($columns, $name = null)
    {
        return $this->indexCommand('index', $columns, $name);
    }

    public function foreign($columns, $name = null)
    {
        return $this->indexCommand('foreign', $columns, $name);
    }

    public function increments($column)
    {
        return $this->unsignedInteger($column, true);
    }

    public function unsignedInteger($column, $autoIncrement = false)
    {
        return $this->integer($column, $autoIncrement, true);
    }
    public function integer($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn('integer', $column, compact('autoIncrement', 'unsigned'));
    }
    protected function indexCommand($type, $columns, $index)
    {
        $columns = (array) $columns;
        if (is_null($index)) {
            $index = $this->createIndexName($type, $columns);
        }

        return $this->addCommand($type, compact('index', 'columns'));
    }

    protected function createIndexName($type, array $columns)
    {
        $index = strtolower($this->table.'_'.implode('_', $columns).'_'.$type);

        return str_replace(['-', '.'], '_', $index);
    }

    protected function addCommand($name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

    protected function createCommand($name, array $parameters = [])
    {
        return new Fluent(array_merge(compact('name'), $parameters));
    }

//    public function nullable($value)
//    {
//        return $this->addColumn('nullable', $value);
//    }
//
//    public function comment($value)
//    {
//        return $this->addColumn('comment', $value);
//    }
//
//    public function default($value)
//    {
//        return $this->addColumn('default', $value);
//    }

    public function addColumn($type, $name, array $parameters = [])
    {
        $attributes = array_merge(compact('type', 'name'), $parameters);
        $this->columns[] = $column = new Fluent($attributes);
        return $column;
    }
    public function tinyInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn('tinyInteger', $column, compact('autoIncrement', 'unsigned'));
    }

    public function double($column, $total = null, $places = null)
    {
        return $this->addColumn('double', $column, compact('total', 'places'));
    }

    public function float($column, $total = 8, $places = 2)
    {
        return $this->addColumn('float', $column, compact('total', 'places'));
    }

    public function decimal($column, $total = 8, $places = 2)
    {
        return $this->addColumn('decimal', $column, compact('total', 'places'));
    }

    public function text($column)
    {
        return $this->addColumn('text', $column);
    }

//    public function change()
//    {
//        return $this->adaaadColumn('is_change',1);
//    }

    public function excute()
    {
        DB::getInstance();
        DB::excute($this->table,$this->engine,$this->charset,$this->operate,$this->columns);
    }


    public function readConf($key)
    {
        $conf = new Conf('pro');
        $config_arr = $conf->getConf();
        return $config_arr[$key] ?? '';
    }
}

