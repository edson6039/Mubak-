<?php


class DbManagerActions extends DbManager
{
    // O cursor é utilizado para evitar repetições
    private $cursor;

    public function __construct($user, $pass, $host, $database)
    {
        parent::__construct($user, $pass, $host, $database);
        $this->cursor = 0;
    }

    // Itera sobre os dados retornados
    public function fetch(callable $callback)
    {

        foreach ($this->data as $row) {
            $callback($row);
        }
    }

    private function verifyInnerJoin($inner_join)
    {
        // Se inner join não for um array, é definido um erro
        if (!is_array($inner_join)) {

            $this->error = true;
            return $this;
        }
        // Se inner join for um array...
        else {


            // Itera sobre os itens do inner join. Exemplo: ['unidade', 'id_unidade =' => '5'] 
            for ($i = 0; $i < count($inner_join); $i++) {
                // É adicionada à query a chave do array e o valor é uma interrogação
                $this->query .= "INNER JOIN " . $inner_join[$i][0] . " ON " . $inner_join[$i][1] . " ";
            }
        }
    }

    private function verifyLeftJoin($left_join)
    {
        // Se left join não for um array, é definido um erro
        if (!is_array($left_join)) {

            $this->error = true;
            return $this;
        }
        // Se left join for um array...
        else {

            // Itera sobre os itens do left join. Exemplo: ['unidade', 'id_unidade =' => '5'] 
            for ($i = 0; $i < count($left_join); $i++) {
                // É adicionada à query a chave do array e o valor é uma interrogação
                $this->query .= "LEFT JOIN " . $left_join[$i][0] . " ON " . $left_join[$i][1] . " ";
            }
        }
    }

    // Verifica se existe a cláusula where
    private function verifyWhere($where)
    {
        $bind_items = array();

        $cursor = $this->cursor;

        if ($where) {
            $this->query .= "WHERE ";

            // Se where não for um array, é definido um erro
            if (!is_array($where)) {

                $this->error = true;
                return $this;
            }
            // Se where for um array...
            else {

                // Itera sobre os itens do where. Exemplo: ['id_unidade =' => '5'] 
                foreach ($where as $key => $value) {
                    // Se $cursor for maior que 0, adiciona um espaço antes de cada condição
                    if ($cursor > 0) {
                        $this->query .= " ";
                    }
                    // É adicionada à query a chave do array e o valor é uma interrogação
                    $this->query .= "$key :bind_$cursor ";

                    // O valor é adicionado ao array de bind
                    $bind_items[":bind_$cursor"] = $value;

                    $cursor++;
                    $this->cursor = $cursor;
                }
            }

            return $bind_items;
        } else {
            return $bind_items;
        }
    }

    // Verifica os valores para inserção
    private function verifyValuesToInsert($values)
    {
        $values_array = explode(',', $values);

        $cursor = $this->cursor;

        $this->query .= "VALUES (";

        // Itera sobre os itens do values. Exemplo: ['id_unidade =' => '5'] 
        foreach ($values_array as $value) {
            
            // numeros, numeros e espaços ou espaços
            // if (!preg_match('/^[A-Za-z]+$/',$value)) {
                $value = trim($value);
            // }
            

            // É adicionada à query a chave do array e o valor é uma interrogação
            $this->query .= ":bind_$cursor";

            if ($cursor < count($values_array) - 1) {
                $this->query .= ", ";
            }

            // O valor é adicionado ao array de bind
            $bind_items[":bind_$cursor"] = $value;

            $cursor++;
            $this->cursor = $cursor;
        }

        $this->query .= ")";

        return $bind_items;
    }

