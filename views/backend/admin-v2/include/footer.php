

<?=AdminTemplateManager::loadHTML()?>



<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        Version <?php if (defined("_APP_VERSION")) echo _APP_VERSION; else echo APP_VERSION; ?>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?= date("Y") ?> <a href="<?= site_url("") ?>"><?= APP_NAME ?></a>.</strong> <?= Translate::sprint("All rights reserved.") ?>
</footer>
</div><!-- ./wrapper -->

<!-- jQuery 3.3.1 -->
<script src="<?= adminAssets("plugins/jQuery/jquery-3.3.1.min.js") ?>"></script>
<!-- jQuery 2.1.4 -->
<script src="<?= adminAssets("plugins/jQuery/jQuery-2.1.4.min.js") ?>"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?= adminAssets("bootstrap/js/bootstrap.min.js") ?>"></script>
<!-- AdminLTE App -->
<script src="<?= adminAssets("dist/js/app.min.js") ?>"></script>
<script src="<?= adminAssets("plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js") ?>"></script>

<script>

    //Colorpicker
    var html_editable = "<div class='pull-right'><button class='hidden' id='enable_html'><?=Translate::sprint("Enable HTML editor")?></button></div>";

    var html_enabled = false;
    if( $("#editable-textarea").lenght ){
        $("#editable-textarea").after(html_editable);
    
        if($("#editable-textarea").val() !== ""){
            $('#enable_html').removeClass('hidden');
        }
    
        $("#editable-textarea").focusout(function () {
            var text = $(this).val();
            if(text !== ""){
                $('#enable_html').removeClass('hidden');
            }else {
                $('#enable_html').addClass('hidden');
            }
        });
    
        $("#editable-textarea").keyup(function () {
            var text = $(this).val();
            if(text !== ""){
                $('#enable_html').removeClass('hidden');
            }else {
                $('#enable_html').addClass('hidden');
            }
        });
    }


    $("#enable_html").on('click',function () {

        $(this).addClass('hidden');
        $(this).addClass('hidden');

        if(!html_enabled){

            var text = $("#editable-textarea").val();
            //text = text.replace('\n','<br/><br/>');

            text = text.replace(/(?:\r\n|\r|\n)/g, '<br>');

            $("#editable-textarea").val(text);

            $("#editable-textarea").wysihtml5({
                "image": false,
                "link": false,
                "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": false, //Button which allows you to edit the generated HTML. Default false
                "color": false //Button to change color of font
            });
        }


        html_enabled = true;

        return false;
    });

    /**/
</script>


<script>

<?php
    $link = $this->session->userdata("redirect_to");
    if($link!=""){
        echo "redirect('".$link."')";
        $this->session->set_userdata(array(
            "redirect_to" => ""
        ));
    }

    ?>


    function redirect(url) {
        document.location.href=url;
        //window.open(url, '_target');
        setTimeout(function () {

        },2000);
    }

</script>


<?php
echo AdminTemplateManager::loadScripts();
?>



