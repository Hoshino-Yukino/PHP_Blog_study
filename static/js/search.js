$(document).ready(function () {
    // 検索ボタンのアニメーション
    $('.search-btn').on('click', function () {
        $(this).text('検索中...').css('opacity', '0.8');
    });

    // ログアウトボタンの確認ダイアログ
    $('.logout-btn').on('click', function (event) {
        if (!confirm('本当にログアウトしますか？')) {
            event.preventDefault();
        }
    });

    // 禁止页面滚动并保持滚动条占位
    const lockBodyScroll = () => {
        const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
        if (scrollBarWidth > 0) {
            $('body').addClass('lock-scroll').css('padding-right', `${scrollBarWidth}px`);
        }
    };
    const unlockBodyScroll = () => {
        $('body').removeClass('lock-scroll').css('padding-right', originalBodyPaddingRight);
    };

    $(document).on('click', '.blog-card', function () {
        const id = $(this).data('id'); 
        lockBodyScroll(); 
        if (id) {
            $('.modal').fadeIn();

            $('.modal-body').html('<p>読み込み中...</p>');

            $.get('read.php?id=' + id, function (data) {
                $('.modal-body').html(data);
            }).fail(function () {
                $('.modal-body').html('<p>コンテンツの読み込みに失敗しました。</p>');
            });
        }
    });

    
    $('#create-link').on('click', function (event) {
        event.preventDefault();
        lockBodyScroll();
        const url = $(this).data('url');

        $('.modal').fadeIn();
        $('.modal-body').html('<p>読み込み中...</p>');

        $.get(url, function (data) {
            $('.modal-body').html(data);

            const turnstileDiv = document.querySelector(".cf-turnstile");
            if (turnstileDiv) {
                turnstile.render(turnstileDiv, {
                    sitekey: "0x4AAAAAAA5RjmcHEOkVY3BE",
                    theme: "light"
                });
            }
        }).fail(function () {
            $('.modal-body').html('<p>コンテンツの読み込みに失敗しました。</p>');
        });
    });

    $(document).on('submit', '.create-form', function (event) {
        event.preventDefault();

        const $submitBtn = $('.submit-btn');
        const formData = $(this).serialize();

        $submitBtn.prop('disabled', true).text('投稿中...').css({
            backgroundColor: '#007bff',
            color: '#fff'
        });

        $.post('create_article.php', formData, function (response) {
            if (response.success) {
                $submitBtn.css('backgroundColor', '#28a745').text('記事を投稿しました！ ID: ' + response.id);
                setTimeout(function () {
                    location.reload(); 
                }, 3000);
            } else {
                $submitBtn.css('backgroundColor', '#dc3545').text('投稿失敗: ' + response.message);
                $submitBtn.prop('disabled', false);
            }
        }, 'json').fail(function () {
            $submitBtn.css('backgroundColor', '#dc3545').text('サーバーエラーが発生しました。');
            $submitBtn.prop('disabled', false);
        });
    });

    $(document).on('click', '.update-link', function (event) {
        event.preventDefault();
        const url = $(this).data('url');

        $('.modal').fadeIn();
        $('.modal-body').html('<p>読み込み中...</p>');

        $.get(url, function (data) {
            $('.modal-body').html(data);

            const turnstileDiv = document.querySelector(".cf-turnstile");
            if (turnstileDiv) {
                turnstile.render(turnstileDiv, {
                    sitekey: "0x4AAAAAAA5RjmcHEOkVY3BE",
                    theme: "light"
                });
            }
        }).fail(function () {
            $('.modal-body').html('<p>コンテンツの読み込みに失敗しました。</p>');
        });
    });

    document.body.addEventListener('change', function (event) {
        if (event.target.name === 'modified') {
            const yoyakuRadio = document.querySelector('input[name="modified"][value="yoyaku"]');
            const timePicker = document.getElementById('timePicker');

            if (yoyakuRadio && yoyakuRadio.checked) {
                timePicker.style.display = 'block';
            } else {
                timePicker.style.display = 'none';
            }
        }
    });

    $(document).on('click', '.user-link', function (event) {
        event.preventDefault();
        lockBodyScroll();
        const url = "../users/update.php";

        $('.modal').fadeIn();
        $('.modal-body').html('<p>読み込み中...</p>');

        $.get(url, function (data) {
            $('.modal-body').html(data);

            const turnstileDiv = document.querySelector(".cf-turnstile");
            if (turnstileDiv) {
                turnstile.render(turnstileDiv, {
                    sitekey: "0x4AAAAAAA5RjmcHEOkVY3BE",
                    theme: "light"
                });
            }
        }).fail(function () {
            $('.modal-body').html('<p>コンテンツの読み込みに失敗しました。</p>');
        });
    });

    $(document).on('submit', '.userUpdateButton', function (event) {
        event.preventDefault();

        const $submitBtn = $('.userUpdateButton');
        const formData = $(this).serialize();


        $.post('../users/update_user.php', formData, function (response) {
            if (response.success) {
                    location.reload(); 
            } else {
                $submitBtn.css('backgroundColor', '#dc3545').text('サーバーエラーが発生しました。');
                $submitBtn.prop('disabled', false);
            }
        }, 'json').fail(function () {
            $submitBtn.css('backgroundColor', '#dc3545').text('サーバーエラーが発生しました。');
            $submitBtn.prop('disabled', false);
        });
    });
    $('.close-btn').on('click', function () {
        $('.modal').fadeOut();
        unlockBodyScroll(); 
    });
    $(document).on('click', function (event) {
        if ($(event.target).hasClass('modal')) {
            $('.modal').fadeOut();
			$('.modal-body').innerHTML='';
            unlockBodyScroll(); 
        }
    });
});

