<?php

define('BASE_URL', 'http://localhost/Mubak-/'); 

// Conectando ao gerenciador do banco de dados
require_once __DIR__ . '/DbManager/DbMain.php';

// Variáveis de ambiente
require_once __DIR__ . '/connection/env.php';
require_once __DIR__ . '/connection/connection.php';

// Funções do banco de dados
require_once __DIR__ . '/database_functions.php';