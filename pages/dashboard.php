<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Controle de Contas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Controle de Contas</h1>
            <div class="flex items-center gap-4">
                <span class="text-gray-600">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
                <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                    Sair
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <!-- Mensagem de feedback -->
        <div id="mensagem-container" class="hidden mb-4">
            <div id="mensagem" class="border px-4 py-3 rounded"></div>
        </div>

        <!-- Cards de estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-gray-600 text-sm font-semibold mb-2">Total Pendente</h3>
                <p class="text-3xl font-bold text-orange-600" id="total-pendente">R$ 0,00</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-gray-600 text-sm font-semibold mb-2">Total Pago</h3>
                <p class="text-3xl font-bold text-green-600" id="total-pago">R$ 0,00</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-gray-600 text-sm font-semibold mb-2">Contas Vencidas</h3>
                <p class="text-3xl font-bold text-red-600" id="contas-vencidas">0</p>
            </div>
        </div>

        <!-- Formulário para adicionar conta -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Adicionar Nova Conta</h2>
            <form id="formAdicionarConta">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Descrição</label>
                        <input type="text" name="descricao" class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Valor</label>
                        <input type="number" step="0.01" name="valor" class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Data de Vencimento</label>
                        <input type="date" name="data_vencimento" class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Categoria</label>
                        <input type="text" name="categoria" class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: Aluguel, Energia">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Observações</label>
                        <textarea name="observacoes" rows="2" class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
                        Adicionar Conta
                    </button>
                </div>
            </form>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-wrap gap-2">
                <button data-filtro="todas" class="filtro-btn bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                    Todas
                </button>
                <button data-filtro="pendentes" class="filtro-btn bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition">
                    Pendentes
                </button>
                <button data-filtro="pagas" class="filtro-btn bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition">
                    Pagas
                </button>
                <button data-filtro="vencidas" class="filtro-btn bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition">
                    Vencidas
                </button>
            </div>
        </div>

        <!-- Lista de contas -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Minhas Contas</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tabela-contas">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Carregando contas...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../js/dashboard.js"></script>
</body>
</html>
