<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../globals.php';

    $nome_produto = $_POST['nome_produto'];
    $descricao_produto = $_POST['descricao_produto'];
    $imagem_produto = $_FILES['imagem_produto'];

    // Processar o upload da imagem
    if ($imagem_produto['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $upload_file = $upload_dir . basename($imagem_produto['name']);

        if (move_uploaded_file($imagem_produto['tmp_name'], $upload_file)) {
            // Inserir os dados do produto no banco de dados
            $produto_data = [
                'nome_produto' => $nome_produto,
                'descricao_produto' => $descricao_produto,
                'imagem_produto' => 'uploads/' . basename($imagem_produto['name']),
                'ativo_produto' => 1 // Definindo como ativo por padrão
            ];

            $query = insert('produto', $produto_data);

            var_dump($query); // Exibir a consulta SQL gerada
            die();


            // Redirecionar para a página de listagem de produtos
            header('Location: index.php');
            exit();
        } else {
            echo "Erro ao fazer upload da imagem.";
        }
    } else {
        echo "Erro no envio da imagem.";
    }
}