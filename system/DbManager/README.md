# DbManager v0.1:

## **ATUALIZAÇÕES...**

Uma das funções mais utilizadas
pelo **PHP** para conexão e
gerenciamento de consultas
no banco de dados era a
**mysql\_\***

```php
mysql_query("SELECT * FROM usuarios");
```

Entretanto, o PHP, na **_versão 5.6_** lança o
**mysqli\_\*** e agora a
antiga função mysql*\* tornou-se
***depreciada.**\_ Não sendo
mais suportada pela
linguagem nas próximas versões.

Uso da função mysqli\_:

```php
$resultado = mysqli_query($conn, "SELECT * FROM usuarios");
```

> Nessa nova versão, agora é necessário informar um **objeto de conexão com o banco de dados** _($conn)_ e em seguida a consulta.

## **O PROBLEMA...**

Com isso, sistemas que utilizavam
o **mysql\_\*** tornaram-se _“depreciados”._
Então, o que poderia ser
feito para resolver o
problema?

> **Resposta:** Atualizar uma
> centena de arquivos que gerenciavam as conexões fazendo
> uso do **mysql\_\*** para a nova versão: **mysqli\_\***.

Entretanto, isso se torna impraticável quando se tem
centenas de arquivos que se relacionam
com o banco de dados.

## **POSSÍVEL SOLUÇÃO**

Tendo em mente esse cenário,
para precaver uma possível
descontinuação do mysqli,
surge a proposta de utilizar
um gerenciador próprio: o **DbManager**.

> O **DbManager** é uma classe que gerencia a
> conexão e consultas ao banco de dados.
> Além de ajudar na execução de _queries_, o **DbManager**
> também possui métodos simples para facilitar
> a realização de operações mais comuns que em
> _\*\*mysqli_\*\*\* precisaria de mais código.

## **Como usar?**

Instanciando o **DbManager**:

> A porta de entrada do **DbManager** é o arquivo **DbMain.php**.

```php
// Instancia a classe
$db = new DbManagerActions('usuario', 'senha', 'host', 'banco');
```

## **Uma das formas de deixar o **DbManager** GLOBAL:**

```php
// Torna o objeto disponível globalmente
$GLOBALS['db'] = $db;
```

## **Realizando CONSULTA e utilizando dados**

```php
$db = $GLOBALS['db'];

// Define a consulta
$db->setQuery("SELECT * FROM usuarios");

// Executa a consulta
$db->execute();

// Obtém os resultados e já itera sobre eles
$db->fetch(function($row){

    echo $row['nome_usuario'];

});
```

## **Visualizando a última consulta realizada**

```php
// Executa a consulta
$db->getQuery(); // Retorna a última consulta realizada
});
```

## **E se eu não quiser usar o "fetch"?**

_Solução 1:_

```php
/* Realiza a consulta... */

// Obtém o resultado e armaneza em uma variável
$dados = $db->getData();
```

_Solução 2:_ **(RECOMENDADA)**

```php
/* Realiza a consulta... */

// Realiza iteração sobre os dados retornados da consulta
foreach($db->getData() as  $dados){

    // Lógica aqui

}
```

## **Como verificar erros?**

### _Exemplo 1:_

```php
/* Realiza a consulta... */

// Obtém erro e armaneza em uma variável
// Se houver erro, a variável receberá um booleano com valor true
$erro = $db->getError();
```

### _Exemplo 2:_

```php
/* Realiza a consulta... */

if($db->getError()){
    echo "Erro ao executar a consulta: ". $db->getErrorMessage();
} else{
    // Processa os dados
}
```

> O método "getErrorMessage()" retorna a mensagem de erro da última consulta realizada.

### _Exemplo 3:_

```php
/* Realiza a consulta... */

// Verifica se houve erro
if ($db->getError()) {

    // getQuery() retorna a consulta que gerou o erro
    echo "Erro! Query: " . $db->getQuery() . "\n";

} else{
    // Continua com o processamento
}
```

## **Métodos Curtos:**

## _select()_

> O método **select** aceita os parâmetros: **table, inner_join, left_join, columns, where, order, limit e complement**. onde:

- o parâmetro _table_ é obrigatório
- o parâmetro _columns_ é opcional e seu valor padrão é **'\*'**, ou seja, todas as colunas

### _Exemplo 1:_

```php
$db->select('usuarios'); // SELECT * FROM usuarios
```

### _Exemplo 2:_

```php
$db->select(table: 'usuarios', where: ['ativo_usuario =' => '1']); // SELECT * FROM usuarios WHERE ativo_usuario = 1
```

### _Exemplo 3:_

```php
$db->select(
    table: 'usuarios',
    columns: 'nome_usuario, email_usuario',
    where: ['ativo_usuario =' => '1'],
    order: 'nome_usuario ASC',
    limit: '0, 10'
); // SELECT nome_usuario, email_usuario FROM usuarios WHERE ativo_usuario = 1 ORDER BY nome_usuario ASC LIMIT 0, 10
```

### _Exemplo 4:_

```php
$db->select(
    table: 'setor',
    inner_join: array(
        ['unidade', 'id_unidade = unidade_id_unidade'],
        ['alocacao', 'alocacao.unidade_id_unidade = setor.unidade_id_unidade']
    ),
    where: [
        'ativo_setor =' => '1',
    ]
); // SELECT * FROM setor INNER JOIN unidade ON unidade.id_unidade = setor.unidade_id_unidade INNER JOIN alocacao ON alocacao.unidade_id_unidade = setor.unidade_id_unidade WHERE ativo_setor = 1
```

