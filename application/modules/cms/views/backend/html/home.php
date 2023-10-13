
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>


    <?php

        try {
            CMS_Display::render('widget_top');
            CMS_Display::render('widget_middle');
            CMS_Display::render('widget_bottom');
        } catch (Exception $e) {
            die($e->getTraceAsString());
        }


        ?>







    </section><!-- /.content -->
</div><!-- /.content-wrapper -->