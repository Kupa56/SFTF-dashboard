<div class="col-sm-12">
    <div class="box box-solid">
        <div class="box-header">
            <div class="box-title">
                <strong><?= Translate::sprint("Coupon Config") ?></strong>
            </div>
        </div>

        <div class="box-body">

            <div class="row">
                <div class="form-group col-sm-6">
                    <label><?=Translate::sprint("Coupon type")?></label>
                    <select id="offer_coupon_config_type" class="select2 form-control">
                        <option value="<?=Qrcoupon::COUPON_DISABLED?>" <?=$offer['coupon_config']==Qrcoupon::COUPON_DISABLED?"selected":""?>><?= Translate::sprint(Qrcoupon::COUPON_DISABLED) ?></option>
                        <option value="<?=Qrcoupon::COUPON_LIMITED?>" <?=$offer['coupon_config']==Qrcoupon::COUPON_LIMITED?"selected":""?>><?= Translate::sprint(Qrcoupon::COUPON_LIMITED) ?></option>
                        <option value="<?=Qrcoupon::COUPON_UNLIMITED?>" <?=$offer['coupon_config']==Qrcoupon::COUPON_UNLIMITED?"selected":""?>><?= Translate::sprint(Qrcoupon::COUPON_UNLIMITED) ?></option>
                    </select>
                </div>

                <div class="form-group col-sm-6">
                    <label><?= Translate::sprint("Value") ?></label>
                    <div class="form-group">
                        <input type="number" class="form-control" id="offer_coupon_config_limit"
                               value="<?=$offer['coupon_config']!=Qrcoupon::COUPON_DISABLED?$offer['coupon_redeem_limit']:0?>" <?=$offer['coupon_config']==Qrcoupon::COUPON_DISABLED?"disabled":""?>>
                    </div>
                </div>

                <div class="form-group col-sm-12">
                    <label><?= Translate::sprint("Coupon code") ?></label>
                    <div class="form-group">
                        <input type="text" class="form-control" id="offer_coupon_code"
                               value="<?=trim($offer['coupon_code'])!=""?$offer['coupon_code']:coupon::generate(6, "", "", true, true, false, true)?>" <?=$offer['coupon_config']==Qrcoupon::COUPON_DISABLED?"disabled":""?>>
                    </div>
                </div>
            </div>

            <p class="text-blue"><?=_lang("This feature allows users to acquire and save coupons within the app. If you set a limited coupon, the value will decrease after each redemption.<br>To view who has redeemed coupons from offers, go to 'Manage coupons'.")?></p>


        </div>

    </div>
</div>