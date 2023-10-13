<?php
$this->load->view("pack/client_view/header-client");

?>

<!-- Content Wrapper. Contains page content -->
<section class="main">

    <div class="my-custom-container">
        <div class="row packs" style="padding: 20px">
        <?php foreach ($packs as $value): if ($value->display > 0) : ?>

                <div class="col-sm-4">
                    <div class="pack-item <?php if ($value->recommended == 1) echo 'pack-recommended' ?>  pack-item-<?= $value->id ?>"
                         style="  <?php if ($value->recommended == 1) echo 'border: 1px solid ' . DASHBOARD_COLOR ?>">
                        <div class="item-header">
                            <h3 class="Montserrat-Regular text-red"><?= _lang($value->name) ?> <br>
                            </h3>
                            <div class="item-option">
                            <?php if ($value->price > 0): ?>
                                <?php

                                    echo "<h3 id='price_per_month_" . $value->id . "'><strong>" . Currency::parseCurrencyFormat($value->price, PAYMENT_CURRENCY) . "</strong></h3>";

                                    $saved_yearly_ammount = ($value->price * 12) - $value->price_yearly;
                                    echo "<h3 class='hidden' id='price_per_year_" . $value->id . "'><strong>" . Currency::parseCurrencyFormat($value->price_yearly, PAYMENT_CURRENCY) . "</strong></h3>";
                                    if ($saved_yearly_ammount > 0)
                                        echo "<h5 class='invisible' id='price_saved_" . $value->id . "'>" . Translate::sprint("You will save") . ": <strong class='text-orange'>" . Currency::parseCurrencyFormat(($saved_yearly_ammount / 12), PAYMENT_CURRENCY) . "</strong> " . Translate::sprint("monthly") . "</h5>";
                                    else
                                        echo "<h5 class='invisible' id='price_saved_" . $value->id . " no-value'>" . Translate::sprint("You will save") . ": <strong class='text-orange'>" . Currency::parseCurrencyFormat(0, PAYMENT_CURRENCY) . "</strong> " . Translate::sprint("monthly") . "</h5>";

                                    ?>
                            <?php else: ?>
                                    <h3><strong><?= Translate::sprint("FREE") ?></strong></h3>
                                    <h5 class="invisible" id="">You will save: <strong class="text-orange">FREE</strong> </h5>
                            <?php endif; ?>
                                <?= Translate::sprint("Duration") ?>: <strong
                                        id="duration_<?= $value->id ?>"><?= Translate::sprint("1 Month") ?></strong>

                            </div>
                        </div>
                        <div class="item-body">

                            <!-- SET OPTIONS -->
                        <?php
                                $fields = UserSettingSubscribe::getFields();
                            ?>


                        <?php foreach ($fields as $key => $field): ?>
                            <?php if ($field['_display'] == 1): ?>
                                    <div class="item-option">
                                        <strong class="Montserrat-Regular"><?= ucfirst(Translate::sprint($field['field_name'])) ?></strong>

                                    <?php

                                        if ($field['field_type'] == UserSettingSubscribeTypes::INT) {

                                            if ($value->{$field['field_name']} == -1)
                                                echo ' <span><i class=\'mdi mdi-check text-green\'></i>&nbsp;&nbsp;' . Translate::sprint("Unlimited") . '</span>';
                                            else if ($value->{$field['field_name']} == 0)
                                                echo '<i class="mdi mdi-close text-red"></i>';
                                            else
                                                echo ' <span><i class=\'mdi mdi-check text-green\'></i>&nbsp;&nbsp;' . $value->{$field['field_name']} . '</span>';

                                        } elseif ($field['field_type'] == UserSettingSubscribeTypes::BOOLEAN) {

                                            if ($value->{$field['field_name']} == 1)
                                                echo ' <span><i class=\'mdi mdi-check text-green\'></i>&nbsp;&nbsp;' . Translate::sprint("Enabled") . '</span>';
                                            else if ($value->{$field['field_name']} == 0)
                                                echo '<span><i class="mdi mdi-close text-red"></i>&nbsp;&nbsp;' . Translate::sprint("Disabled") . '</span>';

                                        }


                                        ?>

                                    </div>

                                <?php

                                        if(intval($key)==4)
                                            break;
                                    ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                            <!-- END SET OPTIONS -->


                        <?php if (count($fields)>5 OR $value->description!="")  : ?>
                                <div class="item-option">
                                    <a href="#" data-toggle="modal"
                                       data-target="#modal-default-<?= md5($value->id) ?>">
                                        <?= Translate::sprint("Show more") ?>
                                    </a>

                                    <!--Popup description-->
                                    <div class="modal fade" id="modal-default-<?= md5($value->id) ?>">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">

                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title Montserrat-Regular"> <?= $value->name ?></h4>
                                                </div>
                                                <div class="modal-body">

                                                    <div class="row margin">

                                                        <div class="col-sm-4">
                                                            <div style="text-align: left">
                                                                <strong class="uppercase title"><?= Translate::sprint("Subscribe Options") ?></strong>

                                                                <?php

                                                                    $fields = UserSettingSubscribe::load();

                                                                    foreach ($fields as $field) {

                                                                        if ($field['_display'] == 1) {

                                                                            echo '- <b>'.ucfirst(Translate::sprint($field['field_name'])).':</b>&nbsp;&nbsp;';

                                                                            if ($field['field_type'] == UserSettingSubscribeTypes::INT) {

                                                                                if ($value->{$field['field_name']} == -1)
                                                                                    echo '<i class=\'mdi mdi-check text-green\'></i>&nbsp;&nbsp;' . Translate::sprint("Unlimited") ;
                                                                                else if ($value->{$field['field_name']} == 0)
                                                                                    echo '<i class="mdi mdi-close text-red"></i></li>';
                                                                                else
                                                                                    echo '&nbsp;&nbsp;<i class=\'mdi mdi-check text-green\'>' . $value->{$field['field_name']}.'</i>' ;

                                                                            } elseif ($field['field_type'] == UserSettingSubscribeTypes::BOOLEAN) {

                                                                                if ($value->{$field['field_name']} == 1)
                                                                                    echo ' <i class=\'mdi mdi-check text-green\'>&nbsp;&nbsp;'. Translate::sprint("Enabled").'</i>';
                                                                                else if ($value->{$field['field_name']} == 0)
                                                                                    echo ' <i class="mdi mdi-close text-red">&nbsp;&nbsp;'.Translate::sprint("Disabled").'</i>';

                                                                            }

                                                                            echo '<br>';



                                                                        }


                                                                    }

                                                                    ?>

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <div style="text-align: left">
                                                                <strong class="uppercase title"><?= Translate::sprint("Description") ?></strong>
                                                                <p class="text-info"> <?= $value->description ?></p>
                                                            </div>
                                                        </div>


                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary btn-flat pull-right"
                                                            data-dismiss="modal"><?= Translate::sprint("OK") ?></button>


                                                </div>
                                            </div>

                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                </div>

                        <?php else: ?>
                                <div class="item-option invisible">
                                    <a href="#">
                                        <?= Translate::sprint("Show more") ?>
                                    </a>
                                </div>
                        <?php endif; ?>



                        </div>
                        <div class="item-footer">

                            <div class="form-group" <?= ($value->price > 0 ? "" : "style='visibility: hidden;'") ?>>
                                <select id="duration_select" data="<?= $value->id ?>" class="select2">
                                <?php foreach ($this->mPack->getDurations() as $key => $duration): ?>
                                        <option value="<?=$duration?>">1 <?= Translate::sprint($key) ?></option>
                                <?php endforeach;?>
                                </select>
                            </div>

                        <?php if (RequestInput::get("req") == "upgrade"): ?>

                            <?php


                                $pack_id = intval($this->mUserBrowser->getData("pack-id"));
                                $pack = $this->mPack->getPack($pack_id);

                                if ($pack != NULL) {
                                    $this->db->where("id", $value->id);
                                    $this->db->where("price >", $pack->price);
                                    $c = $this->db->count_all_results("packmanager");
                                } else {
                                    $c = 1;
                                }


                                ?>

                            <?php if ($value->price == 0 && $this->mPack->havePickedPack()): ?>
                                    <button data-duration="1" data-id="<?= $value->id ?> "
                                            class="btn btn-primary btn-block btn-flat select_pack_<?= $value->id ?> select_pack"
                                            ><?= Translate::sprint("Select") ?></button>

                            <?php else: ?>
                                    <button data-duration="1" data-id="<?= $value->id ?> "
                                            class="btn btn-primary btn-block btn-flat select_pack_<?= $value->id ?> select_pack" <?php if ($c == 0) echo "disabled" ?>><?= ($value->trial_period>0 && SessionManager::getData('trial_period_used')==0)?Translate::sprintf("Try %s day(s) FREE",array($value->trial_period)):_lang("Select") ?></button>
                            <?php endif; ?>


                        <?php else: ?>
                                <button data-duration="1" data-id="<?= $value->id ?> "
                                        class="btn btn-primary btn-block btn-flat select_pack_<?= $value->id ?> select_pack"><?= ($value->trial_period>0 && SessionManager::getData('trial_period_used')==0)?Translate::sprintf("Try for %s day(s)",array($value->trial_period)):_lang("Select") ?></button>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
        <?php endif; endforeach; ?>
        </div>
    </div>

