<?php

class DbManager
{
    protected $host;
    protected $user;
    protected $pass;
    protected $database;
    protected $query;
    protected $error;
    protected $data;
    protected $numRows;
    protected $lastInsertId;
    protected $errorMessage;

    public function __construct($user, $pass, $host, $database)
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
        $this->database = $database;
        $this->query = '';
        $this->error = false;
        $this->data = [];
        $this->numRows = 0;
        $this->lastInsertId = '';
        $this->errorMessage = '';
    }

    public function resetDbData()
    {
        $this->error = false;
        $this->data = [];
        $this->numRows = 0;
        $this->errorMessage = '';
    }

    // Realiza a conexão com o banco
    public function connect()
    {
        $connection = new PDO("mysql:host=$this->host;dbname=$this->database", $this->user, $this->pass);

        // Define o modo de erro para exibir os erros
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Define o charset para UTF-8
        $connection->exec("SET NAMES utf8");

        // Reseta os atributos
        $this->resetDbData();

        return $connection;
    }

    // Executa uma query no banco de dados
    public function execute()
    {
        // Cria a conexão com o banco
        $connection = $this->connect();

        // Tenta executar a query
        try {
            $query = $this->query;

            $prepared_statement = $connection->prepare($query);
            if ($prepared_statement->execute()) {
                $this->numRows = $prepared_statement->rowCount();
                $this->data = $prepared_statement->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $this->error = true;
            }
        } catch (PDOException $e) {
            $this->error = true;
            return $this;
        }
        // Caso ocorra algum erro, retorna o erro
        catch (Exception $e) {

            $this->error = true;

            return [
                'error' => true,
                'message' => 'Erro ao executar a query: ' . $this->query
            ];
        }
    }

    // Seta a query a ser executada
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    // Retorna a query
    public function getQuery()
    {
        return $this->query;
    }

    // Retorna error
    public function getError()
    {
        return $this->error;
    }

    // Retorna dados
    public function getData()
    {
        return $this->data;
    }

    // Retorna a quantidade de linhas
    public function getNumRows()
    {
        return $this->numRows;
    }

    // Retorna id do último elemento inserido
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    // Retorna mensagem de erro
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
