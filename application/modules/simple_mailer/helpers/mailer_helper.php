<?php


class Mailer{

    public static function sendClientNotification($user_id, $body,$subject)
    {
        $ctx = &get_instance();
        $ctx->mMailer->sendSimpleNotification($user_id, $body,$subject);

    }

    public static function sendAdminNotification($body,$subject)
    {
        $ctx = &get_instance();
        $ctx->mMailer->sendAdminNotification($body,$subject);

    }
}