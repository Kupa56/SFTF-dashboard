
<script src="<?=  adminAssets("plugins/select2/select2.full.min.js")?>"></script>

<script>


   $("#refund").on('click',function () {

       var id = $(this).attr('data-id');

       $.ajax({
           type:'post',
           data:{
               id: id
           },
           url:"<?=site_url("ajax/payment/refund")?>",
           dataType: 'json',
           beforeSend: function (xhr) {
               $("#refund").addClass("hidden");
               $("#refund_proccessing").removeClass("hidden");
           },error: function (request, status, error) {
               alert(request.responseText);
               $("#refund").removeClass("hidden");
               $("#refund_proccessing").addClass("hidden");
               console.log(request);
           },
           success: function (data, textStatus, jqXHR) {

               console.log(data);
               if(data.success===1){
                   document.location.reload();
               }else if(data.success===0){
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


       return false;

   });

</script>


