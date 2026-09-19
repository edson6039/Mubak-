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

function insert($table, $data) {
    $db = $GLOBALS['db'];

    $insertQuery = "";

    foreach ($data as $key => $value) {
        $insertQuery .= "$key = ";
        if (is_string($value)) {
            $insertQuery .= "'" . $value . "', ";
        } elseif (is_null($value)) {
            $insertQuery .= "NULL, ";
        } else {
            $insertQuery .= "$value, ";
        }
    }

    $insertQuery = rtrim($insertQuery, ", ");

    return $insertQuery;

    $insert = $db->insert(
        table: $table,
        data: $data
    );

    if ($db->getError()) {
        echo "Erro na inserção: " . $db->getError();
        return false;
    }

    return true;
}