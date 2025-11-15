<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class DictionaryController extends Controller
{
    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'word' => ['required', 'string', 'max:100'],
        ]);

        $word = Str::lower(trim($validated['word']));

        try {
            $phoneticData = $this->fetchPhoneticData($word);
            $translations = $this->fetchTranslations($word);

            if (empty($phoneticData['meanings']) && empty($translations)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Không tìm thấy kết quả từ điển.',
                ], 404);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Tra cứu từ điển thành công.',
                'data'    => [
                    'word'                 => $word,
                    'phonetic'             => $phoneticData['phonetic'] ?? null,
                    'pronunciation_audio'  => $phoneticData['pronunciation'] ?? null,
                    'meanings'             => $phoneticData['meanings'],
                    'translations'         => $translations,
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không thể kết nối đến dịch vụ từ điển.',
            ], 503);
        }
    }

    protected function fetchPhoneticData(string $word): array
    {
        $result = [
            'phonetic'    => null,
            'pronunciation' => null,
            'meanings'    => [],
        ];

        $response = Http::timeout(10)
            ->acceptJson()
            ->get('https://api.dictionaryapi.dev/api/v2/entries/en/' . urlencode($word));

        if (! $response->successful()) {
            return $result;
        }

        $entries = $response->json();
        $entry = is_array($entries) && isset($entries[0]) ? $entries[0] : null;

        if (! is_array($entry)) {
            return $result;
        }

        $result['phonetic'] = $entry['phonetic'] ?? null;

        if (isset($entry['phonetics']) && is_array($entry['phonetics'])) {
            foreach ($entry['phonetics'] as $phoneticEntry) {
                if (! $result['phonetic'] && ! empty($phoneticEntry['text'])) {
                    $result['phonetic'] = $phoneticEntry['text'];
                }

                if (! $result['pronunciation'] && ! empty($phoneticEntry['audio'])) {
                    $result['pronunciation'] = $phoneticEntry['audio'];
                }

                if ($result['phonetic'] && $result['pronunciation']) {
                    break;
                }
            }
        }

        if (isset($entry['meanings']) && is_array($entry['meanings'])) {
            foreach ($entry['meanings'] as $meaning) {
                $definitions = [];

                if (! empty($meaning['definitions']) && is_array($meaning['definitions'])) {
                    foreach ($meaning['definitions'] as $definition) {
                        if (empty($definition['definition'])) {
                            continue;
                        }

                        $definitions[] = [
                            'definition' => $definition['definition'],
                            'example'    => $definition['example'] ?? null,
                        ];
                    }
                }

                if (! empty($definitions)) {
                    $result['meanings'][] = [
                        'part_of_speech' => $meaning['partOfSpeech'] ?? null,
                        'definitions'    => $definitions,
                    ];
                }
            }
        }

        return $result;
    }

    protected function fetchTranslations(string $word): array
    {
        $url = 'https://glosbe.com/gapi/translate?from=eng&dest=vie&format=json&phrase=' . urlencode($word) . '&tm=true&pretty=true';

        $response = Http::timeout(10)
            ->acceptJson()
            ->get($url);

        if (! $response->successful()) {
            return [];
        }

        $payload = $response->json();

        if (($payload['result'] ?? null) !== 'ok' || empty($payload['tuc']) || ! is_array($payload['tuc'])) {
            return [];
        }

        $translations = [];

        foreach ($payload['tuc'] as $entry) {
            if (! isset($entry['phrase']['text'])) {
                continue;
            }

            $translations[] = [
                'text'    => $entry['phrase']['text'],
                'example' => $entry['examples'][0]['second'] ?? null,
            ];

            if (count($translations) >= 15) {
                break;
            }
        }

        return $translations;
    }
}
