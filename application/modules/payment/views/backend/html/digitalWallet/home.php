
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
                <?php $this->load->view(AdminPanel::TemplatePath."/include/messages");?>
            </div>
        </div>
        <div class="form">
            <?php $this->load->view("backend/html/digitalWallet/form");?>
        </div>
        <div class=" transactions">
            <?php $this->load->view("backend/html/digitalWallet/transactions");?>
        </div>
    </section>
</div>