    // Verifica os valores para atualização
    private function verifyValuesToUpdate($values)
    {
        $values_array = explode(',', $values);

        $cursor = $this->cursor;

        // Itera sobre os itens do values. Exemplo: ['id_unidade =' => '5'] 
        foreach ($values_array as $arguments) {

            $arguments_array = explode('=', $arguments);

            // É adicionada à query a chave do array e o valor é uma interrogação
            $this->query .= "$arguments_array[0]= :bind_$cursor ";

            $tamanho_maximo_array_de_valores = count($values_array) - 1;

            if ($cursor < $tamanho_maximo_array_de_valores || $cursor == $tamanho_maximo_array_de_valores - 1) {
                $this->query .= ", ";
            }
            
            // numeros, numeros e espaços ou espaços
            // if (!preg_match('/^[A-Za-z]+$/', $arguments_array[1])) {
                $arguments_array[1] = trim($arguments_array[1]);
            // }

            // O valor é adicionado ao array de bind
            $bind_items[":bind_$cursor"] = $arguments_array[1];

            $cursor++;
            $this->cursor = $cursor;
        }

        return $bind_items;
    }

    // Realiza select no banco de dados
    public function select($table, $columns = '*', $inner_join = null, $left_join = null, $where = null, $order = null, $limit = null, $complement = '', $group_by = null)
    {
        $this->resetDbData();

        // O cursor é utilizado para evitar que os valores sejam repetidos
        $this->cursor = 0;


        try {
            $connection = $this->connect();

            // PDO
            $this->query = "SELECT $columns FROM $table ";

            // Se existir um where
            $bind_items = array();

            // Se existir um inner join
            if ($inner_join) {

                $this->verifyInnerJoin($inner_join);
            }

            // Se existir um left join
            if ($left_join) {

                $this->verifyLeftJoin($left_join);
            }

            // Se existir um Where
            $bind_items = $this->verifyWhere($where);

            // Se existir um group by
            if ($group_by) {
                $this->query .= "GROUP BY $group_by ";
            }

            // Se existir um order
            if ($order) {
                $this->query .= "ORDER BY $order ";
            }

            // Se existir um limit
            if ($limit) {
                $this->query .= "LIMIT $limit ";
            }

            $this->query .= $complement;

            // Prepara a query
            $prepared_statement = $connection->prepare($this->query);

            // Realiza o bind dos parâmetros
            foreach ($bind_items as $key => $value) {
                $prepared_statement->bindValue($key, $value);
            }

            // Executa a query
            if ($prepared_statement->execute()) {
                $this->numRows = $prepared_statement->rowCount();
                $this->data = $prepared_statement->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $this->error = true;
            }
        } catch (PDOException $e) {
            $this->error = true;
            $this->errorMessage = $e->getMessage();
        }

        return $this;
    }
      // Realiza select no banco de dados e retorna dados direto
      public function selectData($table, $columns = '*', $inner_join = null, $left_join = null, $where = null, $order = null, $limit = null, $complement = '')
      {
          $this->resetDbData();
  
          // O cursor é utilizado para evitar que os valores sejam repetidos
          $this->cursor = 0;
  
  
          try {
              $connection = $this->connect();
  
              // PDO
              $this->query = "SELECT $columns FROM $table ";
  
              // Se existir um where
              $bind_items = array();
  
              // Se existir um inner join
              if ($inner_join) {
  
                  $this->verifyInnerJoin($inner_join);
              }
  
              // Se existir um left join
              if ($left_join) {
  
                  $this->verifyLeftJoin($left_join);
              }
  
              // Se existir um Where
              $bind_items = $this->verifyWhere($where);
  
              // Se existir um order
              if ($order) {
                  $this->query .= "ORDER BY $order ";
              }
  
              // Se existir um limit
              if ($limit) {
                  $this->query .= "LIMIT $limit ";
              }
  
              $this->query .= $complement;
  
              // Prepara a query
              $prepared_statement = $connection->prepare($this->query);
  
              // Realiza o bind dos parâmetros
              foreach ($bind_items as $key => $value) {
                  $prepared_statement->bindValue($key, $value);
              }
  
              // Executa a query
              if ($prepared_statement->execute()) {
                  $this->numRows = $prepared_statement->rowCount();
                  $this->data = $prepared_statement->fetchAll(PDO::FETCH_ASSOC);
              } else {
                  $this->error = true;
              }
          } catch (PDOException $e) {
              $this->error = true;
              $this->errorMessage = $e->getMessage();
          }
  
          return $this->data;
      }

