$(document).ready(function() {
    // Validação e submit do formulário via AJAX
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val().trim();
        const senha = $('#senha').val();

        if (!email || !senha) {
            mostrarErro('Por favor, preencha todos os campos!');
            return false;
        }

        // Validação básica de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            mostrarErro('Por favor, insira um e-mail válido!');
            return false;
        }

        // Desabilita botão e mostra loading
        const btnSubmit = $(this).find('button[type="submit"]');
        const textoOriginal = btnSubmit.text();
        btnSubmit.prop('disabled', true).text('Entrando...');

        // Envia requisição AJAX
        $.ajax({
            url: 'api/login.php',
            type: 'POST',
            data: {
                email: email,
                senha: senha
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarSucesso(response.message);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    mostrarErro(response.message);
                    btnSubmit.prop('disabled', false).text(textoOriginal);
                }
            },
            error: function(xhr, status, error) {
                mostrarErro('Erro ao processar login. Tente novamente!');
                btnSubmit.prop('disabled', false).text(textoOriginal);
                console.error('Erro:', error);
            }
        });
    });

    // Efeito de foco nos inputs
    $('input').focus(function() {
        $(this).addClass('ring-2 ring-blue-500');
    }).blur(function() {
        $(this).removeClass('ring-2 ring-blue-500');
    });

    // Função para mostrar mensagem de erro
    function mostrarErro(mensagem) {
        $('#erro-msg').remove();
        const erroHtml = '<div id="erro-msg" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" style="animation: fadeIn 0.3s ease-in;">' + mensagem + '</div>';
        $('#loginForm').before(erroHtml);
        
        setTimeout(function() {
            $('#erro-msg').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Função para mostrar mensagem de sucesso
    function mostrarSucesso(mensagem) {
        $('#erro-msg').remove();
        const sucessoHtml = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" style="animation: fadeIn 0.3s ease-in;">' + mensagem + '</div>';
        $('#loginForm').before(sucessoHtml);
    }
});