<script>

    $("#menu-search").removeClass('hidden');

    var last_active_selector = null;

    $( ".sidebar-menu li").each(function( index ) {

        if(!$(this).hasClass('header')){
            var text = $(this).children("a").text();
            text = text.trim().replace(/(<([^>]+)>)/ig,"");
            $(this).attr('data-search',text);
            $(this).attr('data-search-key',text.replace(/ /g,"_"));
            $(this).addClass('parent');
            //$(this).children('ul').attr('data-search-child',text);
            //set text for parents
            if($(this).hasClass('treeview')){
                $(this).find('ul.treeview-menu > li').attr('data-search-parent',text);
                $(this).find('ul.treeview-menu > li').attr('data-search-parent-key',text.replace(/ /g,"_"));
                $(this).find('ul.treeview-menu > li').removeClass('parent');
            }

            if($(this).hasClass('active')){
                last_active_selector = $(this);
            }
        }

    });

    $("#menu-search input[type=text]").keyup(function () {

        var collected_data = {};

        var text = $(this).val();
        if(text !== ""){

            $('.sidebar-menu li').addClass('hidden');
            $('.sidebar-menu li#menu-search').removeClass('hidden');
            $('.sidebar-menu li#reset-search').remove();
            $('#menu-search').after('<li id="reset-search"><a href="#"><i class="mdi mdi-arrow-left"></i>&nbsp;&nbsp;<?=_lang('Back')?></a></li>');

            searchInMenu($(this),text);

        }else {

            $('.sidebar-menu li').removeClass('hidden');
            $('.sidebar-menu li').removeClass('active');
            $('.sidebar-menu li#reset-search').remove();

            if(last_active_selector !== null){
                last_active_selector.addClass('active');
                var attr_key = last_active_selector.attr('data-search-parent-key');
                $('li[data-search-key='+attr_key+']').addClass('active');
            }
        }

        $('.sidebar-menu li#reset-search').on('click',function () {
            $("#menu-search input[type=text]").val('');
            $('.sidebar-menu li').removeClass('hidden');
            $('.sidebar-menu li').removeClass('active');
            $('.sidebar-menu li#reset-search').remove();

            if(last_active_selector !== null){
                last_active_selector.addClass('active');
                var attr_key = last_active_selector.attr('data-search-parent-key');
                $('li[data-search-key='+attr_key+']').addClass('active');
            }

            return false;
        });


    });

    function searchInMenu(selector,query) {

        $( ".sidebar-menu li[data-search]").each(function( index ) {

            var data_search_value = $(this).attr('data-search');
            data_search_value = data_search_value.toLowerCase();
            query = query.toLowerCase();

            var n = data_search_value.search(query);
            if(n>=0){

                var attr_parent_key = $(this).attr('data-search-parent-key');

                $(this).removeClass('hidden');
                $('li[data-search-key='+attr_parent_key+']').removeClass('hidden').addClass('active');
                if($(this).hasClass('treeview')){
                    $(this).addClass('active');
                    $(this).find('ul.treeview-menu > li').removeClass('active');
                    $(this).find('ul.treeview-menu > li').removeClass('hidden');
                }
            }

        });

    }

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

                window.NSTemplateUIAnimationContent  = selector.html().trim();
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;<?=_lang("Loading")?>");

            },

            set success(selector) {

                selector.html( window.NSTemplateUIAnimationContent);
                selector.attr('disabled',true);

            },
            set default(selector) {
                selector.html( window.NSTemplateUIAnimationContent);
            },

        },


    };



</script>




<script>

    $('a.linkAccess').on('click', function () {
        let selector = $(this);
        var url = ($(this).attr('href'));
        //calling the ajax function
        executeUrl(url,selector);
        return false;
    });

    function executeUrl(url,selector) {

        $.ajax({
            type: 'post',
            url: url,
            dataType: 'json',
            data: {source:url},
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.buttonWithIcon.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);
                console.log(request);
                NSTemplateUIAnimation.buttonWithIcon.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                NSTemplateUIAnimation.button.success = selector;

                if (data.success === 1) {
                    NSTemplateUIAnimation.buttonWithIcon.success = selector;
                    document.location.reload();
                } else if (data.success === 0) {
                    NSTemplateUIAnimation.buttonWithIcon.default = selector;

                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + "- "+data.errors[key] + "<br/>";
                    }
                    if(errorMsg!==""){
                        $(".message-error .messages").html(errorMsg);
                        $(".message-error").removeClass("hidden");
                    }

                    $('html, body').animate({
                        scrollTop: $('html, body').offset().top
                    }, 100);
                }
            }

        });

        return false;
    }

</script>


<script>
    var bodyStyles = document.body.style;
    bodyStyles.setProperty('--primary-color', '<?=DASHBOARD_COLOR?>');
</script>


</body>
</html>