### _Exemplo 5:_

```php
$db->select(
    table: 'setor',
    left_join: array(
        ['unidade', 'id_unidade = unidade_id_unidade'],
        ['alocacao', 'alocacao.unidade_id_unidade = setor.unidade_id_unidade']
    ),
    where: [
        'ativo_setor =' => '1',
    ]
); // SELECT * FROM setor LEFT JOIN unidade ON unidade.id_unidade = setor.unidade_id_unidade LEFT JOIN alocacao ON alocacao.unidade_id_unidade = setor.unidade_id_unidade WHERE ativo_setor = 1
```

### _Exemplo 6:_

```php
$db->select(
    table: 'setor',
    complement: 'WHERE id_agenda NOT IN (SELECT agenda_id_agenda FROM guia_sp_sadt_agenda)
                                            ORDER BY data_consulta_agenda ASC',
); // O complement é utilizado para adicionar um SQL que não foi possível com os parâmetros do dbManager
```

### _selectData(...):_ O mesmo que o select() mas retorna os dados diretamente

```php
$db->selectData(
    table: 'setor',
    complement: 'WHERE id_agenda NOT IN (SELECT agenda_id_agenda FROM guia_sp_sadt_agenda)
                                            ORDER BY data_consulta_agenda ASC',
);
```

### _Pegando o número de linhas da consulta (num_rows):_

```php
// Consulta...

$db->getNumRows(); // Retorna a quantidade de linhas
```



## _find()_

> O método **find** traz somente uma linha da busca.

> O método **find** aceita os parâmetros: **table, inner_join, left_join, columns, where e complement**. onde:

- o parâmetro _table_ é obrigatório
- o parâmetro _columns_ é opcional e seu valor padrão é **'\*'**, ou seja, todas as colunas

### _Exemplo 1:_

```php
$db->find('usuarios'); // SELECT * FROM usuarios
```

### _Exemplo 2:_

```php
$db->find(table: 'usuarios', where: ['ativo_usuario =' => '1']); // SELECT * FROM usuarios WHERE ativo_usuario = 1
```

### _Exemplo 3:_

```php
$db->find(
    table: 'setor',
    inner_join: array(
        ['unidade', 'id_unidade = unidade_id_unidade'],
        ['alocacao', 'alocacao.unidade_id_unidade = setor.unidade_id_unidade']
    ),
    where: [
        'ativo_setor =' => '1',
    ]
); // SELECT * FROM setor INNER JOIN unidade ON unidade.id_unidade = setor.unidade_id_unidade INNER JOIN alocacao ON alocacao.unidade_id_unidade = setor.unidade_id_unidade WHERE ativo_setor = 1
```

### _Exemplo 4:_

```php
$db->find(
    table: 'setor',
    left_join: array(
        ['unidade', 'id_unidade = unidade_id_unidade'],
        ['alocacao', 'alocacao.unidade_id_unidade = setor.unidade_id_unidade']
    ),
    where: [
        'ativo_setor =' => '1',
    ]
); // SELECT * FROM setor LEFT JOIN unidade ON unidade.id_unidade = setor.unidade_id_unidade LEFT JOIN alocacao ON alocacao.unidade_id_unidade = setor.unidade_id_unidade WHERE ativo_setor = 1
```

### _Exemplo 5:_

```php
$db->find(
    table: 'setor',
    complement: 'WHERE id_agenda NOT IN (SELECT agenda_id_agenda FROM guia_sp_sadt_agenda)
                                            ORDER BY data_consulta_agenda ASC',
); // O complement é utilizado para adicionar um SQL que não foi possível com os parâmetros do dbManager
```




## _insert()_

> O método **insert** aceita os parâmetros: **table, values** e **columns**. onde:

- os parâmetros _table_ e _values_ são obrigatórios

### _Exemplo 1:_

```php
$db->insert(table:'usuarios', values:'John Doe, 23'); // INSERT INTO usuarios VALUES ('John Doe, 23')
```

### _Exemplo 2:_

```php
$db->insert(
    table:'usuarios',
    values:'John Doe, 23',
    columns: 'nome_usuario, idade_usuario'
    ); // INSERT INTO usuarios (nome_usuario, idade_usuario) VALUES ('John Doe, 23')
```

### _Como pegar id do último elemento inserido:_

```php
// Inserção...

$db->getLastInsertId() // Retorna o último id que foi inserido
```

## _update()_

> O método **update** aceita os parâmetros: **table, values, where**. onde:

- os parâmetros _table_, _values_ e _where_ são obrigatórios.

### _Exemplo 1:_

```php
$db->update(
    table:'usuarios',
    values:'nome_usuario = John Doe',
    where:['id_usuario =' => '1']); // UPDATE usuarios SET nome_usuario = "John Doe" WHERE id_usuario = 1
```

> OBS: Mesmo em STRINGS, não utilize aspas!

## _exists()_

$table, $column, $value

> O método **exists** aceita os parâmetros: **table, column** e **value**. onde:

- os parâmetros _table_, _column_ e _value_ são obrigatórios.

### _Exemplo 1:_

```php
$db->exists(
    table:'usuarios',
    column:'nome_usuario',
    value:'John Doe'
    ); // true
```

## **Encadeamento de métodos:**

### _Exemplo (Select + Fetch):_

```php
$db->select('usuario')->fetch(

    function ($row) {

        echo $row['nome_usuario'];

    }

);
```
