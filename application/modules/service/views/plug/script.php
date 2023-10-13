<script src="<?= adminAssets("plugins/jQueryUI/jquery-ui.js") ?>"></script>


<script>


    $('#grp-service-container').sortable({
        start: function(e, ui) {

        },
        stop: function() {
            reload_service_data()
        }
    });


    $('#grp-service-container .group tbody').sortable({
        start: function(e, ui) {
            $(this).addClass('dashed-border');
        },
        stop: function() {
            reload_service_data();
            $(this).removeClass('dashed-border');
        }
    });



    $('.store-service .create-new-grp-service').on('click',function () {
        $("#modal-create-group").modal('show');
        return false;
    });

    $('.pv-options-selector').select2();



    $('#modal-create-group #create').on('click',function () {

        let selector = $(this);

        $.ajax({
            url: "<?=  site_url("ajax/service/createGroup")?>",
            data: {
                "store_id": <?=$id?>,
                "label": $('#modal-create-group #label').val(),
                "option_type": $('#modal-create-group #option_type').val(),
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
                    $('#grp-service-container').append(data.result);

                    reload_service_data();

                    NSTemplateUIAnimation.button.default = selector;

                    $('#modal-create-group #label').val('');

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
        let service_id = parseInt($(this).attr('data-id'));

        if(service_id === 0){
            $("#modal-update-group").modal('hide');
            return ;
        }

        $.ajax({
            url: "<?=  site_url("ajax/service/updateGroup")?>",
            data: {
                "store_id": <?=$id?>,
                "option_id": service_id,
                "label": $('#modal-update-group #label').val(),
                "option_type": $('#modal-update-group #option_type').val(),
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

                    $('#grp-service-container .group-'+service_id).remove();
                    $('#grp-service-container').append(data.result);

                    reload_service_data();

                    NSTemplateUIAnimation.button.default = selector;

                    $('#modal-create-group #label').val('');

                    $('#modal-update-group').modal('hide');

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


    $('body').delegate('.store-service .add-option','click',function(){

            $("#modal-create-option").modal('show');
            let id = parseInt($(this).attr('data-id'));
            $('#modal-create-option #create').attr('data-id',id);

        return false;
    });

    $('body').delegate('.store-service .update-grp','click',function(){

        let id = parseInt($(this).attr('data-id'));
        let title  = $(".group-"+id+" .grp-"+id+"-label").val();

        $("#modal-update-group").modal("show");
        $("#modal-update-group #label").val(title);
        $("#modal-update-group #update").attr("data-id",id);

        return false;
    });

    $('#modal-create-option #create').on('click',function () {

        let service_id = parseInt($(this).attr('data-id'));
        let selector = $(this);

        $.ajax({
            url: "<?=  site_url("ajax/service/createOption")?>",
            data: {
                "store_id": <?=$id?>,
                "option_name": $('#modal-create-option #option_name').val(),
                "option_price": $('#modal-create-option #option_price').val(),
                "option_description": $('#modal-create-option #option_description').val(),
                "service_id": service_id,
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

                console.log(data);

                if (data.success === 1) {

                    $("#modal-create-option").modal('hide');
                    $('#grp-service-container .group-'+service_id+" tbody").append(data.result);

                    reload_service_data();

                    NSTemplateUIAnimation.button.default = selector;

                    $('#modal-create-option #option_name').val('');
                    $('#modal-create-option #option_price').val('');
                    $('#modal-create-option #option_description').val('');

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

    $('body').delegate('.store-service .update-opt','click',function(){

        let id = parseInt($(this).attr('data-id'));
        let title  = $("tr.opt-"+id+" .opt-"+id+"-title").val();
        let description  = $("tr.opt-"+id+" .opt-"+id+"-description").val();

        $("#modal-update-option").modal("show");

        $("#modal-update-option #option_name").val(title);
        $("#modal-update-option #option_description").val(description);

        $("#modal-update-option #update").attr("data-id",id);

        return false;
    });


    $('#modal-update-option #update').on('click',function () {

        let service_id = parseInt($(this).attr('data-id'));
        let selector = $(this);

        if(service_id === 0){
            $("#modal-update-option").modal('hide');
            return ;
        }

        $.ajax({
            url: "<?=  site_url("ajax/service/updateOption")?>",
            data: {
                "store_id": <?=$id?>,
                "option_name": $('#modal-update-option #option_name').val(),
                "option_price": $('#modal-update-option #option_price').val(),
                "option_description": $('#modal-update-option #option_description').val(),
                "option_id": service_id,
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

                console.log(data);

                if (data.success === 1) {

                    $('#modal-update-option #update').attr('data-id',0);
                    $("#modal-update-option").modal('hide');


                    $('#grp-service-container tr.opt-'+service_id+' span').html(
                        $('#modal-update-option #option_name').val()
                    );


                    reload_service_data();

                    NSTemplateUIAnimation.button.default = selector;

                    $('#modal-update-option #option_name').val('');
                    $('#modal-update-option #option_price').val('');
                    $('#modal-update-option #option_description').val('');

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

    $('body').delegate('.store-service .remove-grp','click',function(){

        let id = parseInt($(this).attr('data-id'));

        $("#fmodal-default").modal("show");
        $("#fmodal-default #apply_confirm").attr("data-id",id).attr("data-type","grp");

        return false;
    });

    $('body').delegate('.store-service .remove-opt','click',function(){

        let id = parseInt($(this).attr('data-id'));

        $("#fmodal-default").modal("show");
        $("#fmodal-default #apply_confirm").attr("data-id",id).attr("data-type","opt");

        return false;
    });




    $('body').delegate("#fmodal-default #apply_confirm","click",function () {

        let selector = $(this);
        let id = parseInt($(this).attr('data-id'));
        let type = $(this).attr('data-type');



        $.ajax({
            url:"<?=site_url("service/ajax/removeService")?>",
            data: {
                "service_id":id
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {

                NSTemplateUIAnimation.button.default = selector;

                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                NSTemplateUIAnimation.button.default = selector;


                if(data.success === 1)
                if(type === "grp"){

                    $("#fmodal-default").modal("hide");
                    $("#fmodal-default #apply_confirm").attr("data-id",0).attr("data-type","");
                    $('.store-service .group-'+id).attr('removed-id',id).hide();


                }else if(type === "opt"){

                    $("#fmodal-default").modal("hide");
                    $("#fmodal-default #apply_confirm").attr("data-id",0).attr("data-type","");
                    $('.store-service tr.opt-'+id).attr('removed-id',id).hide();

                }

            }
        });

        return false;
    });




   function reload_service_data() {

       let service_data = [];
       let order = 0;

       /*
       * Start getting group orders
        */
       $( ".service-list .group" ).each(function( index ) {

               let grp_id = $(this).attr('data-id');

               service_data.push({
                   'service_id': grp_id,
                   'order': order,
                   'parent_id': 0,
               });

           /*
          * Start getting option orders
           */


               $( ".service-list .group-"+grp_id+" tbody .opt" ).each(function( index ) {

                   let opt_id = $(this).attr('data-id');

                   order++;

                   service_data.push({
                       'service_id': opt_id,
                       'order': order,
                       'parent_id': grp_id,
                   });




               }).promise().done(function () {

                   console.log("opt finished");
                   console.log(service_data);
               });


           order++;


       }).promise().done(function () {

           console.log("grp finished");
           console.log(service_data);

           upload_new_orders_list(service_data);
       });

   }
   
   function upload_new_orders_list(list) {

       $.ajax({
           url:"<?=site_url("service/ajax/re_order_list")?>",
           data: {
               "store_id":<?=$id?>,
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