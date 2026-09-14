<?php

// Conectar com o banco de dados
try {
    $db = new DbManagerActions($DB_USER, $DB_PASSWORD, $DB_HOST, $DB_NAME);

    $GLOBALS['db'] = $db;
} catch (exception $erro) {
    echo "Erro na conexão: " . $erro->getMessage();
}