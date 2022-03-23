$(function () {

    function auth() {
        $('#response').text('Загрузка...');
        $.ajax({
            url: '/test/api/api.php',
            method: 'post',
            data: $('#form-user').serialize(),
            success: function (response) {
                console.dir(response);
                let data = $.parseJSON(response);
                $('#response').text(data.message);
            },
            complete: function (response) {
                console.dir(response);
            }
        });
        return false;
    }

    function register() {
        $('#response').text('Загрузка...');
        $.ajax({
            url: '/test/api/api.php',
            method: 'post',
            data: $('#form-user').serialize(),
            success: function (response) {
                console.dir(response);
                let data = $.parseJSON(response);
                $('#response').text(data.message);
            },
            complete: function (response) {
                console.dir(response);
            }
        });
        return false;
    }

    $('#send-form-register').click(register);
    $('#send-form-auth').click(auth);

});