<?php

header('Content-type: application/json');

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = filter_input(INPUT_POST, 'nome', FILTER_DEFAULT);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_DEFAULT);
    $confirma_senha = filter_input(INPUT_POST, 'confirma_senha', FILTER_DEFAULT);

    if (empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
        echo json_encode([
            'success'=> false,
            'message'=> 'Preencha todos os campos'
        ]);
        return;
    }

    echo json_encode([
        'success'=> false,
        'message'=> 'Apenas um exemplo de resposta JSON'
    ]);
}
