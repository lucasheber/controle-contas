$(document).ready(function() {
    // Validação e submit do formulário via AJAX
    $('#cadastroForm').on('submit', function(e) {
        e.preventDefault();
        
        const nome = $('#nome').val().trim();
        const email = $('#email').val().trim();
        const senha = $('#senha').val();
        const confirmaSenha = $('#confirma_senha').val();

        // Validação de campos vazios
        if (!nome || !email || !senha || !confirmaSenha) {
            mostrarErro('Por favor, preencha todos os campos!');
            return false;
        }

        // Validação de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            mostrarErro('Por favor, insira um e-mail válido!');
            return false;
        }

        // Validação de senha
        if (senha.length < 6) {
            mostrarErro('A senha deve ter no mínimo 6 caracteres!');
            return false;
        }

        // Validação de confirmação de senha
        if (senha !== confirmaSenha) {
            mostrarErro('As senhas não conferem!');
            return false;
        }

        // Desabilita botão e mostra loading
        const btnSubmit = $(this).find('button[type="submit"]');
        const textoOriginal = btnSubmit.text();
        btnSubmit.prop('disabled', true).text('Cadastrando...');

        // Envia requisição AJAX
        $.ajax({
            url: 'api/cadastro.php',
            type: 'POST',
            data: {
                nome: nome,
                email: email,
                senha: senha,
                confirma_senha: confirmaSenha
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarSucesso(response.message + ' Redirecionando...');
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000);
                } else {
                    mostrarErro(response.message);
                    btnSubmit.prop('disabled', false).text(textoOriginal);
                }
            },
            error: function(xhr, status, error) {
                mostrarErro('Erro ao processar cadastro. Tente novamente!');
                btnSubmit.prop('disabled', false).text(textoOriginal);
                console.error('Erro:', error);
            }
        });
    });

    // Verificar força da senha em tempo real
    $('#senha').on('keyup', function() {
        const senha = $(this).val();
        let forca = 0;
        
        if (senha.length >= 6) forca++;
        if (senha.length >= 8) forca++;
        if (/[A-Z]/.test(senha)) forca++;
        if (/[0-9]/.test(senha)) forca++;
        if (/[^A-Za-z0-9]/.test(senha)) forca++;

        // Você pode adicionar um indicador visual de força da senha aqui
    });

    // Função para mostrar mensagem de erro
    function mostrarErro(mensagem) {
        $('#erro-msg, #sucesso-msg').remove();
        const erroHtml = '<div id="erro-msg" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" style="animation: fadeIn 0.3s ease-in;">' + mensagem + '</div>';
        $('#cadastroForm').before(erroHtml);
        
        setTimeout(function() {
            $('#erro-msg').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Função para mostrar mensagem de sucesso
    function mostrarSucesso(mensagem) {
        $('#erro-msg, #sucesso-msg').remove();
        const sucessoHtml = '<div id="sucesso-msg" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" style="animation: fadeIn 0.3s ease-in;">' + mensagem + '</div>';
        $('#cadastroForm').before(sucessoHtml);
    }
});
