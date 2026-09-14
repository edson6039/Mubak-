<?php

function search($table, $column = '*', $complement = '', $order = '', $limit = '') {
    $db = $GLOBALS['db'];

    $select = $db->select(
        table: $table,
        columns: $column,
        complement: $complement,
        order: $order,
        limit: $limit
    )->getData();

    if ($db->getError()) {
        echo "Erro na consulta: " . $db->getError();
        return false;
    }

    return $select;
}