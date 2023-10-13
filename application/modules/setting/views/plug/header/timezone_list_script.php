<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>

    $("#select_timezone").on("click",function () {

        if($(".timezone_selector .select2-container").hasClass("hidden")){
            $(".timezone_selector .select2-container").removeClass("hidden");
            $(this).addClass('hidden');
        }else {
            $(".timezone_selector .select2-container").addClass("hidden");
            $(this).removeClass('hidden');
        }




        return false;
    });
    //.timezone_selector .select2-container
    $("#select_timezone_options").select2();
    $(".timezone_selector .select2-container").addClass('hidden');

    $("#select_timezone_options").on('change',function () {
        let id = $(this).val();
        if(id === 0){
            $("#select_timezone_options").addClass("hidden");
            $("#select_timezone").removeClass('hidden');
        }else if(id != null){
            document.location.href = '<?=admin_url('setting/edit_timezone?tz=')?>'+id+'&c=<?=base64_encode(current_url())?>';
        }

        return false;
    });

</script>