</section>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="<?= adminAssets("bootstrap/js/bootstrap.min.js") ?>"></script>
<script src="<?= adminAssets("plugins/iCheck/icheck.min.js") ?>"></script>
<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>

<?php

    $token = $this->mUserBrowser->setToken("S69BMNSJB8JB");

    ?>

    $(".packs .select_pack").on('click', function () {


        var duration = $(this).attr("data-duration");
        var id = $(this).attr("data-id");

        var selector = $(this);


        $.ajax({
            url: "<?= site_url("ajax/pack/select_pack") ?>",
            data: {
                "pack-duration": duration,
                "pack-id": id,
                "token": "<?=$token?>"
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                console.log(request);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {
                console.log(data);
                NSTemplateUIAnimation.button.success = selector;

                if (data.success === 1) {
                    document.location.href = data.result;
                } else {
                    alert("Error!");
                }
            }
        });

        return false;
    });


    $(".packs #duration_select").select2();
    $(".packs #duration_select").on('change', function () {

        var duration = parseInt($(this).val());
        var id = $(this).attr('data');


        if (duration === 1) {

            $("#price_saved_" + id).addClass('invisible');
            $("#price_per_year_" + id).addClass('hidden');
            $("#price_per_month_" + id).removeClass('hidden');
            $("#duration_" + id).text("<?=Translate::sprint("1 Month")?>");

        } else if (duration === 12) {

            $("#price_saved_" + id).removeClass('invisible');
            $("#price_saved_" + id + ".no-value").addClass('invisible');
            $("#price_per_year_" + id).removeClass('hidden');
            $("#price_per_month_" + id).addClass('hidden');
            $("#duration_" + id).text("<?=Translate::sprint("1 Year")?>");

        }

        $(".select_pack_" + id).attr("data-duration", duration);

        return true;
    });


</script>


<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>

<script>

    var NSTemplateUIAnimation = {

        button: {

            set loading(selector){
                var text  = selector.text().trim();
                selector.attr("disabled",true);
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
            },

            set success(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.html("<i class=\"btn-saving-cart fa fa-check\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
                selector.addClass('bg-green');
                selector.attr("disabled",true);
            },
            set default(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.attr("disabled",false);
            },

            // selector.html('<i class="btn-saving-cart fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;<?=Translate::sprint("Mail Sent")?>&nbsp;&nbsp;');
        },

        buttonWithIcon: {

            set loading(selector){
                var text  = selector.html().trim();
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
            },

            set success(selector) {
                var text  = selector.html().trim();
                selector.html(text);
            },
            set default(selector) {
                var text  = selector.html().trim();
                selector.html(text);
            },

        },


    };

</script>
</body>
</html>
