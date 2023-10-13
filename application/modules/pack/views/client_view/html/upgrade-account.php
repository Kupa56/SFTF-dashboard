<?php
$this->load->view("pack/backend/header-client");
?>





<!-- Content Wrapper. Contains page content -->
<section class="main">

    <div class="my-custom-container">
        <div class="row payment">
            <div class="col-sm-12">
                <div class="alert alert-warning disabled">
                    <h4><i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;<?=Translate::sprint("Upgrade account to business")?></h4>
                    <p><?=Translate::sprint("Sorry your account should be for business, to connect with the dashboard, Check our prices and upgrade it now!")?></p>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                    </div>
                    <div class="col-sm-4">
                        <div class="pay-btn">
                            <a href="<?=site_url('pack/pickpack?req=upgrade')?>" id="pay-now" class="btn btn-primary btn-flat">
                                <u><?=Translate::sprint("Upgrade now!")?></u>
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-4">
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
