<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\Http\Response as CoreResponse;

class Response
{
    /**
     * Redirect the client to a given URL.
     *
     * Unlike the previous implementation, this no longer calls exit(),
     * making the method safe to use in a test context. The caller is
     * expected to return or stop further processing if needed.
     *
     * @param string $url
     */
    public static function redirect(string $url): void
    {
        header("Location: {$url}");
    }

    /**
     * Return a JSON response.
     *
     * Unlike the previous implementation, this no longer calls exit(),
     * making the method safe to use in a test context. The caller is
     * expected to return or stop further processing if needed.
     *
     * @param array $data
     * @param int   $status
     */
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode($data);
    }
}
