$(document).ready(function() {
    let filtroAtual = 'todas';

    // Carrega as contas ao iniciar a página
    carregarContas();

    // Submit do formulário para adicionar conta via AJAX
    $('#formAdicionarConta').on('submit', function(e) {
        e.preventDefault();
        
        const descricao = $('input[name="descricao"]').val().trim();
        const valor = $('input[name="valor"]').val();
        const dataVencimento = $('input[name="data_vencimento"]').val();
        const categoria = $('input[name="categoria"]').val().trim();
        const observacoes = $('textarea[name="observacoes"]').val().trim();

        if (!descricao || !valor || !dataVencimento) {
            mostrarMensagem('Por favor, preencha os campos obrigatórios!', 'erro');
            return false;
        }

        if (parseFloat(valor) <= 0) {
            mostrarMensagem('O valor deve ser maior que zero!', 'erro');
            return false;
        }

        // Desabilita botão
        const btnSubmit = $(this).find('button[type="submit"]');
        const textoOriginal = btnSubmit.text();
        btnSubmit.prop('disabled', true).text('Adicionando...');

        $.ajax({
            url: '../api/contas.php',
            type: 'POST',
            data: {
                acao: 'adicionar',
                descricao: descricao,
                valor: valor,
                data_vencimento: dataVencimento,
                categoria: categoria,
                observacoes: observacoes
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarMensagem(response.message, 'sucesso');
                    $('#formAdicionarConta')[0].reset();
                    carregarContas();
                } else {
                    mostrarMensagem(response.message, 'erro');
                }
                btnSubmit.prop('disabled', false).text(textoOriginal);
            },
            error: function(xhr, status, error) {
                mostrarMensagem('Erro ao adicionar conta!', 'erro');
                btnSubmit.prop('disabled', false).text(textoOriginal);
                console.error('Erro:', error);
            }
        });
    });

    // Filtros de contas
    $('.filtro-btn').on('click', function() {
        filtroAtual = $(this).data('filtro');
        
        // Atualiza visual dos botões
        $('.filtro-btn').removeClass('bg-blue-500 text-white').addClass('bg-gray-200 text-gray-700');
        $(this).removeClass('bg-gray-200 text-gray-700').addClass('bg-blue-500 text-white');
        
        carregarContas();
    });

    // Delegação de eventos para botões dinâmicos
    $(document).on('click', '.btn-pagar', function(e) {
        e.preventDefault();
        const contaId = $(this).data('id');
        
        if (confirm('Marcar esta conta como paga?')) {
            pagarConta(contaId);
        }
    });

    $(document).on('click', '.btn-excluir', function(e) {
        e.preventDefault();
        const contaId = $(this).data('id');
        
        if (confirm('Tem certeza que deseja excluir esta conta?')) {
            excluirConta(contaId);
        }
    });

    // Função para carregar contas
    function carregarContas() {
        $.ajax({
            url: '../api/contas.php',
            type: 'GET',
            data: {
                acao: 'listar',
                filtro: filtroAtual
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    atualizarTotais(response.totais);
                    renderizarContas(response.contas);
                } else {
                    mostrarMensagem('Erro ao carregar contas!', 'erro');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensagem('Erro ao carregar contas!', 'erro');
                console.error('Erro:', error);
            }
        });
    }

    // Função para atualizar totais
    function atualizarTotais(totais) {
        $('#total-pendente').text('R$ ' + formatarValor(totais.total_pendente || 0));
        $('#total-pago').text('R$ ' + formatarValor(totais.total_pago || 0));
        $('#contas-vencidas').text(totais.contas_vencidas || 0);
    }

    // Função para renderizar contas na tabela
    function renderizarContas(contas) {
        const tbody = $('#tabela-contas');
        tbody.empty();

        if (contas.length === 0) {
            tbody.append('<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhuma conta encontrada.</td></tr>');
            return;
        }

        contas.forEach(function(conta) {
            const statusInfo = getStatusInfo(conta);
            const dataVencimento = formatarData(conta.data_vencimento);
            const observacoes = conta.observacoes ? '<div class="text-sm text-gray-500">' + conta.observacoes + '</div>' : '';
            const categoria = conta.categoria || '-';
            
            const acoes = conta.status === 'pendente' 
                ? '<a href="#" class="btn-pagar text-green-600 hover:text-green-900 mr-3" data-id="' + conta.id + '">Pagar</a>'
                : '';
            
            const row = `
                <tr class="hover:bg-gray-50 ${statusInfo.rowClass}">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">${conta.descricao}</div>
                        ${observacoes}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">${categoria}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">R$ ${formatarValor(conta.valor)}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">${dataVencimento}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusInfo.class}">
                            ${statusInfo.texto}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        ${acoes}
                        <a href="#" class="btn-excluir text-red-600 hover:text-red-900" data-id="${conta.id}">Excluir</a>
                    </td>
                </tr>
            `;
            
            tbody.append(row);
        });
    }

    // Função para pagar conta
    function pagarConta(contaId) {
        $.ajax({
            url: '../api/contas.php',
            type: 'POST',
            data: {
                acao: 'pagar',
                conta_id: contaId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarMensagem(response.message, 'sucesso');
                    carregarContas();
                } else {
                    mostrarMensagem(response.message, 'erro');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensagem('Erro ao pagar conta!', 'erro');
                console.error('Erro:', error);
            }
        });
    }

    // Função para excluir conta
    function excluirConta(contaId) {
        $.ajax({
            url: '../api/contas.php',
            type: 'POST',
            data: {
                acao: 'excluir',
                conta_id: contaId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarMensagem(response.message, 'sucesso');
                    carregarContas();
                } else {
                    mostrarMensagem(response.message, 'erro');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensagem('Erro ao excluir conta!', 'erro');
                console.error('Erro:', error);
            }
        });
    }

    // Função para obter informações de status
    function getStatusInfo(conta) {
        let statusClass = 'bg-yellow-100 text-yellow-800';
        let statusTexto = 'Pendente';
        let rowClass = '';
        
        if (conta.status === 'pago') {
            statusClass = 'bg-green-100 text-green-800';
            statusTexto = 'Pago';
        } else if (conta.status === 'pendente' && conta.data_vencimento < new Date().toISOString().split('T')[0]) {
            statusClass = 'bg-red-100 text-red-800';
            statusTexto = 'Vencido';
            rowClass = 'bg-red-50';
        }
        
        return {
            class: statusClass,
            texto: statusTexto,
            rowClass: rowClass
        };
    }

    // Função para formatar data
    function formatarData(data) {
        const partes = data.split('-');
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }

    // Função para formatar valor
    function formatarValor(valor) {
        return parseFloat(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Função para mostrar mensagem
    function mostrarMensagem(mensagem, tipo) {
        const container = $('#mensagem-container');
        const msgDiv = $('#mensagem');
        
        container.removeClass('hidden');
        msgDiv.removeClass('bg-red-100 border-red-400 text-red-700 bg-green-100 border-green-400 text-green-700');
        
        if (tipo === 'erro') {
            msgDiv.addClass('bg-red-100 border-red-400 text-red-700');
        } else {
            msgDiv.addClass('bg-green-100 border-green-400 text-green-700');
        }
        
        msgDiv.text(mensagem);
        
        setTimeout(function() {
            container.fadeOut('slow', function() {
                container.addClass('hidden').show();
            });
        }, 3000);
    }

    // Formatar valor monetário ao digitar
    $('input[name="valor"]').on('blur', function() {
        let valor = parseFloat($(this).val());
        if (!isNaN(valor)) {
            $(this).val(valor.toFixed(2));
        }
    });

    // Definir data mínima como hoje
    const hoje = new Date().toISOString().split('T')[0];
    $('input[name="data_vencimento"]').attr('min', hoje);

    // Contador de caracteres para observações
    $('textarea[name="observacoes"]').on('keyup', function() {
        const maxLength = 500;
        const currentLength = $(this).val().length;
        
        if (!$(this).next('.char-counter').length) {
            $(this).after('<div class="char-counter text-xs text-gray-500 mt-1"></div>');
        }
        
        $(this).next('.char-counter').text(currentLength + ' / ' + maxLength + ' caracteres');
        
        if (currentLength > maxLength) {
            $(this).val($(this).val().substring(0, maxLength));
        }
    });
});
