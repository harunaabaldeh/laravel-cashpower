<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;

class BroadcastUtil
{
    public static function doBroadCast($channel, $event, $broadCastMessage, $provider = null)
    {
        if (empty($provider)){
            self::broadCastViaPusher($channel,$event,$broadCastMessage);
        }
    }

    private static function broadCastViaPusher($channel,$event,$broadCastMessage)
    {
        try {
            $pusher = new \Pusher\Pusher(env("PUSHER_APP_KEY"), env("PUSHER_APP_SECRET"),
                env("PUSHER_APP_ID"), ['cluster' => env("PUSHER_APP_CLUSTER")]);

            $pusher->trigger($channel,$event, $broadCastMessage);
        }catch (\Exception $exception){
            Log::error("[BroadcastUtil][broadCastViaPusher]\t Error doing broadcast\t ".
                $exception->getMessage());
        }

    }
}