    // Realiza find no banco de dados
    public function find($table, $columns = '*', $inner_join = null, $left_join = null, $where = null, $complement = '', $group_by = null)
    {
        $this->resetDbData();

        // O cursor é utilizado para evitar que os valores sejam repetidos
        $this->cursor = 0;


        try {
            $connection = $this->connect();

            // PDO
            $this->query = "SELECT $columns FROM $table ";

            // Se existir um where
            $bind_items = array();

            // Se existir um inner join
            if ($inner_join) {

                $this->verifyInnerJoin($inner_join);
            }

            // Se existir um group by
            if ($group_by) {
                $this->query .= "GROUP BY $group_by ";
            }

            // Se existir um left join
            if ($left_join) {

                $this->verifyLeftJoin($left_join);
            }

            // Se existir um Where
            $bind_items = $this->verifyWhere($where);

            $this->query .= $complement;

            // Prepara a query
            $prepared_statement = $connection->prepare($this->query);

            // Realiza o bind dos parâmetros
            foreach ($bind_items as $key => $value) {
                $prepared_statement->bindValue($key, $value);
            }

            // Executa a query
            if ($prepared_statement->execute()) {
                $this->data = $prepared_statement->fetch(PDO::FETCH_ASSOC);
            } else {
                $this->error = true;
            }
        } catch (PDOException $e) {
            $this->error = true;
            $this->errorMessage = $e->getMessage();
        }

        return $this->data;
    }

    // Realiza insert no banco de dados
    public function insert($table, $values, $columns = null)
    {
        $this->resetDbData();

        // O cursor é utilizado para evitar que os valores sejam repetidos
        $this->cursor = 0;

        try {
            $connection = $this->connect();

            // PDO
            $this->query = "INSERT INTO $table ";

            // Se existir um where
            $bind_items = array();

            // Se existir columns
            if ($columns) {
                $this->query .= "($columns) ";
            }

            // verifica os valores para inserção
            $bind_items = $this->verifyValuesToInsert($values);


            // Prepara a query
            $prepared_statement = $connection->prepare($this->query);

            // Realiza o bind dos parâmetros
            foreach ($bind_items as $key => $value) {
                $prepared_statement->bindValue($key, $value);
            }

            // Executa a query
            if ($prepared_statement->execute()) {
                $this->numRows = $prepared_statement->rowCount();
                $this->data = $prepared_statement->fetchAll(PDO::FETCH_ASSOC);
                $this->lastInsertId = $connection->lastInsertId();
            } else {
                $this->error = true;
            }
        } catch (PDOException $e) {
            $this->error = true;
            $this->errorMessage = $e->getMessage();
        }
    }

    // Realiza update no banco de dados
    public function update($table, $values, $where)
    {
        $this->resetDbData();
        // O cursor é utilizado para evitar que os valores sejam repetidos
        $this->cursor = 0;

        try {
            $connection = $this->connect();

            // PDO
            $this->query = "UPDATE $table SET ";

            // Se existir um where
            $bind_items = array();

            // verifica os valores para inserção
            $bind_items = $this->verifyValuesToUpdate($values);

            // Se existir um where
            $bind_items = array_merge($bind_items, $this->verifyWhere($where));

            // Prepara a query
            $prepared_statement = $connection->prepare($this->query);

            // Realiza o bind dos parâmetros
            foreach ($bind_items as $key => $value) {
                $prepared_statement->bindValue($key, $value);
            }

            // Executa a query
            if ($prepared_statement->execute()) {
                $this->numRows = $prepared_statement->rowCount();
                $this->data = $prepared_statement->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $this->error = true;
            }
        } catch (PDOException $e) {
            $this->error = true;
            $this->errorMessage = $e->getMessage();
        }
    }

    // Verifica se campo existe no banco de dados
    public function exists($table, $column, $value)
    {
        $this->resetDbData();

        $this->query = "SELECT * FROM $table WHERE $column = '$value'";

        $this->execute();

        return $this->numRows ? true : false;
    }
}
