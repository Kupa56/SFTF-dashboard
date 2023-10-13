<?php

$logsFiles =  DateTimeZone::listIdentifiers(DateTimeZone::ALL);


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content">

        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages");?>
            </div>

        </div>

        <div class="row">

            <div class="col-sm-6">
                <div class="callout callout-info">
                    <h4><i class="mdi mdi-link" aria-hidden="true"></i>&nbsp;&nbsp;
                        <?=Translate::sprint("What is the deep linking?","")?>!</h4>
                    <p>
                        <?=Translate::sprint("DeepLinkingIntro")?>
                    </p>
                </div>
            </div>

            <div class="col-sm-6">

                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?php echo Translate::sprint("Deep Linking Configuration"); ?> </b></h3>
                    </div>


                    <div class="box-body">

                        <form id="form" role="form">

                            <div class="form-group">
                                <label><?=Translate::sprint("Host Name")?></label>
                                <input type="text" class="form-control" placeholder="value" disabled value="<?=$host?>">
                            </div>

                            <div class="form-group">
                                <label><?=Translate::sprint("Path Prefix for store")?></label>
                                <input type="text" class="form-control" placeholder="value" disabled value="<?=$pstore?>">
                            </div>

                            <div class="form-group">
                                <label><?=Translate::sprint("Path Prefix for product")?></label>
                                <input type="text" class="form-control" placeholder="value" disabled value="<?=$pproduct?>">
                            </div>


                            <div class="form-group">
                                <label><?=Translate::sprint("Path Prefix for event")?></label>
                                <input type="text" class="form-control" placeholder="value" disabled value="<?=$pevent?>">
                            </div>


                        </form>

                    </div>

                </div>



            </div>



    </section>



</div>




