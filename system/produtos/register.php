<?php
require_once '../globals.php';
require_once '../../includes/header.php';
?>

<div class="register-product-container column">
    <form action="create.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="nome_produto" placeholder="Nome do Produto" required>

        <textarea name="descricao_produto" placeholder="Descrição do Produto" required></textarea>

        <input type="file" name="imagem_produto" accept="image/*" required>

        <button type="submit">Cadastrar Produto</button>
    </form>
</div>

<?php
require_once '../../includes/footer.php';
