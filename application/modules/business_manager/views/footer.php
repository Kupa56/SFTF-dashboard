<script src="<?=AdminTemplateManager::assets("business_manager", "js/framework7.min.js")?>"></script>
<script src="<?=AdminTemplateManager::assets("business_manager", "js/app_obf.js")?>"></script>
<!-- jQuery 2.2.3 -->
<script src="<?= adminAssets("plugins/jQuery/jquery-2.2.3.min.js") ?>"></script>
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

<script>

    function handle_device_events(data) {
        if(data.action === "manage_business_options"){


            var options = [];

        <?php if(GroupAccess::isGranted('store',ADD_STORE)): ?>
                options.push({
                    text: '<?=_lang("Create new Store")?>',
                    onClick: function (dialog, e) {
                        dialog.close();
                        document.location.href = "<?=admin_url("business_manager/create_business")?>";
                    }
                });
        <?php endif; ?>
        <?php if(GroupAccess::isGranted('offer',ADD_OFFER)): ?>
                options.push({
                    text: '<?=_lang("Create new Offer")?>',
                    onClick: function (dialog, e) {
                        dialog.close();
                        document.location.href = "<?=admin_url("business_manager/create_offer")?>";
                    }
                });
        <?php endif; ?>
        <?php if(GroupAccess::isGranted('event',ADD_EVENT)): ?>
                options.push({
                    text: '<?=_lang("Create new Event")?>',
                    onClick: function (dialog, e) {
                        dialog.close();
                        document.location.href = "<?=admin_url("business_manager/create_event")?>";
                    }
                });
        <?php endif; ?>


        <?php if(SessionManager::isLogged()): ?>
            options.push({
                text: '<?=_lang("Log out")?>',
                onClick: function (dialog, e) {
                    dialog.close();
                    document.location.href = "<?=admin_url("business_manager/logout")?>";
                }
            });
        <?php endif; ?>



            if(options.length === 0){
                app.dialog.alert("<?=_lang(Messages::USER_ACCOUNT_ISNT_BUSINESS_2)?>","<?=_lang("Business Manager")?>");
                return;
            }


            options.push( {
                text: 'Cancel',
                color: '#000000',
                onClick: function (dialog, e) {
                    dialog.close();
                }
            });


            app.dialog.create({
                title: '<?=_lang("Manage Your Business")?>',
                text: '<?=_lang("Please select an option")?>',
                buttons: options,
                verticalButtons: true,

            }).open();
        }
    }


    $("a.link.clickable").on('click',function () {
        let href = $(this).attr('href');
        document.location.href = href;
        return false;
    });

</script>

<?php
echo AdminTemplateManager::loadScripts();
?>



</body>
</html>