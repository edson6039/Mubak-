<?php
require_once 'system/globals.php';

$produtos = search('produto', complement: '
    WHERE ativo_produto = 1
');

var_dump($produtos);