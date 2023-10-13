<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content" id="#messages-module">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages");?>
            </div>

        </div>


        <div class="row">
            <div class="col-md-7">

                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?=Translate::sprint("Inbox")?></b></h3>

                        <div class="pull-right">
                            <a href="#" id="reload-inbox"><i class="mdi mdi-refresh"></i> </a>
                        </div>
                    </div>

                    <div class="box-body discussions-box">

                        <table id="list-discussions" class="table table-bordered table-hover">
                            <tbody id="discussion-list">

                            </tbody>
                        </table>


                    </div>

                    <div id="pagination" class="box-footer clearfix">

                    </div>

                    <div class="overlay inbox-loading">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>


        <?php


                if(!empty($userData))
                    $this->load->view("backend/html/messenger");
                else
                    $this->load->view("backend/html/empty_messeneger");

            ?>
        </div>

    </section>



</div>


<?php


$script = $this->load->view("messenger/backend/html/scripts/discussions-script",NULL,TRUE);
AdminTemplateManager::addScript($script);

