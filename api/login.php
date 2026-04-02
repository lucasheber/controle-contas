<?php

header('Content-type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/classes/Session.php';

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = filter_input(INPUT_POST, 'senha', FILTER_DEFAULT);


try {

    $query = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);

    if (!$stmt->execute()) {
        throw new \Exception('Falha ao processar o login. Favor tente novamente mais tarde.');
    }

    if ($stmt->rowCount() <= 0) {
        throw new \Exception('Usuario não encontrado.');
    }

    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if (password_verify($senha, $user->senha)) {

        unset($user->senha);
        Session::setUser($user);

        echo json_encode([
            'success' => true,
            'message' => 'Login efetuado com sucesso!',
            'redirect' => 'http://localhost/controle-contas/pages/dashboard.php',
        ]);
        return;
    }

    throw new \Exception('Usuario ou senha inválidos.');

} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
    return;
}