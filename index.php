<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Controle de Contas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Controle de Contas</h1>
            <p class="text-gray-600">Faça login para continuar</p>
        </div>

        <form id="loginForm">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    E-mail
                </label>
                <input 
                    class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    id="email" 
                    name="email" 
                    type="email" 
                    placeholder="seu@email.com"
                    required
                >
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="senha">
                    Senha
                </label>
                <input 
                    class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    id="senha" 
                    name="senha" 
                    type="password" 
                    placeholder="********"
                    required
                >
            </div>

            <div class="flex items-center justify-between mb-6">
                <button 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded focus:outline-none focus:shadow-outline w-full transition duration-200" 
                    type="submit"
                >
                    Entrar
                </button>
            </div>

            <div class="text-center">
                <p class="text-gray-600">
                    Não tem uma conta? 
                    <a href="cadastro.php" class="text-blue-500 hover:text-blue-800 font-bold">
                        Cadastre-se
                    </a>
                </p>
            </div>
        </form>
    </div>

    <script src="js/login.js"></script>
</body>
</html>
