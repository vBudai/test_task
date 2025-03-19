<?php

namespace App\Service\Country;

class CountryFromPhoneService
{
    protected static array $phoneCountryCodes = [];
    public static function extract(string $phone): ?string
    {
        self::loadPhoneCountryCodesIfEmpty();
        self::formatPhone($phone);

        foreach (self::$phoneCountryCodes as $code => $country) {
            if (str_starts_with($phone, $code)) {
                return $country;
            }
        }
        return null;
    }

    protected static function formatPhone(string &$phone): void
    {
        $phone = preg_replace('/\D/', '', $phone);
        if(str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }
    }

    protected static function loadPhoneCountryCodesIfEmpty(): void
    {
        if (!self::$phoneCountryCodes) {
            self::$phoneCountryCodes = require __DIR__ . '/../../../config/phone_country_codes.php';
        }
    }
}