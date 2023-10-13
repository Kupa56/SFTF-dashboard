<?php
$this->load->view("payment/client_view/header-client");
?>



<!-- Content Wrapper. Contains page content -->
<section class="main">

    <div class="my-custom-container">
        <div class="row payment">
            <div class="col-sm-12">
                <div class="alert alert-danger disabled">
                    <h4><i class="fa fa-times" aria-hidden="true"></i>&nbsp;&nbsp;<?=Translate::sprint("Payment with error!")?></h4>
                    <p><?=Translate::sprint("Error occurrence the payment")?></p>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="pay-btn">
                            <a href="<?=admin_url()?>" id="pay-now" class="btn btn-default btn-flat">
                                <u><?=Translate::sprint("Cancel")?></u>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?=  adminAssets("bootstrap/js/bootstrap.min.js")?>"></script>

<
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>




</body>
</html>
