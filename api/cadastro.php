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
            'success' => false,
            'message' => 'Preencha todos os campos'
        ]);
        return;
    }

    if ($senha !== $confirma_senha) {
        echo json_encode([
            'success' => false,
            'message' => 'As senhas não conferem!'
        ]);
        return;
    }

    if (strlen($senha) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'A senha deve ter no mínimo 6 caracteres!'
        ]);
        return;
    }

    try {
        $query = "SELECT id FROM usuarios WHERE email = :email";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Este e-mail já está cadastrado!'
            ]);
            return;
        }

        // criptografar a senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $query = "INSERT INTO usuarios(nome, email, senha) VALUES(:nome, :email, :senha)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha_hash);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Cadastro realizado com sucesso!',
                'redirect' => 'index.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao cadastrar, tente novamente!'
            ]);
        }

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao cadastrar. ' . $e->getMessage()
        ]);
    }
}
