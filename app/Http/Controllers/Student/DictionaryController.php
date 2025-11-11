<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DictionaryController extends Controller
{
    public function lookup(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100'
        ]);

        try {
            $word = trim($request->word);

            // Gọi Glosbe API cho định nghĩa Anh-Việt (translations + examples)
            $glosbeUrl = 'https://glosbe.com/gapi/translate?from=eng&dest=vie&format=json&phrase=' . urlencode($word) . '&tm=true&pretty=true';
            $responseGlosbe = Http::timeout(10)->get($glosbeUrl);

            // Gọi Free Dictionary API cho phonetic + audio (English word)
            $freeUrl = 'https://api.dictionaryapi.dev/api/v2/entries/en/' . urlencode($word);
            $responseFree = Http::timeout(10)->get($freeUrl);

            $data = [
                'pronunciation' => '',
                'phonetic' => '',
                'definitions' => []
            ];

            // Xử lý Free Dictionary (phonetic + audio)
            if ($responseFree->successful()) {
                $freeData = $responseFree->json();
                if (isset($freeData[0])) {
                    $entry = $freeData[0];
                    $data['phonetic'] = $entry['phonetic'] ?? '';
                    if (isset($entry['phonetics']) && count($entry['phonetics']) > 0) {
                        foreach ($entry['phonetics'] as $ph) {
                            if (isset($ph['audio']) && $ph['audio']) {
                                $data['pronunciation'] = $ph['audio'];
                                break;
                            }
                        }
                    }
                }
            }

            // Xử lý Glosbe (definitions)
            if ($responseGlosbe->successful()) {
                $glosbeData = $responseGlosbe->json();
                if (isset($glosbeData['result']) && $glosbeData['result'] === 'ok' && isset($glosbeData['tuc'])) {
                    $definitions = [];
                    $meanings = [];

                    foreach ($glosbeData['tuc'] as $item) {
                        if (isset($item['phrase']['text'])) {
                            $text = $item['phrase']['text']; // Vietnamese translation
                            $example = '';
                            if (isset($item['examples']) && count($item['examples']) > 0) {
                                $example = $item['examples'][0]['second']; // Vietnamese example
                            }
                            $meanings[] = ['text' => $text, 'example' => $example];
                        }
                    }

                    if (!empty($meanings)) {
                        $definitions[] = [
                            'type' => '', // Glosbe không có POS rõ ràng, có thể để trống hoặc dùng từ Free nếu cần
                            'meanings' => $meanings
                        ];
                    }

                    $data['definitions'] = $definitions;
                }
            }

            if (!empty($data['definitions'])) {
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy từ này'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kết nối đến từ điển'
            ], 500);
        }
    }
}