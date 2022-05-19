<?php

namespace Damon\migrations;
use Damon\migrations\Conf;
final class DB
{
    private static $_instance = NULL;
    public static $statments = [];
    public static $unique = [];
    public static $index = [];
    public static $primary_key = '';
    private function __construct()
    {

    }
    public function __clone(){
        die('Clone is not allowed.' . E_USER_ERROR);
    }
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new  \mysqli(
                self::readConf('host'),
                self::readConf('username'),
                self::readConf('password'),
                self::readConf('db'),
                self::readConf('port')
            );
        }

        return self::$_instance;
    }
    public static function excute($table,$engine,$charset,$operate,$objects)
    {
        try{
            foreach ($objects as $value) {
                if (in_array($value->type,['integer','tinyInteger'])) {
                    self::$statments[] = self::getDDLByint($value);
                } else if (in_array($value->type,['string','text'])) {
                    self::$statments[] = self::getDDLByStr($value);
                } else if (in_array($value->type,['timestamp'])) {
                    self::$statments[] = self::getDDLByTimes($value);
                } else if (in_array($value->type,['float','double','decimal'])){
                    self::$statments[] = self::getDDLByFloat($value);
                }
            }


            if (self::$statments) {
                if (self::$primary_key) {
                    array_push (self::$statments,self::$primary_key);
                }
                if (self::$unique) {
                    foreach (self::$unique as $value) {
                        array_push (self::$statments,$value);
                    }
                }
                if (self::$index) {
                    foreach (self::$index as $value) {
                        array_push (self::$statments,$value);
                    }
                }
//                var_dump(self::$statments);die;
                if ($operate == 'create') {
                    self::$statments = implode(',',self::$statments);
                    $sql = "CREATE TABLE `{$table}` (
                    ".self::$statments."
                    )ENGINE={$engine} DEFAULT CHARSET={$charset}";
                    $res = self::$_instance->query($sql);
                    echo "执行结果：".($res > 0 ? '执行成功' : '执行失败').PHP_EOL;
                    echo "执行的SQL：".$sql;
                } else if ($operate == 'modify') {
                    foreach (self::$statments as $value) {
                        $sql = 'ALTER TABLE '.$table.$value;
                        $res = self::$_instance->query($sql);
                        echo "执行结果：".($res > 0 ? '执行成功' : '执行失败').PHP_EOL;
                        echo "执行的SQL：".$sql;
                    }
                }
                //关闭链接
                self::$_instance->close();
                echo " NOT EXCUTE....".PHP_EOL;
            }
        }catch (Exception $e) {
            echo "执行错误：".$e->getMessage();
        }


    }


    public static function getDDLByint($value)
    {
//        `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
        if (isset($value->change) && $value->change) {
            $str = " MODIFY COLUMN {$value->name}";
        } else  {
            $str = "`{$value->name}`";
        }

        if ($value->type == 'tinyInteger') {
            $str .= " tinyint(4)";
        } else if ($value->type == 'integer') {
            $str .= " int(11)";
        } else {
            throw new \Exception('type error');
        }

        if ($value->unsigned) {
            $str .= " unsigned";
        }

        if (isset($value->nullable)) {
            $str .= " NULL";
        } else{
            $str .= " NOT NULL";
        }

        if ($value->autoIncrement) {
            $str .= " AUTO_INCREMENT";
        }

        if (isset($value->default)) {
            $str .= " DEFAULT {$value->default}";
        }

        if (isset($value->comment)){
            $str .= " COMMENT '{$value->comment}'";
        }

        if (isset($value->primary)) {
            self::$primary_key = " PRIMARY KEY (`{$value->primary}`)";
        }
        if (isset($value->unique)) {
            if (! is_array($value->unique)) $value->unique = (array) $value->unique;
            self::$unique[] = " UNIQUE INDEX ".implode('_',$value->unique)." (".implode(',',$value->unique).")";
        }
        if (isset($value->index)) {
            if (! is_array($value->index)) $value->index = (array) $value->index;
            self::$index[] = " INDEX ".implode('_',$value->index)." (".implode(',',$value->index).")";
        }
        return $str;
    }


    public static function getDDLByStr($value) {
        if (isset($value->change) && $value->change) {
            $str = " MODIFY COLUMN {$value->name}";
        } else {
            $str = "`{$value->name}`";
        }
        if ($value->type == 'text') {
            $str .= " text";
        } else if ($value->type == 'string') {
            $str .= " varchar({$value->length})";
        } else {
            throw new \Exception("type error");
        }

        if (isset($value->nullable)) {
            $str .= " NULL";
        } else {
            $str .= " NOT NULL";
        }

        if (isset($value->default)) {
            $str .= " DEFAULT {$value->default}";
        }

        if (isset($value->comment)) {
            $str .= " COMMENT '{$value->comment}'";
        }
        if (isset($value->unique)) {
            if (! is_array($value->unique)) $value->unique = (array) $value->unique;
            self::$unique[] = " UNIQUE INDEX ".implode('_',$value->unique)." (".implode(',',$value->unique).")";
        }
        if (isset($value->index)) {
            if (! is_array($value->index)) $value->index = (array) $value->index;
            self::$index[] = " INDEX ".implode('_',$value->index)." (".implode(',',$value->index).")";
        }
        return $str;

    }

    public static function getDDLByTimes($value) {
        if (isset($value->change) && $value->change) {
            $str = " MODIFY COLUMN {$value->name} {$value->timestamp} ";
        } else {
            $str = "`{$value->name}` {$value->timestamp} ";
        }
        if (isset($value->nullable)) {
            $str .= " NULL";
        } else {
            $str .= " NOT NULL";
        }

        if (isset($value->default)) {
            $str .= " DEFAULT {$value->default}";
        }

        //自动更新
        $str .= " ON UPDATE CURRENT_TIMESTAMP";

        if (isset($value->comment)) {
            $str .= " COMMENT '{$value->comment}'";
        }
        if (isset($value->unique)) {
            if (! is_array($value->unique)) $value->unique =(array) $value->unique;
            self::$unique[] = " UNIQUE INDEX ".implode('_',$value->unique)." (".implode(',',$value->unique).")";
        }
        if (isset($value->index)) {
            if (! is_array($value->index)) $value->index = (array) $value->index;
            self::$index[] = " INDEX ".implode('_',$value->index)." (".implode(',',$value->index).")";
        }
        return $str;
    }


    public static function getDDLByFloat($value) {
        if (isset($value->change) && $value->change) {
            $str = " MODIFY COLUMN {$value->name} {$value->type}({$value->total},{$value->places})";
        } else {
            $str = "`{$value->name}` {$value->type}({$value->total},{$value->places})";
        }
        if (isset($value->nullable)){
            $str .= " NULL";
        } else {
            $str .= " NOT NULL";
        }

        if (isset($value->default)) {
            $str .= " DEFAULT {$value->default}";
        }

        if (isset($value->comment)) {
            $str .= " COMMENT '{$value->comment}'";
        }
        if (isset($value->unique)) {
            if (! is_array($value->unique)) $value->unique = (array) ($value->unique);
            self::$unique[] = " UNIQUE INDEX ".implode('_',$value->unique)." (".implode(',',$value->unique).")";
        }
        if (isset($value->index)) {
            if (! is_array($value->index)) $value->index = (array) ($value->index);
            self::$index[] = " INDEX ".implode('_',$value->index)." (".implode(',',$value->index).")";
        }
        return $str;

    }

    public static function readConf($key)
    {
        $conf = new Conf('pro');
        $conf_arr = $conf->getConf();
        return $conf_arr[$key] ?? '';
    }
}
