<script src="<?= adminAssets("plugins/jQueryUI/jquery-ui.js") ?>"></script>

<script>

    let cloned_line = $(".cf-list .first_line").clone(false);
    let first_line = cloned_line.prevObject[0].outerHTML;


    $(".cf-list .first_line .remove").remove();

    $("#add_new_line").on("click",function () {


        first_line = first_line.replace("first_line","");
        $(".cf-list .dd").append(first_line);

        let timestamp = new Date().getUTCMilliseconds();

        $(".cf-list .dd tr:last-child").attr("data-id",timestamp);
        $(".cf-list .dd tr:last-child").addClass("line_"+timestamp);
        $(".cf-list .dd tr:last-child .remove").attr("data-id",timestamp);
        $(".cf-list .dd tr:last-child .required").attr("name","is_required_"+timestamp);

        $(".cf-list .dd tr:last-child").removeClass("hidden");
        $(".cf-list .dd tr:last-child").addClass("edit");

        $(".cf-list .dd tr:last-child .remove").on("click",function () {
            $(".cf-list .line.line_"+$(this).attr("data-id")).animate({"opacity":0},'linear',function () {
                $(this).remove();
            });
            return false;
        });


        return false;
    });


    $( ".dd" ).sortable({
        start: function(e, ui) {

        },
        stop: function() {

        }
    });


    $(".cf-list .dd .remove").on("click",function () {
        $(".cf-list .line.line_"+$(this).attr("data-id")).animate({"opacity":0},'linear',function () {
            $(this).remove();
        });
        return false;
    });



    $("#save-cf").on('click',function () {

        let data = [];
        var order = 1;
        let selector = $(this);

        $( ".line.edit" ).each(function( index ) {

            let selector_id = $(this).attr("data-id");
            let f_type = $("tr.line.line_"+selector_id+" #field_type").val();
            let f_label =$("tr.line.line_"+selector_id+" #field_label").val();
            let f_default =$("tr.line.line_"+selector_id+" #field_default").val();
            let f_is_required = 0;

            if($("tr.line.line_"+selector_id+" input.is_required").is(":checked")){
                f_is_required = 1;
            }

            data.push({
                'type':f_type,
                'label':f_label,
                'default':f_default,
                'required':f_is_required,
                'order':order,
                'step':1,
            });

            order++;


        }).promise().done(function () {

            console.log(data);
            send_data(selector,data);

        });



        return false;
    });



    function send_data(selector,fields) {

        $.ajax({
            url: "<?=  site_url("ajax/cf_manager/edit")?>",
            data: {
                "fields":fields,
                "label":$("#cf_label").val(),
                "id":<?=$cf['id']?>,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";

                NSTemplateUIAnimation.button.default = selector;
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                if (data.success === 1) {
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href = "<?=admin_url("cf_manager/cf_list")?>";
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


    }



</script>