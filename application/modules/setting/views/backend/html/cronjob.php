<?php


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content">

        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row">

            <div class="col-sm-6">

                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?php echo Translate::sprint("Cronjob Paths"); ?> </b></h3>
                    </div>


                    <div class="box-body">

                        <form id="form" role="form">

                            <div class="form-group">
                                <label><?= Translate::sprint("All Modules") ?></label>
                                <input type="text" class="form-control" placeholder="value" readonly
                                       value="<?= base_url("cronjob.php") ?>">
                                <sub><i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?= _lang("By setting up the main url in cronjob, all modules cronjob will exectue one time") ?></sub>
                            </div>

                            <div class="">
                                <a class="btn btn-primary" target="_blank" href="<?= base_url("cronjob.php") ?>"><?=_lang("Execute manually")?></a>
                            </div>

                            <hr>

                        <?php
                            $modules = FModuleLoader::loadCoreModules();
                            ?>

                        <?php foreach ($modules as $module): ?>

                            <?php if (ModulesChecker::isRegistred($module)): ?>

                                <?php $class = $this->{$module}; ?>

                                <?php if (method_exists($class, 'cron')): ?>
                                        <div class="form-group cmodule ">
                                            <label><?= $module ?></label>
                                            <input type="text" class="form-control" placeholder="value" readonly
                                                   value="<?= site_url("$module/cron") ?>">
                                        </div>
                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endforeach; ?>

                        </form>

                    </div>

                </div>


            </div>

            <div class="col-sm-6">
                <div class="callout callout-info">
                    <h4><i class="mdi mdi-link" aria-hidden="true"></i>&nbsp;&nbsp;
                        <?= Translate::sprint("How to setup cronjob in your server?") ?></h4>
                    <p>
                    <?php
                        echo Translate::sprint("set this command in your cronjob") . " <BR><CODE> /usr/bin/php -q " . FCPATH . "cronjob.php</CODE>";
                        echo '<br><i><u><a target="_blank" href="https://www.youtube.com/watch?v=EW5KRkeFBvE">Tutorial Video</a></u></i>'
                        ?>
                    </p>
                </div>
            </div>



    </section>


</div>




