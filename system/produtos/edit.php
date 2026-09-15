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
                <img 
                    src="<?php echo $p['imagem_produto']; ?>" 
                    alt="<?php echo $p['nome_produto']; ?>"
                >
            </div>

            <div class="product-detail-content">

                <!-- NOME -->
                <div 
                    class="editable-field"
                    data-id="<?php echo $p['id_produto']; ?>"
                    data-field="nome_produto"
                    data-type="text"
                >
                    <span class="edit-hint">✎ Duplo clique para editar</span>

                    <h2 class="editable-value">
                        <?php echo $p['nome_produto']; ?>
                    </h2>
                </div>


                <!-- DESCRIÇÃO -->
                <div 
                    class="editable-field"
                    data-id="<?php echo $p['id_produto']; ?>"
                    data-field="descricao_produto"
                    data-type="textarea"
                >
                    <span class="edit-hint">✎ Duplo clique para editar</span>

                    <p class="editable-value">
                        <?php echo $p['descricao_produto']; ?>
                    </p>
                </div>

            </div>

        </article>

    <?php } ?>

</div>



<?php
require_once '../../includes/footer.php';
?>

<script>

document.querySelectorAll('.editable-field').forEach(function(field) {

    field.addEventListener('dblclick', function() {

        iniciarEdicao(field);

    });

});


function iniciarEdicao(field) {

    // Já está editando
    if (field.classList.contains('editing')) {
        return;
    }

    field.classList.add('editing');

    var valorAtual = field.querySelector('.editable-value').innerText.trim();

    var tipo = field.getAttribute('data-type');

    var valorOriginal = valorAtual;

    // Remove "R$" do preço
    if (field.getAttribute('data-field') == 'preco_produto') {

        valorAtual = valorAtual
            .replace('R$', '')
            .trim();

    }


    var editor;


    // TEXTAREA
    if (tipo == 'textarea') {

        editor = document.createElement('textarea');

        editor.value = valorAtual;

    }


    // INPUT
    else {

        editor = document.createElement('input');

        editor.type = 'text';

        editor.value = valorAtual;

    }


    // Substitui o conteúdo
    var valorElement = field.querySelector('.editable-value');

    valorElement.innerHTML = '';

    valorElement.appendChild(editor);


    editor.focus();

    // Seleciona tudo
    editor.select();


    /*
     * ENTER
     * Salva
     */
    editor.addEventListener('keydown', function(e) {

        if (e.key == 'Enter') {

            // No textarea, Enter sozinho não salva
            if (tipo == 'textarea') {

                // Ctrl + Enter salva
                if (e.ctrlKey) {

                    e.preventDefault();

                    salvarEdicao(field, editor.value, valorOriginal);

                }

            } else {

                e.preventDefault();

                salvarEdicao(field, editor.value, valorOriginal);

            }

        }


        /*
         * ESC
         * Cancela
         */
        if (e.key == 'Escape') {

            cancelarEdicao(field, valorOriginal);

        }

    });

}


function salvarEdicao(field, valor, valorOriginal) {

    valor = valor.trim();

    /*
     * Não salva vazio
     */
    if (valor == '') {

        alert('O campo não pode ficar vazio.');

        return;

    }


    field.classList.remove('editing');


    var valorElement = field.querySelector('.editable-value');

    valorElement.innerHTML = 'Salvando...';


    var xhr = new XMLHttpRequest();

    xhr.open('POST', 'edit_api.php', true);

    xhr.setRequestHeader(
        'Content-Type',
        'application/x-www-form-urlencoded'
    );


    xhr.onreadystatechange = function() {

        if (xhr.readyState != 4) {
            return;
        }


        try {

            var resposta = JSON.parse(xhr.responseText);

            console.log('Resposta do servidor:', resposta);


            if (resposta.success) {

                /*
                 * Atualiza visualmente
                 */
                if (field.getAttribute('data-field') == 'preco_produto') {

                    valorElement.innerHTML = 'R$ ' + valor;

                } else {

                    valorElement.innerHTML = valor;

                }


                /*
                 * Pequena indicação de sucesso
                 */
                mostrarMensagem(field, '✓ Salvo', 'success');

            } else {

                valorElement.innerHTML = valorOriginal;

                mostrarMensagem(
                    field,
                    resposta.message || 'Erro ao salvar',
                    'error'
                );

            }

        } catch (e) {

            valorElement.innerHTML = valorOriginal;

            console.error('Erro ao processar a resposta do servidor:', e);

            mostrarMensagem(
                field,
                'Erro na resposta do servidor',
                'error'
            );

        }

    };


    xhr.send(
        'id=' + encodeURIComponent(field.getAttribute('data-id')) +
        '&campo=' + encodeURIComponent(field.getAttribute('data-field')) +
        '&valor=' + encodeURIComponent(valor)
    );

}


function cancelarEdicao(field, valorOriginal) {

    field.classList.remove('editing');

    var valorElement = field.querySelector('.editable-value');

    valorElement.innerHTML = valorOriginal;

}


function mostrarMensagem(field, mensagem, tipo) {

    var mensagemElement = document.createElement('span');

    mensagemElement.className = 'edit-' + tipo;

    mensagemElement.innerHTML = mensagem;


    field.appendChild(mensagemElement);


    setTimeout(function() {

        mensagemElement.remove();

    }, 2000);

}

</script>
