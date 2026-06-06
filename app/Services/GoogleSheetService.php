<?php

namespace App\Services;

use Google\Client;
use Google\Service\Exception as GoogleServiceException;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class GoogleSheetService
{
    protected Sheets $service;
    protected string $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = (string) config('services.google_sheet.sheet_id');
        $credentialPath = (string) config('services.google_sheet.credential_path');
        $fullCredentialPath = base_path($credentialPath);

        if ($this->spreadsheetId === '') {
            throw new RuntimeException('Google Sheet ID belum diisi. Lengkapi GOOGLE_SHEET_ID di file .env.');
        }

        if ($credentialPath === '' || ! file_exists($fullCredentialPath)) {
            throw new RuntimeException('File credential Google Service Account tidak ditemukan. Pastikan GOOGLE_SERVICE_ACCOUNT_JSON mengarah ke file JSON yang benar.');
        }

        $client = new Client();
        $client->setAuthConfig($fullCredentialPath);
        $client->addScope(Sheets::SPREADSHEETS);

        $this->service = new Sheets($client);
    }

    public function get(string $range): array
    {
        try {
            $response = $this->service
                ->spreadsheets_values
                ->get($this->spreadsheetId, $range);

            return $response->getValues() ?? [];
        } catch (Throwable $exception) {
            throw new RuntimeException($this->messageFor($exception, $range), previous: $exception);
        }
    }

    public function append(string $range, array $values): void
    {
        $body = new ValueRange([
            'values' => [$values],
        ]);

        try {
            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                $range,
                $body,
                ['valueInputOption' => 'USER_ENTERED']
            );
        } catch (Throwable $exception) {
            throw new RuntimeException($this->messageFor($exception, $range), previous: $exception);
        }
    }

    public function update(string $range, array $values): void
    {
        $body = new ValueRange([
            'values' => [$values],
        ]);

        try {
            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                $range,
                $body,
                ['valueInputOption' => 'USER_ENTERED']
            );
        } catch (Throwable $exception) {
            throw new RuntimeException($this->messageFor($exception, $range), previous: $exception);
        }
    }

    public function findByColumn(string $sheetName, string $columnName, mixed $value): ?array
    {
        foreach ($this->getRowsAsAssoc($sheetName) as $row) {
            if (($row[$columnName] ?? null) === (string) $value) {
                return $row;
            }
        }

        return null;
    }

    public function getRowsAsAssoc(string $sheetName): array
    {
        $rows = $this->get($sheetName.'!A:Z');

        if ($rows === []) {
            return [];
        }

        $headers = array_map('trim', array_shift($rows));
        $result = [];

        foreach ($rows as $index => $row) {
            if ($this->isBlankRow($row)) {
                continue;
            }

            $assoc = [];
            foreach ($headers as $position => $header) {
                if ($header === '') {
                    continue;
                }

                $assoc[$header] = (string) ($row[$position] ?? '');
            }

            $assoc['_row_number'] = $index + 2;
            $result[] = $assoc;
        }

        return $result;
    }

    public function generateId(string $prefix): string
    {
        return strtoupper($prefix).now()->format('YmdHis').strtoupper(Str::random(4));
    }

    public function updateRowById(string $sheetName, string $idColumn, string $id, array $values): bool
    {
        $rows = $this->getRowsAsAssoc($sheetName);
        $target = collect($rows)->firstWhere($idColumn, $id);

        if (! $target) {
            return false;
        }

        $headers = $this->get($sheetName.'!1:1')[0] ?? [];
        $ordered = [];

        foreach ($headers as $header) {
            $ordered[] = $values[$header] ?? $target[$header] ?? '';
        }

        $lastColumn = $this->columnName(count($headers));
        $this->update($sheetName.'!A'.$target['_row_number'].':'.$lastColumn.$target['_row_number'], $ordered);

        return true;
    }

    private function columnName(int $number): string
    {
        $name = '';

        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function isBlankRow(array $row): bool
    {
        return collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isEmpty();
    }

    private function messageFor(Throwable $exception, string $range): string
    {
        if ($exception instanceof GoogleServiceException) {
            $code = $exception->getCode();

            return match ($code) {
                403 => 'Akses Google Sheet ditolak. Share spreadsheet ke email service account dan pastikan Google Sheets API aktif.',
                404 => 'Google Sheet atau range tidak ditemukan: '.$range.'. Periksa GOOGLE_SHEET_ID dan nama sheet.',
                default => 'Google Sheets API mengembalikan error: '.$exception->getMessage(),
            };
        }

        return 'Gagal mengakses Google Sheet untuk range '.$range.': '.$exception->getMessage();
    }
}
