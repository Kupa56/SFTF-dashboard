
<?php  if( $this->mUserBrowser->getData("manager") == 1){?>

    <div class="col-sm-5 messenger">
        <!-- DIRECT CHAT PRIMARY -->
        <div class="box box-solid direct-chat direct-chat-primary">
            <div class="box-header with-border">
                <strong class="box-title">

                </strong>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <!-- Conversations are loaded here -->
                <div class="direct-chat-messages" style="text-align: center;vertical-align: middle;font-size: 270px;opacity: .1;">
                    <!-- Message. Default to the left -->
                    <i class="mdi mdi-inbox-outline"></i>
                </div>
                <!--/.direct-chat-messages-->
                <!-- Contacts are loaded here -->
                <div class="direct-chat-contacts">

                    <!-- /.contatcts-list -->
                </div>
                <!-- /.direct-chat-pane -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer" style="min-height: 79px;">
            </div>
            <!-- /.box-footer-->
        </div>
        <!--/.direct-chat -->
    </div>



<?php } ?>

