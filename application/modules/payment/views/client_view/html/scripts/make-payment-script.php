<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="<?=  adminAssets("bootstrap/js/bootstrap.min.js")?>"></script>
<script src="<?=  adminAssets("plugins/iCheck/icheck.min.js")?>"></script>

<script>

    $('.methods .method').on('click',function () {

        $('.methods .method').removeClass('active').addClass('inactive');
        $(this).addClass('active').removeClass('inactive');

        var data = $(this).attr('data');
        data = parseFloat(data);

        $('#pay-now').attr('method-data',data).removeClass('hidden');

        return true;
    });



    $('#pay-now').on('click',function () {

        var method_id = $(this).attr('method-data');
        method_id = parseInt(method_id);
        var href = $(this).attr('href');

        NSTemplateUIAnimation.button.loading = $(this);

        document.location.href = href+'&mp='+method_id;

        return false;
    });



</script>


<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'iradio_square',
            radioClass: 'iradio_square',
            increaseArea: '20%' // optional
        });
    });
</script>



<script>

    var NSTemplateUIAnimation = {

        button: {

            set loading(selector){
                var text  = selector.text().trim();
                selector.attr("disabled",true);
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
            },

            set success(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.html("<i class=\"btn-saving-cart fa fa-check\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
                selector.addClass('bg-green');
                selector.attr("disabled",true);
            },
            set default(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.attr("disabled",false);
            },

            // selector.html('<i class="btn-saving-cart fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;<?=Translate::sprint("Mail Sent")?>&nbsp;&nbsp;');
        },

        buttonWithIcon: {

            set loading(selector){
                var text  = selector.html().trim();
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
            },

            set success(selector) {
                var text  = selector.html().trim();
                selector.html(text);
            },
            set default(selector) {
                var text  = selector.html().trim();
                selector.html(text);
            },

        },


    };

</script>

