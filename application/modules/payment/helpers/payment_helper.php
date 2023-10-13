<?php


class TaxManager
{

    private static $disabled_modules = array();

    public static function disable($module)
    {
        if (!isset(self::$disabled_modules[$module])) {
            self::$disabled_modules[$module] = $module;
        }
    }

    public static function isDisabled($module)
    {

        if (isset(self::$disabled_modules[$module])) {
            return TRUE;
        }

        return FALSE;
    }


}

class PaymentStatus
{


    const PENDING = 0;
    const CONFIRMED = 1;
    const DENIED = -1;


}


class PaymentsProvider
{

    private static $codes = array(
        "paypal" => self::PAYPAL_ID,
        "stripe" => self::STRIPE_ID,
        "cod" => self::COD_ID,
        "wallet" => self::WALLET_ID,
        "razorpay" => self::RAZORPAY_ID,
        "flutterwave" => self::FLUTTERWAVE,
        "hyperpay" => self::HYPERPAY,
        "paytm" => self::PAYTM,
        "transferBank" => self::BANK_TRANSFER,
        "paystack" => self::PAY_STACK,
        "mercadopago" => self::MERCADO_PAGO,
        "my-coolpay" => self::MY_COOLPAY,
    );


    const  PAYPAL_ID = 1;
    const  STRIPE_ID = 2;
    const  COD_ID = 3;
    const  WALLET_ID = 5;
    const  RAZORPAY_ID = 6;
    const  FLUTTERWAVE = 7;
    const  HYPERPAY = 8;
    const  PAYTM = 9;
    const  APPLE_PAY = 10012;
    const  BANK_TRANSFER = 10013;
    const  PAY_STACK = 10014;
    const  MERCADO_PAGO = 10015;

    const  MY_COOLPAY = 100187;


    private static $payments = array();
    private static $redirections = array();
    private static $success_callbacks = array();
    private static $error_callbacks = array();

    public static function findKeyById($id){
       foreach (self::$codes as $name => $code){
           if($code == $id){
               return ($name);
           }
       }
       return "";
    }

    public static function findIdByKey($name){
        foreach (self::$codes as $key => $code){
            if($key == $name){
                return ucfirst($code);
            }
        }

        return "";
    }

    /**
     * @return array
     */

    public static function updatePayments($module,$payments)
    {
        if (isset(self::$payments[$module])) {
            self::$payments[$module] = $payments;
        }else{
            die("The module <$module> doesn't has registered payments");
        }
    }

    public static function getPayments($module)
    {
        $list = array();

        if (isset(self::$payments[$module])) {

            foreach (self::$payments[$module] as $m => $p) {
                if (self::isEnabled($p['id'])) {
                    $list[] = $p;
                }
            }

            return $list;

        } else {
            return array();
        }
    }

    public static function excludePayments($module,$excludeId=array())
    {

        if (isset(self::$payments[$module])) {
            foreach (self::$payments[$module] as $ke => $payment){
                if(in_array($payment["id"],$excludeId) ) {
                    unset(self::$payments[$module][$ke]);
                }
            }

        }else{
            throw new Exception("The module <$module> hasn't registered payments");
        }

    }


    public static function getDefault($module="default")
    {
        $list = array();

        if (isset(self::$payments[$module])) {

            foreach (self::$payments[$module] as $m => $p) {
                if (self::isEnabled($p['id'])) {
                    $list[] = $p;
                }
            }

            return $list;

        } else {
            return array();
        }
    }


    public static function getAll()
    {
        return self::$payments;
    }

    private static $replaced = array();

    public static function provide($module, $payments, $redirection = "", $payment_callback_success = "", $payment_callback_error = "")
    {

        if (isset(self::$replaced[$module]))
            return;

        if (!isset(self::$payments[$module])) {
            self::$payments[$module] = $payments;

            $ctx = &get_instance();

            foreach (self::$payments[$module] as $k => $p) {
                if (defined('DEFAULT_TAX') AND DEFAULT_TAX > 0) {
                    $p["taxes"] = $tax = $ctx->mTaxModel->getTax(DEFAULT_TAX);
                } elseif (defined('DEFAULT_TAX') AND DEFAULT_TAX == -2) {
                    $p["taxes"] = json_decode(MULTI_TAXES, JSON_OBJECT_AS_ARRAY);
                }
                self::$payments[$module][$k] = $p;
            }


            self::$redirections[$module] = $redirection;
            self::$success_callbacks[$module] = $payment_callback_success;
            self::$error_callbacks[$module] = $payment_callback_error;
        }
    }


