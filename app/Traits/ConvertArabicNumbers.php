<?php

namespace App\Traits;

trait ConvertArabicNumbers
{
    /**
     * Convert Arabic/Persian numbers to English numbers.
     *
     * @param string|null $number
     * @return string|null
     */
    public function arabicToEnglishNumbers(?string $number): ?string
    {
        if (is_null($number)) {
            return null;
        }

        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $number = str_replace($arabic, $english, $number);
        $number = str_replace($persian, $english, $number);

        return $number;
    }
}
