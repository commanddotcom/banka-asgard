<?php

namespace App\Services;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use InvalidArgumentException;

/**
 * Builds the NBU "розрахунковий QR-код" payload and renders it as SVG.
 *
 * Field order, base URL and encoding are taken verbatim from the NBU's own
 * technical instructions ("Як сформувати QR-код суб'єкту господарювання",
 * bank.gov.ua), which uses the bank.gov.ua/qr/ base URL, Win1251 encoding
 * and 13 fields — this is what Monobank and PrivatBank both parse correctly.
 */
final class NbuQr
{
    private const SERVICE_TAG = 'BCD';
    private const VERSION = '002';
    private const BASE_URL = 'https://bank.gov.ua/qr/';

    private const ENCODING_WIN1251 = '2';
    private const FUNCTION_UCT = 'UCT';

    public static function buildUrl(
        string $recipient,
        string $iban,
        string $recipientCode,
        string $purpose,
        ?float $amount = null
    ): string {
        $iban = preg_replace('/\s+/', '', $iban);

        if (mb_strlen($recipient) === 0 || mb_strlen($recipient) > 140) {
            throw new InvalidArgumentException('Назва отримувача обов\'язкова і не довша за 140 символів.');
        }
        if (! preg_match('/^UA\d{27}$/', $iban)) {
            throw new InvalidArgumentException('IBAN має бути у форматі UA + 27 цифр.');
        }
        if (mb_strlen($recipientCode) === 0 || mb_strlen($recipientCode) > 10) {
            throw new InvalidArgumentException('Код отримувача (ІПН/ЄДРПОУ) обов\'язковий, до 10 символів.');
        }
        if (mb_strlen($purpose) === 0 || mb_strlen($purpose) > 200) {
            throw new InvalidArgumentException('Призначення платежу обов\'язкове, до 200 символів.');
        }
        if ($amount !== null && ($amount < 0 || $amount > 999999999.99)) {
            throw new InvalidArgumentException('Некоректна сума.');
        }

        $fields = [
            self::SERVICE_TAG,
            self::VERSION,
            self::ENCODING_WIN1251,
            self::FUNCTION_UCT,
            '',
            self::toWin1251($recipient),
            $iban,
            $amount !== null ? 'UAH'.self::trimAmount($amount) : '',
            $recipientCode,
            '',
            '',
            self::toWin1251($purpose),
            '',
        ];

        $encoded = rtrim(strtr(base64_encode(implode("\n", $fields)), '+/', '-_'), '=');

        return self::BASE_URL.$encoded;
    }

    private static function toWin1251(string $text): string
    {
        return mb_convert_encoding($text, 'Windows-1251', 'UTF-8');
    }

    public static function renderSvg(string $url): string
    {
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'eccLevel' => EccLevel::M,
            'scale' => 6,
            'addQuietzone' => true,
            'outputBase64' => false,
        ]);

        return (new QRCode($options))->render($url);
    }

    private static function trimAmount(float $amount): string
    {
        return rtrim(rtrim(number_format($amount, 2, '.', ''), '0'), '.');
    }
}
