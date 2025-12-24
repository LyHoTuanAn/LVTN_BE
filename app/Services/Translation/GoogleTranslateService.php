<?php

namespace App\Services\Translation;

use Illuminate\Support\Facades\Http;

class GoogleTranslateService
{
    protected string $endpoint = 'https://translate.googleapis.com/translate_a/single';

    /**
     * Translate text using unofficial Google Translate endpoint.
     *
     * @param string $text  Source text
     * @param string $from  Source language (e.g. 'vi')
     * @param string $to    Target language (e.g. 'en')
     */
    public function translate(string $text, string $from = 'vi', string $to = 'en'): string
    {
        if (trim($text) === '') {
            return $text;
        }

        try {
            $response = Http::get($this->endpoint, [
                'client' => 'gtx',
                'sl' => $from,
                'tl' => $to,
                'dt' => 't',
                'q' => $text,
            ]);

            if (!$response->ok()) {
                return $text;
            }

            $data = $response->json();

            // Expected structure: [ [ [ [ translatedText, originalText, ... ] ] , ... ], ... ]
            if (!is_array($data) || !isset($data[0][0][0])) {
                return $text;
            }

            return (string) $data[0][0][0];
        } catch (\Throwable $e) {
            // On any error, just return original text (no hard fail)
            return $text;
        }
    }
}


