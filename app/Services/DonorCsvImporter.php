<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

/**
 * Parses the admin-uploaded donor CSV: one donor per line, semicolon-
 * separated, no header row —
 *   Ім'я;4_цифри_номера_телефону;очікувана_сума
 * The amount is optional (either 2 or 3 fields per line). Validates the
 * whole file before returning anything — a single bad line fails the
 * entire import with a message naming that exact line, rather than
 * silently skipping it.
 */
final class DonorCsvImporter
{
    /**
     * @return array<int, array{name: string, phone_last4: string, amount: ?float}>
     *
     * @throws ValidationException
     */
    public static function parse(string $content): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $content);

        $rows = [];
        $errors = [];

        foreach ($lines as $index => $line) {
            $lineNumber = $index + 1;
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $parts = explode(';', $line);

            if (count($parts) < 2 || count($parts) > 3) {
                $errors["line.{$lineNumber}"] = "Рядок {$lineNumber}: очікується формат \"Ім'я;4_цифри_номера_телефону;очікувана_сума\" (сума необов'язкова), отримано \"{$line}\".";

                continue;
            }

            $name = trim($parts[0]);
            $phoneLast4 = trim($parts[1]);
            $rawAmount = isset($parts[2]) ? trim(str_replace(',', '.', $parts[2])) : '';

            if ($name === '') {
                $errors["line.{$lineNumber}"] = "Рядок {$lineNumber}: ім'я не може бути порожнім.";

                continue;
            }

            if (! preg_match('/^\d{4}$/', $phoneLast4)) {
                $errors["line.{$lineNumber}"] = "Рядок {$lineNumber}: \"{$phoneLast4}\" — номер телефону має бути рівно 4 цифрами.";

                continue;
            }

            if ($rawAmount !== '' && (! is_numeric($rawAmount) || (float) $rawAmount < 0)) {
                $errors["line.{$lineNumber}"] = "Рядок {$lineNumber}: сума \"{$rawAmount}\" не є коректним додатним числом.";

                continue;
            }

            $rows[] = [
                'name' => $name,
                'phone_last4' => $phoneLast4,
                'amount' => $rawAmount !== '' ? round((float) $rawAmount, 2) : null,
            ];
        }

        if ($errors === [] && $rows === []) {
            $errors['file'] = 'Файл порожній.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $rows;
    }
}
