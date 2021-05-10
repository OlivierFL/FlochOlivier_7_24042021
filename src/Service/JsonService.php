<?php

namespace App\Service;

use JsonException;
use Symfony\Component\HttpFoundation\Request;

class JsonService
{
    /**
     * @param Request $request
     *
     * @throws JsonException
     *
     * @return bool|string
     */
    public function toJson(Request $request): bool | string
    {
        if ($request->getContent()) {
            return $request->getContent();
        }

        return json_encode($request->request->all(), JSON_THROW_ON_ERROR);
    }
}
