<?php

define('BASE_URL', 'http://localhost/Mubak!/'); 

// Conectando ao gerenciador do banco de dados
require_once 'DbManager/DbMain.php';

// Variáveis de ambiente
require_once 'connection/env.php';
require_once 'connection/connection.php';

// Header e Footer
require_once 'includes/header.php';
require_once 'includes/footer.php';

// Funções do banco de dados
require_once 'database_functions.php';