<script src="<?= adminAssets("plugins/jQueryUI/jquery-ui.js") ?>"></script>


<script>

    $(".select2").select2();

    $('#grp-menu-container').sortable({
        start: function(e, ui) {

        },
        stop: function() {
            reload_menu_data()
        }
    });


    $('#grp-menu-container .group tbody').sortable({
        start: function(e, ui) {
            $(this).addClass('dashed-border');
        },
        stop: function() {
            reload_menu_data();
            $(this).removeClass('dashed-border');
        }
    });


    $('.create-new-grp-menu').on('click',function () {
        $("#modal-create-group").modal('show');
        $("#modal-create-group #parent_id").val(0);
        return false;
    });


    $('.add-sub-menu').on('click',function () {
        let parent_id = parseInt($(this).attr('data-id'));
        $("#modal-create-group").modal('show');
        $("#modal-create-group #parent_id").val(parent_id);
        return false;
    });


    $('#modal-create-group .pv-options-selector').on('change',function () {

        let option = parseInt($(this).val());
        $('#modal-create-group .pv-selector').addClass("hidden");

        if(option === 1){
            $('#modal-create-group .pv-selector.page-selector').removeClass("hidden");
        }else if(option === 2){
            $('#modal-create-group .pv-selector.url-editor').removeClass("hidden");
        }else if(option === 3){

        }

    });

    $('#modal-update-group .pv-options-selector').on('change',function () {

        let option = parseInt($(this).val());
        $('#modal-update-group .pv-selector').addClass("hidden");

        if(option === 1){
            $('#modal-update-group .pv-selector.page-selector').removeClass("hidden");
        }else if(option === 2){
            $('#modal-update-group .pv-selector.url-editor').removeClass("hidden");
        }else if(option === 3){

        }

    });


    $('#modal-create-group #create').on('click',function () {

        let selector = $(this);


        $.ajax({
            url: "<?=  site_url("ajax/cms/addMenu")?>",
            data: {

                "parent_id": parseInt($("#modal-create-group #parent_id").val()),
                "title": $('#modal-create-group #title').val(),

                "option": $('#modal-create-group .pv-options-selector').val(),
                "page": $('#modal-create-group .page-selector .pv-page-selector').val(),
                "ex_url": $('#modal-create-group .url-editor .ex_url').val(),
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = selector;
            },
            error: function (request, status, error) {
                console.log(request);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                if (data.success === 1) {

                    $("#modal-create-group").modal('hide');
                    NSTemplateUIAnimation.button.default = selector;
                    document.location.reload();

                } else if (data.success === 0) {

                    NSTemplateUIAnimation.button.default = selector;

                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + "<br/>";
                    }
                    if (errorMsg !== "") {
                        NSAlertManager.simple_alert.request = errorMsg;
                    }
                }
            }
        });

        return false;
    });

    $('#modal-update-group #update').on('click',function () {

        let selector = $(this);
        let menu_id = parseInt($(this).attr('data-id'));

        if(menu_id === 0){
            $("#modal-update-group").modal('hide');
            return ;
        }


        $.ajax({
            url: "<?=  site_url("ajax/cms/updateMenu")?>",
            data: {

                "id": parseInt($(this).attr('data-id')),
                "parent_id": parseInt($("#modal-update-group #parent_id").val()),
                "title": $('#modal-update-group #title').val(),

                "option": $('#modal-update-group .pv-options-selector').val(),
                "page": $('#modal-update-group .page-selector .pv-page-selector').val(),
                "ex_url": $('#modal-update-group .url-editor .ex_url').val(),

            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = selector;
            },
            error: function (request, status, error) {
                console.log(request);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                if (data.success === 1) {

                    $("#modal-update-group").modal('hide');
                    NSTemplateUIAnimation.button.default = selector;
                    document.location.reload();

                } else if (data.success === 0) {

                    NSTemplateUIAnimation.button.default = selector;

                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + "<br/>";
                    }
                    if (errorMsg !== "") {
                        NSAlertManager.simple_alert.request = errorMsg;
                    }
                }
            }
        });

        return false;
    });


    function re_init_update_popup(){

        $("#modal-update-group .pv-options-selector").val(0).trigger("change");
        $("#modal-update-group .pv-page-selector").val(0).trigger("change");
        $("#modal-update-group .ex_url").val("");

        $("#modal-create-group .pv-options-selector").val(0).trigger("change");
        $("#modal-create-group .pv-page-selector").val(0).trigger("change");
        $("#modal-create-group .ex_url").val("");

    }


    $('body').delegate('.update-menu','click',function(){

        let id = parseInt($(this).attr('data-id'));
        let title  = $(".menu-"+id+" .menu-"+id+"-title").val();

        let option  = $(".menu-"+id+" .menu-"+id+"-option").val();
        let page  = $(".menu-"+id+" .menu-"+id+"-page").val();
        let ex_url  = $(".menu-"+id+" .menu-"+id+"-ex_url").val();
        let parent_id  = $(".menu-"+id+" .menu-"+id+"-parent_id").val();


        $("#modal-update-group .pv-options-selector").val(option).trigger("change");
        $("#modal-update-group .pv-page-selector").val(page).trigger("change");
        $("#modal-update-group .ex_url").val(ex_url);
        $("#modal-update-group #parent_id").val(parent_id);

        $("#modal-update-group #title").val(title);
        $("#modal-update-group #update").attr("data-id",id);

        $("#modal-update-group").modal("show");


        return false;
    });




    $('body').delegate('#grp-menu-container .remove-menu, .remove-sub-menu','click',function(){

        let id = parseInt($(this).attr('data-id'));

        NSAlertManager.alert.request = function (modal) {
            $.ajax({
                url:"<?=site_url("cms/ajax/removeMenu")?>",
                data: {
                    "id":id
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                    modal("beforeSend",xhr);

                }, error: function (request, status, error) {

                    modal("error",request);
                    console.log(request);

                },
                success: function (data, textStatus, jqXHR) {

                    modal("success",data,function (success) {
                        document.location.reload();
                    });

                }
            });
        };

        return false;
    });



   function reload_menu_data() {

       let menu_data = [];
       let order = 0;

       /*
       * Start getting group orders
        */
       $( "#grp-menu-container .group" ).each(function( index ) {

               let grp_id = $(this).attr('data-id');

               menu_data.push({
                   'menu_id': grp_id,
                   'order': order,
                   'parent_id': 0,
               });

           /*
          * Start getting option orders
           */

               $( "#grp-menu-container .menu-"+grp_id+" .menu" ).each(function( index ) {

                   let opt_id = $(this).attr('data-id');

                   order++;

                   menu_data.push({
                       'menu_id': opt_id,
                       'order': order,
                       'parent_id': grp_id,
                   });



               }).promise().done(function () {

               });


           order++;


       }).promise().done(function () {

           console.log("grp finished");
           console.log(menu_data);

           upload_new_orders_list(menu_data);
       });

   }
   
   function upload_new_orders_list(list) {

       $.ajax({
           url:"<?=site_url("cms/ajax/re_order_menu")?>",
           data: {
               "list":list
           },
           dataType: 'json',
           type: 'POST',
           beforeSend: function (xhr) {


           }, error: function (request, status, error) {


               console.log(request);

           },
           success: function (data, textStatus, jqXHR) {

               console.log(data);


           }
       });

   }

</script>