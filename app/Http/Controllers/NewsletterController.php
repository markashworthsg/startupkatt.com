<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class NewsletterController extends Controller
{
    /**
     * Subscribe an email to the beehiiv publication.
     *
     * This is the server-side half of the on-brand signup form. The browser
     * POSTs an email here; we create the subscription via the beehiiv API v2 so
     * the form stays fully on-brand and isn't subject to ad-blockers eating the
     * hosted embed. Returns small JSON the form turns into inline states.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            // Honeypot: bots fill every field. Real users never see this one.
            'company' => ['nullable', 'size:0'],
        ]);

        $apiKey = config('comics.beehiiv.api_key');
        $publicationId = config('comics.beehiiv.publication_id');

        // beehiiv's API requires the publication id in `pub_<uuid>` form. The
        // dashboard sometimes shows the bare uuid, so normalise it here rather
        // than depending on whoever filled in .env to remember the prefix.
        if ($publicationId && ! str_starts_with($publicationId, 'pub_')) {
            $publicationId = 'pub_'.$publicationId;
        }

        // beehiiv isn't fully wired up: let the form fall back to the hosted page.
        if (! $apiKey || ! $publicationId) {
            return response()->json([
                'ok' => false,
                'reason' => 'unconfigured',
                'message' => "The inbox isn't wired up yet. Try again shortly.",
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(8)
                ->post("https://api.beehiiv.com/v2/publications/{$publicationId}/subscriptions", [
                    'email' => $validated['email'],
                    'reactivate_existing' => true,
                    'send_welcome_email' => true,
                    'utm_source' => 'startupkatt.com',
                    'utm_medium' => 'organic',
                    'referring_site' => $request->getHost(),
                ]);
        } catch (\Throwable $e) {
            Log::warning('beehiiv subscribe failed', ['error' => $e->getMessage()]);

            return response()->json([
                'ok' => false,
                'reason' => 'network',
                'message' => "That didn't send (very startup of it). Mind trying again?",
            ], Response::HTTP_BAD_GATEWAY);
        }

        if (! $response->successful()) {
            Log::warning('beehiiv subscribe rejected', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'ok' => false,
                'reason' => 'rejected',
                'message' => "That didn't send (very startup of it). Mind trying again?",
            ], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json([
            'ok' => true,
            'message' => "You're in. The next strip lands in your inbox tomorrow morning, no pitch deck attached.",
        ]);
    }
}