    public static function plug_payment_method($module,$plug_payment, $first = false)
    {

        $payments = self::$payments[$module];

        if ($first) {
            $np[] = $plug_payment;
        }

        foreach ($payments as $p) {
            $np[] = $p;
        }

        if (!$first) {
            $np[] = $plug_payment;
        }

        self::$payments[$module] = $np;

    }

    public static function replace($module, $payments, $redirection = "", $payment_callback_success = "", $payment_callback_error = "")
    {

        if (!isset(self::$payments[$module])) {
            self::$payments[$module] = $payments;

            $ctx = &get_instance();

            foreach (self::$payments[$module] as $k => $p) {
                if (defined('DEFAULT_TAX') AND DEFAULT_TAX > 0) {
                    $p["taxes"] = $tax = $ctx->mTaxModel->getTax(DEFAULT_TAX);
                } elseif (defined('DEFAULT_TAX') AND DEFAULT_TAX == -2) {
                    $p["taxes"] = json_decode(MULTI_TAXES, JSON_OBJECT_AS_ARRAY);
                }
                self::$payments[$module][$k] = $p;
            }


            self::$redirections[$module] = $redirection;
            self::$success_callbacks[$module] = $payment_callback_success;
            self::$error_callbacks[$module] = $payment_callback_error;

            self::$replaced[$module] = $module;
        }
    }


    public static function getModules()
    {

        $payment = PaymentsProvider::getAll();
        $list = array();

        foreach ($payment as $modules) {

            foreach ($modules as $provided_payments) {
                if (!isset($list[$provided_payments['id']])) {
                    $list[$provided_payments['id']] = array(
                        'payment' => $provided_payments['payment'],
                        'image' => $provided_payments['image'],
                        'id' => $provided_payments['id'],
                    );
                }
            }
        }

        return $list;
    }


    public static function isProvided($module, $payment)
    {

        if (isset(self::$payments[$module])) {
            if (self::isEnabled($payment))
                foreach (self::$payments[$module] as $m => $p) {
                    if (isset($p['payment']) && ($p['payment'] == $payment OR $p['id'] == $payment))
                        return TRUE;
                }
        }

        return FALSE;
    }


    public static function isEnabled($payment)
    {

        //METHOD_PAYMENTS_ENABLED_LIST
        $list = ConfigManager::getValue('METHOD_PAYMENTS_ENABLED_LIST');
        $list = json_decode($list, JSON_OBJECT_AS_ARRAY);

        if (in_array($payment, $list))
            return TRUE;

        if (!is_numeric($payment) && is_string($payment)) {
            if (isset(self::$codes[$payment]))
                $payment = self::$codes[$payment];
        }

        if (!empty($list) && in_array($payment, $list))
            return TRUE;


        return FALSE;
    }

    public static function getRedirection($module)
    {

        if (isset(self::$redirections[$module])) {
            return self::$redirections[$module];
        }

        return FALSE;
    }

    public static function getSuccessCallback($module)
    {

        if (isset(self::$success_callbacks[$module])) {
            return self::$success_callbacks[$module];
        }

        return FALSE;
    }


    public static function getErrorCallback($module)
    {

        if (isset(self::$error_callbacks[$module])) {
            return self::$error_callbacks[$module];
        }

        return FALSE;
    }


}


function calculate_total_items($array)
{


}


class PaymentSubscription
{

    const PENDING = 0;
    const CONFIRMED = 1;
    const SUSPENDED = -1;


}


if (!function_exists('url_get_content')) {

    function url_get_content($url)
    {

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        return file_get_contents($url, false, stream_context_create($arrContextOptions));
    }
}