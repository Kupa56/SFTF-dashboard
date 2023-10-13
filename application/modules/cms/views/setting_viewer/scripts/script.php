<script>

    active('Home_page');

    $(".sub-navigation li a").on('click',function () {
        let href = $(this).attr('href');
        var fixed = href.replace(/#+/g, '');
        active(fixed);
        return false;
    });

    $('.webapp-config-block .form-group .select2').select2();
    $('.webapp-config-block .form-group .colorpicker1').colorpicker();
    $("#btnSaveWebappConfig").on('click', function () {
        let selector = $(this);
        let errors = {};
        let dataSet = {};
        $( ".webapp-config-block .form-control" ).each(function( index ) {
            var $this = $(this);
            if ($this.is("textarea")) {

                let val = $this.val();

                if(val === "")
                    val = $this.text();

                if(val !== ""){
                    dataSet[$this.attr('name')] =  val;
                }else{
                    if($this.is("[required]"))
                        errors[$this.attr('name')] = "empty field!";
                }

            }else{
                let val  = $this.val();
                if(val !== ""){
                    dataSet[$this.attr('name')] =  val;
                }else{
                    if($this.is("[required]"))
                        errors[$this.attr('name')] = "empty field!";
                }
            }
        }).promise().done( function(){
            console.log(dataSet);
            console.log(errors);
            if (Object.keys(errors).length === 0){
                saveConfigData(dataSet,selector);
            }else{
                $('.webapp-config-block .errors').removeClass("hidden");
            }
        } );
        return false;
    });

    function active(tab) {
        $('.sub-navigation-body').addClass("hidden");
        $('.sub-navigation-body#'+tab).removeClass("hidden");
        $('.sub-navigation li').removeClass("active");
        $('.sub-navigation li a[href=#'+tab+']').parent().addClass("active");
    }


</script>

