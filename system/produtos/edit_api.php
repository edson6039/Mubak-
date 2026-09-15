<?php

require_once '../globals.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$campo = isset($_POST['campo']) ? $_POST['campo'] : '';
$valor = isset($_POST['valor']) ? trim($_POST['valor']) : '';

/*
 * Campos que podem ser editados
 */
$camposPermitidos = array(
    'nome_produto',
    'descricao_produto'
);

if ($id <= 0) {

    echo json_encode(array(
        'success' => false,
        'message' => 'Produto inválido.'
    ));
    exit;
}


if (!in_array($campo, $camposPermitidos)) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Campo não permitido.'
    ));
    exit;
}


/*
 * Atualiza
 */

$db = $GLOBALS['db'];

$db->update(
    table: 'produto',
    values: "$campo = $valor",
    where: [
        'id_produto =' => $id
    ]    
);

if ($db->getError()) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Não foi possível salvar.'
    ));
    exit;
}

echo json_encode(array(
    'success' => true
));