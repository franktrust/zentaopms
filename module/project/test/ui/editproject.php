#!/usr/bin/env php
<?php

/**

title=编辑瀑布项目测试
timeout=0
cid=73

- 编辑瀑布项目成功  测试结果 @编辑项目成功

*/
chdir(__DIR__);
include '../lib/editproject.ui.class.php';

$tester = new editProjectTester();
$tester->login();

$project = array(
    array('name' => '编辑项目' . time()),
);

r($tester->editProject($project['0'])) && p('message') && e('编辑项目成功');  //编辑项目名称

$tester->closeBrowser();