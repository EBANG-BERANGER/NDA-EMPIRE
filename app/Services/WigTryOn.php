<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/** Asks Gemini to put $wig on the client's selfie; returns the stored result path (private disk). */
class WigTryOn
{
    public const PROMPT = 'Image 1 is a photo of a woman. Image 2 shows a wig. '
        .'Create a realistic portrait of the exact same woman from image 1 wearing the wig from image 2. '
        .'Keep her face, skin tone, facial features, expression and identity strictly unchanged. '
        .'Reproduce the wig faithfully: same color, length, texture, parting and volume, with a natural hairline. '
        .'Soft flattering salon lighting, head and shoulders framing, plain softly blurred background.';

    public function __invoke(User $user, Wig $wig): string
    {
        $key = config('salon.gemini_key');
        if (! $key) {
            throw new RuntimeException('La cabine IA n\'est pas encore activée.');
        }

        $part = fn (string $disk, string $path) => ['inline_data' => [
            'mime_type' => Storage::disk($disk)->mimeType($path),
            'data' => base64_encode(Storage::disk($disk)->get($path)),
        ]];

        $response = Http::timeout(90)
            ->withHeaders(['x-goog-api-key' => $key])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/'.config('salon.gemini_model').':generateContent', [
                'contents' => [['parts' => [
                    ['text' => self::PROMPT],
                    $part('local', $user->selfie_path),
                    $part('public', $wig->image_path),
                ]]],
                'generationConfig' => ['responseModalities' => ['IMAGE']],
            ]);

        foreach ($response->json('candidates.0.content.parts', []) as $p) {
            $data = $p['inlineData']['data'] ?? $p['inline_data']['data'] ?? null;
            if ($data) {
                $path = 'tryons/'.$user->id.'/'.uniqid().'.png';
                Storage::disk('local')->put($path, base64_decode($data));

                return $path;
            }
        }

        logger()->warning('Gemini try-on returned no image', ['status' => $response->status(), 'body' => mb_substr($response->body(), 0, 500)]);
        throw new RuntimeException('L\'IA n\'a pas pu créer l\'image. Essaie une photo de face, bien éclairée.');
    }
}
