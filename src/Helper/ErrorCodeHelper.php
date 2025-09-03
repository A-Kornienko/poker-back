<?php

declare(strict_types=1);

namespace App\Helper;

class ErrorCodeHelper
{
    public const CODE_ERROR_400 = 400;
    public const CODE_ERROR_401 = 401;
    public const CODE_ERROR_403 = 403;
    public const CODE_ERROR_404 = 404;

    public const TABLE_NOT_FOUND                         = 2;
    public const PLAYER_NOT_FOUND                        = 3;
    public const SMALL_BET                               = 10;
    public const SMALL_BALANCE                           = 11;
    public const PLAYER_WRONG_TURN                       = 12;
    public const INCORRECT_BALANCE                       = 16;
    public const MAIN_USER_NOT_FOUND                     = 17;
    public const USER_IN_GAME                            = 18;
    public const TIME_OVER                               = 20;
    public const USER_INACTIVE                           = 21;
    public const LONG_MESSAGE                            = 22;
    public const EMPTY_MESSAGE                           = 23;
    public const USER_ALREADY_REGISTERED_IN_TOURNAMENT   = 24;
    public const REGISTRATION_NOT_STARTED_YET            = 26;
    public const REGISTRATION_IS_OVER                    = 27;
    public const TOURNAMENT_HAS_STARTED                  = 28;
    public const TOURNAMENT_IS_FULL                      = 29;
    public const CANCEL_REGISTRATION_IS_OVER             = 31;
    public const TOURNAMENT_AMOUNT_BUY_IN_SMALL          = 33;
    public const TOURNAMENT_BUY_IN_LIMIT_TIME            = 34;
    public const TOURNAMENT_BUY_IN_LIMIT_COUNT_PLAYERS   = 35;
    public const TOURNAMENT_BUY_IN_TOO_MANY_CHIPS        = 36;
    public const TOURNAMENT_BUY_IN_LIMIT_BY_NUMBER_TIMES = 37;
    public const TOURNAMENT_AMOUNT_BUY_IN_BIG            = 38;
    public const NO_SETTINGS                             = 40;
    public const NO_CURRENT_SETTING                      = 41;
    public const NO_TOURNAMENTS                          = 42;
    public const NO_TOURNAMENT                           = 43;
    public const NO_TABLES_TOURNAMENTS                   = 44;
    public const USER_NOT_IN_TOURNAMENT                  = 45;
    public const BIG_BALANCE_FOR_BUY_IN                  = 46;
    public const YOU_ARE_NOT_A_LOSER                     = 47;
    public const SMALL_CALL                              = 48;
    public const SMALL_CHECK                             = 49;
    public const BIG_BET                                 = 50;
    public const NO_LATE_TOURNAMENT_REGISTRATION         = 51;
    public const TOO_BIG_BLIND_LEVEL                     = 52;
    public const TIME_OVER_LATE_REGISTRATION             = 53;
    public const LATE_REGISTRATION_NOT_STARTED           = 55;

    public static function getErrorByCode($code): string
    {
        return self::errorCode()[$code] ?? "Code not found";
    }

    public static function errorCode(): array
    {
        return [
            self::TABLE_NOT_FOUND                         => "Table not found",
            self::PLAYER_NOT_FOUND                        => "Player not found",
            self::SMALL_BET                               => "Your bet is small",
            self::SMALL_BALANCE                           => "Your balance is small",
            self::PLAYER_WRONG_TURN                       => "Your turn is wrong",
            self::INCORRECT_BALANCE                       => "Incorrect stack replenishment, your main balance is too low",
            self::MAIN_USER_NOT_FOUND                     => "Main user not found",
            self::USER_IN_GAME                            => "You already in game",
            self::TIME_OVER                               => "Time is expired, please wait your turn",
            self::USER_INACTIVE                           => "You can not play now, please wait a new round",
            self::LONG_MESSAGE                            => "Your message is too long",
            self::EMPTY_MESSAGE                           => "Your message is empty",
            self::USER_ALREADY_REGISTERED_IN_TOURNAMENT   => "You already registered on this tournament.",
            self::REGISTRATION_NOT_STARTED_YET            => "Registration for this tournament has not started yet",
            self::REGISTRATION_IS_OVER                    => "Registration for this tournament is over",
            self::TOURNAMENT_HAS_STARTED                  => "Tournament has already started",
            self::TOURNAMENT_IS_FULL                      => "There are no more free places in the tournament",
            self::CANCEL_REGISTRATION_IS_OVER             => "Can not cancel registration",
            self::TOURNAMENT_AMOUNT_BUY_IN_BIG            => "Tournament amount purchase is too big",
            self::TOURNAMENT_AMOUNT_BUY_IN_SMALL          => "Tournament amount purchase is too small",
            self::TOURNAMENT_BUY_IN_LIMIT_TIME            => "The purchase time is over",
            self::TOURNAMENT_BUY_IN_LIMIT_COUNT_PLAYERS   => "impossible to buy more, not enough players in the tournament",
            self::TOURNAMENT_BUY_IN_TOO_MANY_CHIPS        => "impossible to buy more, your balance is too high",
            self::TOURNAMENT_BUY_IN_LIMIT_BY_NUMBER_TIMES => "impossible to buy more, limit of buy in",
            self::NO_SETTINGS                             => "No found settings for this table",
            self::NO_CURRENT_SETTING                      => "No found current setting",
            self::NO_TOURNAMENTS                          => "No found tournaments",
            self::NO_TOURNAMENT                           => "No found current tournament",
            self::NO_TABLES_TOURNAMENTS                   => "No found tables for this tournament",
            self::USER_NOT_IN_TOURNAMENT                  => "No found current user in tournament",
            self::SMALL_CALL                              => "You can't call, short stack",
            self::SMALL_CHECK                             => "Your current bet is not enough for a check",
            self::BIG_BET                                 => "Bet too big for your stack",
            self::BIG_BALANCE_FOR_BUY_IN                  => "Your balance too big",
            self::YOU_ARE_NOT_A_LOSER                     => "You are not a loser",
            self::NO_LATE_TOURNAMENT_REGISTRATION         => "Tournament has no late registration",
            self::TOO_BIG_BLIND_LEVEL                     => "Tournament blinds level is too big for late registration",
            self::TIME_OVER_LATE_REGISTRATION             => "Tournament late registration time over",
            self::LATE_REGISTRATION_NOT_STARTED           => "Tournament late registration is not started",
        ];
    }
}
