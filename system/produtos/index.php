<?php
require_once '../globals.php';
require_once '../../includes/header.php';

$produtos = search('produto', complement: 
    'WHERE ativo_produto = 1 ORDER BY nome_produto'
);
?>

<div class="product-container">
    <h2>Produtos</h2>
<?php foreach ($produtos as $produto) { ?>
    <div class="product-card" onclick="window.location.href='show.php?id=<?php echo $produto['id_produto']; ?>'">
        <h3><?php echo $produto['nome_produto']; ?></h3>
        <p><?php echo $produto['descricao_produto']; ?></p>
        <img src="<?php echo $produto['imagem_produto']; ?>" alt="<?php echo $produto['nome_produto']; ?>">
    </div>
<?php } ?>
</div>


<?php
require_once '../../includes/footer.php';
