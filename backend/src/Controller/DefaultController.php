<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController
{
    #[Route(path: '/', name: 'app_default', methods: ['GET'])]
    public function index(): Response
    {
        $now = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $html = <<<HTML
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Tickets Manager API</title>
            <style>
                :root { color-scheme: light dark; }
                body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; margin: 2rem; line-height: 1.5; }
                .card { max-width: 720px; border: 1px solid rgba(127,127,127,.3); border-radius: 12px; padding: 1.25rem 1.5rem; }
                h1 { margin: 0 0 .5rem; font-size: 1.4rem; }
                .muted { opacity: .7; font-size: .95rem; }
                code { padding: .15rem .35rem; border-radius: 6px; background: rgba(127,127,127,.15); }
            </style>
        </head>
        <body>
            <div class="card">
                <h1>Tickets Manager backend is running</h1>
                <p class="muted">Default route: <code>/</code></p>
                <p class="muted">UTC now: {$now}</p>
            </div>
        </body>
        </html>
        HTML;

        return new Response($html);
    }
}
