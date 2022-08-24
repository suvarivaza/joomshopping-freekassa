<?php

namespace Suvarivaza\Joomshopping\FreeKassa;

class Cloudflare
{

    /*
     * если существует $_SERVER["HTTP_CF_CONNECTING_IP"] то сайт работает через Cloudflare
     */
    static public function isActive(){
        return !empty($_SERVER["HTTP_CF_CONNECTING_IP"]);
    }


    /*
     * Если включен Cloudflare то реальный IP будет находится в заголовке $_SERVER["HTTP_CF_CONNECTING_IP"]
     */
    static public function getRealIp(){
        if (Cloudflare::checkIPv4()) return $_SERVER["HTTP_CF_CONNECTING_IP"];
        else die(); //попытка взлома! IP с которого пришел запрос не пренадлежит Cloudflare!
    }


    /*
     * проверка принадлежит ли IP с которого пришел запрос сервису Cloudflare
     */
    static public function checkIPv4()
    {
        $cloudflareIps = file_get_contents('https://www.cloudflare.com/ips-v4'); //список IP пренадлежащих Cloudflare
        $cloudflareIps = explode("\n", $cloudflareIps);

        foreach ($cloudflareIps as $cloudflareIp) {
            if (self::checkIpInRange($_SERVER['REMOTE_ADDR'], $cloudflareIp)) return true;
        }

        return false;
    }


    /*
     * проверка вхождения IP в подсеть
     */
    static private function checkIpInRange($ip, $range)
    {
        if (strpos($range, '/') == false) {
            $range .= '/32';
        }
        // $range is in IP/CIDR format eg 127.0.0.1/24
        list($range, $netmask) = explode('/', $range, 2);
        $range_decimal = ip2long($range);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
        $netmask_decimal = ~$wildcard_decimal;
        return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
    }

}