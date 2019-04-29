<?php
include "./mysql.php";

$oMySql = new MySQL();
$oMySql->execStrQueryPDO($oMySql->sqlTabla);