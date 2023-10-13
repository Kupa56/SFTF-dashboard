<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>
<script>

    var checked_modules = [];

    $("#check_all").on('click',function () {
        if($(this).prop('checked')){
            $( ".module_check" ).prop('checked',true);
        }else{
            $( ".module_check" ).prop('checked',false);
        }

    });

    function callAaction(action,selector){

        selector.attr('disabled',true);

        checked_modules = [];
        $( ".module_check" ).each(function( index ) {
            if($(this).is(':checked')){
                var data = $(this).attr('data-module');
                checked_modules.push(data);
            }
        });

        setTimeout(function () {

            console.log(checked_modules);
            if(checked_modules.length > 0){


                $.ajax({
                    url:"<?=  site_url("ajax/modules_manager/")?>"+action,
                    data:{
                        'list':checked_modules
                    },
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (xhr) {

                        selector.attr('disabled',true);
                        //NSTemplateUIAnimation.button.default = selector;

                    },error: function (request, status, error) {
                        alert(request.responseText);
                        console.log(request);
                        selector.attr('disabled',false);

                        //NSTemplateUIAnimation.button.default = selector;
                    },
                    success: function (data, textStatus, jqXHR) {

                        if(data.success===1){

                            //NSTemplateUIAnimation.button.success = selector;
                             document.location.reload();
                        }else if(data.success===0){
                            //NSTemplateUIAnimation.button.default = selector;
                            selector.attr('disabled',false);
                            var errorMsg = "";
                            for(var key in data.errors){
                                errorMsg = errorMsg+data.errors[key]+"\n";
                            }
                            if(errorMsg!==""){
                                alert(errorMsg);
                            }
                        }
                    }
                });
            }else{
                selector.attr('disabled',false);
            }
        },1000);



    }

    $("#action").select2();
    $("#action").on("change",function () {
        var id = parseInt($(this).val());

        if(id === 1){
            callAaction("enable_group",$(this));
        }else if(id === 2){
            callAaction("disable_group",$(this));
        }else if(id === 3){
            callAaction("install_group",$(this));
        }else if(id === 4){
            callAaction("uninstall_group",$(this));
        }else if(id === 5){
            callAaction("upgrade_group",$(this));
        }

        return false;
    });

    $(".module_item #m_install").on('click',function () {

        var selector = $(this);
        var module_id = $(this).attr('data-button');
        $.ajax({
            url:"<?=  site_url("ajax/modules_manager/install")?>",
            data:{
                'module_id':module_id
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                onBtnLoading(selector,"<?=Translate::sprint("Installing...")?>");
            },error: function (request, status, error) {
                alert(request.responseText);
                onBtnDefault(selector,"<?=Translate::sprint("Install")?>");
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success===1){

                    onSuccess(selector,"<?=Translate::sprint("Installed")?>");

                }else if(data.success===0){
                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
                    }
                }else if(data.success===-1){

                    onBtnDefault(selector,"<?=Translate::sprint("Upgrade")?>");
                    window[data.callback](module_id,"install");

                }
            }
        });


        return true;
    });

    $(".module_item #m_enable").on('click',function () {

        var selector = $(this);
        var module_id = $(this).attr('data-button');
        $.ajax({
            url:"<?=  site_url("ajax/modules_manager/enable")?>",
            data:{
                'module_id':module_id
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                onBtnLoading(selector,"<?=Translate::sprint("Enabling...")?>");
            },error: function (request, status, error) {
                alert(request.responseText);
                onBtnDefault(selector,"<?=Translate::sprint("Enable")?>");
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success===1){

                    onSuccess(selector,"<?=Translate::sprint("Enabled")?>");

                }else if(data.success===0){

                    onBtnDefault(selector,"<?=Translate::sprint("Enable")?>");

                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
                    }
                }
            }
        });


        return true;
    });

    $(".module_item #m_disable").on('click',function () {

        var selector = $(this);
        var module_id = $(this).attr('data-button');
        $.ajax({
            url:"<?=  site_url("ajax/modules_manager/disable")?>",
            data:{
                'module_id':module_id
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                onBtnLoading(selector,"<?=Translate::sprint("Disabling...")?>");
            },error: function (request, status, error) {
                alert(request.responseText);
                onBtnDefault(selector,"<?=Translate::sprint("Disable")?>");
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success===1){

                    onSuccess(selector,"<?=Translate::sprint("Disabled")?>");

                }else if(data.success===0){

                    onBtnDefault(selector,"<?=Translate::sprint("Disable")?>");

                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
                    }
                }
            }
        });


        return true;
    });

    $(".module_item #m_upgrade").on('click',function () {

        var selector = $(this);
        var module_id = $(this).attr('data-button');
        $.ajax({
            url:"<?=  site_url("ajax/modules_manager/upgrade")?>",
            data:{
                'module_id':module_id
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                onBtnLoading(selector,"<?=Translate::sprint("Upgrading...")?>");
            },error: function (request, status, error) {
                alert(request.responseText);
                onBtnDefault(selector,"<?=Translate::sprint("Upgrade")?>");
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success===1){

                    onSuccess(selector,"<?=Translate::sprint("Upgraded")?>");

                }else if(data.success===0){

                    onBtnDefault(selector,"<?=Translate::sprint("Upgrade")?>");

                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
                    }
                }else if(data.success===-1){

                    onBtnDefault(selector,"<?=Translate::sprint("Upgrade")?>");
                    window[data.callback](module_id,"upgrade");

                }
            }
        });


        return true;
    });

    $(".module_item #m_uninstall").on('click',function () {

        var selector = $(this);
        var module_id = $(this).attr('data-button');
        $.ajax({
            url:"<?=  site_url("ajax/modules_manager/uninstall")?>",
            data:{
                'module_id':module_id
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                onBtnLoading(selector,"<?=Translate::sprint("Uninstalling...")?>");
            },error: function (request, status, error) {
                alert(request.responseText);
                onBtnDefault(selector,"<?=Translate::sprint("Uninstall")?>");
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success===1){

                    onSuccess(selector,"<?=Translate::sprint("Uninstalled")?>");

                }else if(data.success===0){

                    onBtnDefault(selector,"<?=Translate::sprint("Uninstall")?>");

                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
                    }
                }
            }
        });


        return true;
    });


    function onBtnLoading(selector,text) {
        selector.attr('disabled',true);
        selector.html("<i class='fa fa-spinner fa-spin'></i>&nbsp;&nbsp;"+text);
    }

    function onBtnDefault(selector,text) {
        selector.attr('disabled',false);
        selector.html(text);
    }

    function onSuccess(selector,text) {
        selector.attr('disabled',true);
        selector.html("<i class='mdi mdi-check'></i>&nbsp;&nbsp;"+text);
        setTimeout(function () {
            document.location.reload();
        },1500);
    }



</script>


    
