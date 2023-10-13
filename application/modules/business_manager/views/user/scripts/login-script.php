<script>


    $("#login-business .link").on('click', function () {
        let url = $(this).attr('href');
        document.location.href = url;
    });


    $("#login-business #do-login").on('click', function () {

        let email = $("#login-business #email").val();
        let password = $("#login-business #password").val();

        $.ajax({
            url: "<?=  site_url("ajax/business_manager/signIn")?>",
            data: {
                "login": email,
                "password": password,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                app.dialog.preloader('<?=_lang("Connecting...")?>');

            }, error: function (request, status, error) {

                app.dialog.close();
                console.log(request);

                app.dialog.alert("<?=_lang("Technical error")?>", "<?=_lang("Alert")?>");
            },
            success: function (data, textStatus, jqXHR) {

                app.dialog.close();

                console.log(data);
                if (data.success === 1) {
                    app.dialog.alert("<?=_lang("Success")?>", "<?=_lang("Business Manager")?>", function () {
                        document.location.href = "<?=admin_url("business_manager/check")?>"
                    });
                } else if (data.success === 0) {

                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + " - " + data.errors[key] + "\n";
                    }

                    if (errorMsg !== "") {
                        app.dialog.alert(errorMsg, "<?=_lang("Business Manager")?>");
                    }
                } else if (data.success === -1) {

                    let options = [];

                    var message = "";
                    for (let key in data.errors) {
                        message = message + " - " + data.errors[key] + "\n";
                    }

                    if (message === "") {
                        return;
                    }

                    options.push({
                        text: '<?=_lang("Upgrade Now")?>',
                        color: '#000000',
                        onClick: function (dialog, e) {

                            document.location.href = data.url;

                            dialog.close();
                        }
                    });

                    options.push({
                        text: '<?=_lang("Cancel")?>',
                        color: '#000000',
                        onClick: function (dialog, e) {
                            dialog.close();
                        }
                    });

                    app.dialog.create({
                        title: '<?=_lang("Manage Your Business")?>',
                        text: message,
                        buttons: options,
                        verticalButtons: true,

                    }).open();

                }
            }
        });

        return false;
    });


</script>