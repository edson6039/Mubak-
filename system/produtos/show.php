<?php
require_once '../globals.php';
require_once '../../includes/header.php';

if (!isset($_GET['id']) && !intval($_GET['id'])) {
    echo '<p>ID do produto não fornecido.</p>';
    require_once '../../includes/footer.php';
    exit;
}

$id_produto = $_GET['id'];

$produto = search('produto', complement: 
    "WHERE id_produto = $id_produto AND ativo_produto = 1"
);

if (!$produto) {
    echo '<p>Produto não encontrado.</p>';
    require_once '../../includes/footer.php';
    exit;
}

?>

<div class="product-detail-container">
    <h1>Detalhes do Produto</h1>
<?php foreach ($produto as $p) { ?>
    <article class="product-detail-card">
        <div class="product-detail-media">
            <img src="<?php echo $p['imagem_produto']; ?>" alt="<?php echo $p['nome_produto']; ?>">
        </div>

        <div class="product-detail-content">
            <h2><?php echo $p['nome_produto']; ?></h2>
            <p><?php echo $p['descricao_produto']; ?></p>

            <div class="product-detail-actions">
                <button type="button" class="product-action-buy">Comprar</button>
                <button type="button" class="product-action-edit" onclick="location.href='edit.php?id=<?php echo $p['id_produto']; ?>'">Editar</button>
                <button type="button" class="product-action-delete" onclick="location.href='delete.php?id=<?php echo $p['id_produto']; ?>'">Excluir</button>
            </div>
        </div>
    </article>
<?php } ?>
</div>



<?php
require_once '../../includes/footer.php';
?>
