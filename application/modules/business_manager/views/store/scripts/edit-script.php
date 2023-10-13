<script async-component="business">

    create_business_setup();

    function create_business_setup() {


        $("#create-business #cancel").on('click',function () {

            $("#create-business").addClass("hidden");
            $("#home-page").removeClass("hidden");

            document.location.href = "<?=admin_url("business_manager/businesses")?>";
            return false;
        });

        $("#create-business #go-step-2").on('click',function () {

            if(!check_step_1_fields()){
                var toastBottom = app.toast.create({
                    text: '<?=_lang("Please select a specific location")?>',
                    closeTimeout: 2000,
                });
                toastBottom.open();
                return false;
            }

            $("#create-business .step-1-form").addClass("hidden");
            $("#create-business .step-2-form").removeClass("hidden");
            $("#create-business .step-3-form").addClass("hidden");
            $("#create-business .step-4-form").addClass("hidden");
            $("#create-business .step-5-form").addClass("hidden");

            business_scroll_top();

            return false;
        });

        $("#create-business #back-step-1").on('click',function () {

            $("#create-business .step-1-form").removeClass("hidden");
            $("#create-business .step-2-form").addClass("hidden");
            $("#create-business .step-3-form").addClass("hidden");
            $("#create-business .step-4-form").addClass("hidden");
            $("#create-business .step-5-form").addClass("hidden");

            business_scroll_top();

            return false;
        });

        $("#create-business #go-step-3").on('click',function () {

            if(!check_step_2_fields()){
                var toastBottom = app.toast.create({
                    text: '<?=_lang("Please fill all required fields")?>',
                    closeTimeout: 2000,
                });
                toastBottom.open();
                return false;
            }

            $("#create-business .step-1-form").addClass("hidden");
            $("#create-business .step-2-form").addClass("hidden");
            $("#create-business .step-3-form").removeClass("hidden");
            $("#create-business .step-4-form").addClass("hidden");
            $("#create-business .step-5-form").addClass("hidden");

            business_scroll_top();

            return false;
        });

        $("#create-business #back-step-2").on('click',function () {

            $("#create-business .step-1-form").addClass("hidden");
            $("#create-business .step-2-form").removeClass("hidden");
            $("#create-business .step-3-form").addClass("hidden");
            $("#create-business .step-4-form").addClass("hidden");
            $("#create-business .step-5-form").addClass("hidden");

            business_scroll_top();

            return false;
        });

        $("#create-business #go-step-4").on('click',function () {

            $("#create-business .step-1-form").addClass("hidden");
            $("#create-business .step-2-form").addClass("hidden");
            $("#create-business .step-3-form").addClass("hidden");
            $("#create-business .step-4-form").removeClass("hidden");
            $("#create-business .step-5-form").addClass("hidden");


            business_scroll_top();

            return false;
        });




        $("#create-business #back-step-3").on('click',function () {

            $("#create-business .step-1-form").addClass("hidden");
            $("#create-business .step-2-form").addClass("hidden");
            $("#create-business .step-3-form").removeClass("hidden");
            $("#create-business .step-4-form").addClass("hidden");
            $("#create-business .step-5-form").addClass("hidden");

            business_scroll_top();

            return false;
        });





        var autocompleteDropdownAjax = app.autocomplete.create({
            inputEl: '#autocomplete-categories',
            openIn: 'dropdown',
            preloader: true, //enable preloader
            /* If we set valueProperty to "id" then input value on select will be set according to this property */
            valueProperty: 'name', //object's "value" property name
            textProperty: 'name', //object's "text" property name
            limit: 20, //limit to 20 results
            dropdownPlaceholderText: '<?=_lang("Search on category")?>',
            source: function (query, render) {
                var autocomplete = this;
                var results = [];
                if (query.length === 0) {
                    render(results);
                    return;
                }
                // Show Preloader
                autocomplete.preloaderShow();

                // Do Ajax request to Autocomplete data
                app.request({
                    url: '<?=site_url("category/ajax/getCategories")?>',
                    method: 'GET',
                    dataType: 'json',
                    //send "query" to server. Useful in case you generate response dynamically
                    data: {
                        query: query,
                    },
                    success: function (data) {
                        // Find matched items
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].name.toLowerCase().indexOf(query.toLowerCase()) >= 0) results.push(data[i]);
                        }
                        // Hide Preoloader
                        autocomplete.preloaderHide();
                        // Render items by passing array with result items
                        render(results);
                    }

                });
            },
            on: {
                change: function (value) {
                    console.log(value);
                    $("#category").val(value[0].id);
                },
            },
        });


        $("#create-business #save-business").on('click', function () {

            let selector = $(this);

            let category = $("#create-business #category").val();
            let name = $("#create-business #name").val();
            let description = $("#create-business #description").val();
            let address = $("#<?=$location_fields_id['address']?>").val();
            let lat = $("#<?=$location_fields_id['lat']?>").val();
            let lng = $("#<?=$location_fields_id['lng']?>").val();
            let phone = $("#create-business #phone").val();
            let video_url = $("#create-business #video_url").val();

            let canChat = $("#create-business input[name='canChat']:checked").val();
            let book = $("#create-business input[name='book']:checked").val();


            console.log(times);

            $.ajax({
                url: "<?=  site_url("ajax/store/edit")?>",
                data: {
                    "times": times,
                    "id": <?=$store_id?>,
                    "video_url": video_url,
                    "name": name,
                    "address": address,
                    "detail": description,
                    "tel": phone,
                    "cat": category,
                    "lat": lat,
                    "lng": lng,

                    "book": book,
                    "canChat": canChat,

                    "images": JSON.stringify(<?=$uploader_variable?>),
                <?php if(ModulesChecker::isRegistred("gallery")){ ?>
                    "gallery": JSON.stringify(<?=$gallery_variable?>)
                <?php } ?>
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                    NSTemplateUIAnimation.button.loading = selector;
                    app.dialog.preloader('<?=_lang("Updating...")?>');


                }, error: function (request, status, error) {
                    alert(request.responseText);

                    app.dialog.close();
                    NSTemplateUIAnimation.button.default = selector;

                    console.log(request);
                },
                success: function (data, textStatus, jqXHR) {

                    app.dialog.close();

                    if (data.success === 1) {

                        NSTemplateUIAnimation.button.success = selector;

                        app.dialog.alert("<?=_lang("Success")?>","<?=_lang("Business Manager")?>",function () {
                            $("#create-business").addClass("hidden");
                            $("#home-page").removeClass("hidden");
                            document.location.href = "<?=admin_url("business_manager/businesses")?>"
                        });

                    } else if (data.success === 0) {

                        console.log(data.errors);

                        NSTemplateUIAnimation.button.default = selector;
                        var errorMsg = "";

                        for (var key in data.errors) {
                            errorMsg = errorMsg + data.errors[key] + "\n";
                        }
                        if (errorMsg !== "") {
                            app.dialog.alert(errorMsg,"<?=_lang("Business Manager")?>",function () {
                                business_form_errors(data.errors);
                            });
                        }
                    }
                }
            });

            return false;

        });


    }


    function business_form_errors(errors) {

        for (var key in errors) {
            if (key === "address" || key === "latitude" || key === "longitude") {
                $("#create-business .step-1-form").removeClass("hidden");
                $("#create-business .step-2-form").addClass("hidden");
                $("#create-business .step-3-form").addClass("hidden");
            }else if (key === "category_id" || key === "name" || key === "telephone" || key === "detail" ) {
                $("#create-business .step-1-form").addClass("hidden");
                $("#create-business .step-2-form").removeClass("hidden");
                $("#create-business .step-3-form").addClass("hidden");
            }
        }
    }

    function re_init_business_form() {

        $("#create-business .step-1-form").removeClass("hidden");
        $("#create-business .step-2-form").addClass("hidden");
        $("#create-business .step-3-form").addClass("hidden");

        $("#create-business #category").val("");
        $("#create-business #autocomplete-categories").val("");
        $("#create-business #name").val("");
        $("#create-business #description").val("");
        $("#create-business #phone").val("");


        $('#create-business .image-previews').each(function( index ) {
            var nameDir = $(this).attr("data-id");
            //$("#create-business .image-previews .image-uploaded.item_" + nameDir).fadeOut('linear');

        });

    }

    function business_scroll_top() {
        $('html, body').animate({
            scrollTop: $('html, body').offset().top
        }, 100);
    }

    function check_step_1_fields() {

        let address = $("#<?=$location_fields_id['address']?>").val();
        let lat = $("#<?=$location_fields_id['lat']?>").val();
        let lng = $("#<?=$location_fields_id['lng']?>").val();

        if(address === "" || lat === "" || lng === ""){
            return false;
        }

        return true;
    }


    function check_step_2_fields() {

        let category = $("#create-business #category").val();
        let name = $("#create-business #name").val();
        let description = $("#create-business #description").val();

        if(category === "" || name === "" || description === ""){
            return false;
        }

        return true;
    }

    (function () {
        // inject ourself into the window.alert and window.confirm globals
        alert = function (msg) {
            app.dialog.alert(msg,"<?=_lang("Alert!")?>");
        };

    }());


</script>