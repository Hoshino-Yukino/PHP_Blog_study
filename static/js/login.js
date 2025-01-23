$(document).ready(function() {
    $('#loginBtn').on('click', function(event) {
        let userId = $('#userIdInput').val().trim();
        let password = $('#passWordInput').val().trim();

        if (userId === '' || password === '') {
            alert('ユーザ ID とパスワードを入力してください。');
            event.preventDefault();
        } else {
            // フォーム送信前の簡易アニメーション
            $(this).text('送信中...').css('opacity', '0.8');
        }
    });

    $('#showLogin').on('click', function () {
        $('.login-container').show();
        $('.register-container').hide();
    });

    $('#showRegister').on('click', function () {
        $('.login-container').hide();
        $('.register-container').show();
    }); 
});

    
function onSubmit(token) {
    document.getElementById("demo-form").submit();
} 
