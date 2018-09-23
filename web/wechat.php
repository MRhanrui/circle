<?php
$class_name = $_GET['c'];
include '../controller/wechat/' . $class_name . '.php';
$method_name = $_GET['m'];
$o = new $class_name();
$o->$method_name();