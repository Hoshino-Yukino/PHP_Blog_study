$(document).ready(function() {
    $('.result-table a').on('click', function(event) {
        event.preventDefault(); 

        const url = $(this).attr('href');

        $('.modal').fadeIn();

        $('.modal-body').html('<p>読み込み中...</p>');

        $.get(url, function(data) {
            $('.modal-body').html(data);
        }).fail(function() {
            $('.modal-body').html('<p>コンテンツの読み込みに失敗しました。</p>');
        });
    });

    $(document).on('click', '.delete-link', function(event) {
        event.preventDefault();
        const url = $(this).attr('href');
        $('.modal').fadeIn();
        $('.modal-body').html(`
            <p>本当に削除しますか？</p>
            <button class="confirm-delete" data-url="${url}">はい</button>
        `);
    });
  
    $(document).on('click', '.confirm-delete', function() {
        const url = $(this).data('url');
        $.get(url, function(response) {
            window.location.href = 'https://blog.swqh.online/articles/search.php'
        }).fail(function() {
            alert('削除に失敗しました。');
        });
    });

    $('#update-link').on('click', function(event) {
        event.preventDefault();
        $('.modal').fadeIn();
        $('.modal-body').html('<p>読み込み中...</p>');
        $.get($(this).data('url'), function(data) {
            $('.modal-body').html(data);
        }).fail(function() {
            $('.modal-body').html('<p>コンテンツの読み込みに失敗しました。</p>');
        });
    });

    $(document).on('click', function (event) {
        if ($(event.target).hasClass('modal')) {
            $('.modal').fadeOut();
			$('.modal-body').innerHTML='';
            unlockBodyScroll(); 
        }
    });

});
