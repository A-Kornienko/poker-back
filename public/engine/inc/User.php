<?php

namespace inc;

class User
{
    public $db = 'test_value';

    public array $blocked_set_property = ['id', 'login', 'balance'];

    public array $user_info = [
        "id"                 => 902601,
        "login"              => "Test_12345",
        "pass"               => "152be0dd0693466787745dca064160f2",
        "email"              => "Test_12345@test.com",
        "mail_active_status" => 0,
        "phone_active"       => 0,
        "qiwi"               => "",
        "balance"            => 1000.0000,
        "currency"           => "USD",
        "balance_bonus"      => 0.0,
        "wager"              => "0.0000",
        "wager_bonus"        => "0.0000",
        "pay_points"         => 0,
        "demobalance"        => 0.0,
        "demomode"           => 1,
        "creator"            => 1,
        "reg_time"           => "2024-02-15 23:53:15",
        "go_time"            => "2024-05-14 15:56:52",
        "ip"                 => "2a0d:3344:1e12:3010::ddd",
        "city"               => "Gdynia",
        "last_ge"            => "root",
        "status"             => 5,
        "action"             => 1,
        "denomination"       => 1.0,
        "sound"              => 1,
        "lang"               => "ru",
        "payin_total"        => 0.0,
        "payout_total"       => 0.0,
        "gift"               => "0",
        "firstname"          => null,
        "lastname"           => null,
        "birthday"           => null,
        "ref_id"             => null,
        "http_referer"       => "poker.gambling-dev.pro/",
        "game_token"         => null,
        "comment"            => null,
        "action_token"       => null,
        "url"                => null,
        "refcode_id"         => null,
        "ban_chat"           => 0,
        "tour_id"            => 0,
        "domain_group_id"    => 34,
    ];

    public function __construct(?array $params = [])
    {
        foreach ($params as $key => $param) {
            $this->user_info[$key] = $param;
        }
    }

    public function getBalance(): float
    {
        return $this->user_info['balance'];
    }

    public function changeBalance(float $amount, string $log = '', string $transaction_id = ''): float
    {
        return 1000.0;
    }

    public function getUserInfo(): array
    {
        return $this->user_info;
    }

    public static function findOneBy($params): ?User
    {
        return new User($params);
    }

    public static function getAuthorizedUser(): User
    {
        return new User();
    }
}
