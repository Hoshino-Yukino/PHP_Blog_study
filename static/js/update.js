if (!window.turnstile) {
    const script = document.createElement("script");
    script.src = "https://challenges.cloudflare.com/turnstile/v0/api.js";
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

$(document).ready(function () {
    $(document).on('click', '#submitUpdateButton', function () {
        const formData = $('#updateForm').serialize();
        const actionUrl = 'update.php';

        $.post(actionUrl, formData, function (response) {
			alert('更新完了しました。');
			location.reload(); 

        }).fail(function () {
            alert('更新に失敗しました。');
        });
    });
});
