<?php
/**
 * Desc: 用户编写的sql文件
 * 1 在同级目录下migrations编辑conf.php 配置数据库相关信息
 * 2 在当前类的常量属性填写添加的表名,存储引擎类型及字符集
 * 3 一个表一个文件 修改还（有点问题）
 * 3.1 创建和修改不能同时处理，需分开处理
 * 3.2 创建表时 operate 为 create, 修改字段时 operate 为modify
 * 4 编辑好需要的字段之后，cd 到当前目录 执行 php sql_0503.php
 * 5 结果是输出在控制台 若报错不清楚原因复制sql去native运行下
 * 6 执行结果完毕 最好是注释掉当前语句，如果不注释下次会重复执行 会报错 或者新建一个文件编写DDL
 * 7 目前实现的数据类型 int tinyint varchar text timestamp float double decimal
 * 8 可以添加 主键索引 唯一索引 普通索引
 *
 *
 ****/
require_once './migrations/Base.php';
require_once './migrations/DB.php';
require_once './migrations/Fluent.php';
class migrate extends Base
{
    const TABLE_NAME = 'test17';
    const ENGINE = 'myisam';
    const CHARSET = 'utf8';
    const OPERATE = 'create';
    public function __construct()
    {

        parent::__construct(self::TABLE_NAME,self::ENGINE,self::CHARSET,self::OPERATE);
        /******************************************以下区域编写DDL*******************************************************/
        /**
         *需创建的sql如下：
         ***/
        //字段名称为name varchar length为10  可为null 备注用户名称 默认值 123
        $this->string('name',10)->nullable()->comment('用户名称')->default('123');
        //字段名称id int 无符号主键 自增
        $this->increments('id')->primary('id')->comment('自增id');
        $this->tinyInteger('sex')->comment('1男2女');
        $this->float('price1',8,2)->default(0.00)->comment('价格1');
        $this->double('price2',8,2)->default(0.00)->comment('价格2');
        $this->decimal('price3',8,2)->default(0.00)->comment('价格3');
        $this->text('comment')->comment('评论');
        $this->integer('adddate')->comment('添加时间');
        $this->integer('modifydate')->comment('修改时间')->index('modifydate','index_m');
//        $this->index('modifydate','index_m');
//        $this->float('price1', 9,3)->change();
//        $this->string('name', 50)->change();
    }
}

$m = new migrate;
$m->excute();

