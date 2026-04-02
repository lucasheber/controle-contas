<?php

header('Content-type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/classes/Session.php';

Session::requiredLogin();

$user = Session::getUser();

// ADICIONAR CONTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar'){

    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
    $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
    $data_vencimento = filter_input(INPUT_POST, 'data_vencimento', FILTER_SANITIZE_SPECIAL_CHARS);
    $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_SPECIAL_CHARS);
    $observacoes = filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($descricao) || empty($valor) || empty($data_vencimento)) {
        echo json_encode([
            'success' => false,
            'message' => 'Preencha os campos obrigatórios!'
        ]);
        exit();
    }


    try {
        $query = "INSERT INTO contas_pagar (usuario_id, descricao, valor, data_vencimento, categoria, observacoes) 
                  VALUES (:usuario_id, :descricao, :valor, :data_vencimento, :categoria, :observacoes)";
      
        $stmt = $db->prepare($query);
        $stmt->bindParam(':usuario_id', $user->id);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':data_vencimento', $data_vencimento);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':observacoes', $observacoes);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Conta adicionada com sucesso!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error ao adicionar conta!'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

// LISTAR CONTAS
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao']) && $_GET['acao'] === 'listar') {

    $filtro = filter_input(INPUT_GET, 'filtro', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        $query = "SELECT * FROM contas_pagar WHERE usuario_id = :usuario_id";

        if ($filtro === 'pendentes') {
            $query .= " AND status = 'pendente'";
        } elseif ($filtro === 'pagas') {
            $query .= " AND status = 'pago'";
        } elseif ($filtro === 'vencidas') {
            $query .= " AND status = 'pendente' AND data_vencimento < CURDATE()";
        }

        $query .= " ORDER BY data_vencimento ASC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':usuario_id', $user->id);
        $stmt->execute();

        $contas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $query_totais = "SELECT 
                            SUM(CASE WHEN status = 'pendente' THEN valor ELSE 0 END) as total_pendente,
                            SUM(CASE WHEN status = 'pago' THEN valor ELSE 0 END) as total_pago,
                            COUNT(CASE WHEN status = 'pendente' AND data_vencimento < CURDATE() THEN 1 END) as contas_vencidas
                         FROM contas_pagar WHERE usuario_id = :usuario_id";

        $stmt = $db->prepare($query_totais);
        $stmt->bindParam(':usuario_id', $user->id);
        $stmt->execute();
        $totais = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'contas' => $contas,
            'totais' => $totais
        ]);

    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

// PAGAR CONTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'pagar'){
    $conta_id = filter_input(INPUT_POST, 'conta_id', FILTER_VALIDATE_INT);

    if (empty($conta_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Conta inválida ou não informada!'
        ]);

        exit();
    }

    $data_pagamento = date('Y-m-d');

    try {
        $query = "UPDATE contas_pagar SET status = 'pago', data_pagamento = :data_pagamento WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':data_pagamento', $data_pagamento);
        $stmt->bindParam(':id', $conta_id);
        $stmt->bindParam(':usuario_id', $user->id);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Conta marcada como paga!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error ao atualizar conta!'
            ]);
        }

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro: ' . $e->getMessage()
        ]);
    }

}

// EXCLUIR CONTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir'){if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'pagar'){
    $conta_id = filter_input(INPUT_POST, 'conta_id', FILTER_VALIDATE_INT);

    if (empty($conta_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Conta inválida ou não informada!'
        ]);

        exit();
    }

    $data_pagamento = date('Y-m-d');

    try {
        $query = "UPDATE contas_pagar SET status = 'pago', data_pagamento = :data_pagamento WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':data_pagamento', $data_pagamento);
        $stmt->bindParam(':id', $conta_id);
        $stmt->bindParam(':usuario_id', $user->id);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Conta marcada como paga!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error ao atualizar conta!'
            ]);
        }

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro: ' . $e->getMessage()
        ]);
    }

}
    $conta_id = filter_input(INPUT_POST, 'conta_id', FILTER_VALIDATE_INT);

    if (empty($conta_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Conta inválida ou não informada!'
        ]);

        exit();
    }

    try {
        $query = "DELETE FROM contas_pagar WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $conta_id);
        $stmt->bindParam(':usuario_id', $user->id);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Conta excluída com sucesso!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error ao excluir conta!'
            ]);
        }

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro: ' . $e->getMessage()
        ]);
    }

}