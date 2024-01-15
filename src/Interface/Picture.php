<?php

declare(strict_types=1);

namespace App\Interface;

use Symfony\Component\HttpFoundation\Response;

class Picture implements MediaInterface
{
    public function add(object $item): Response
    {
    }

    public function edit(object $item): void
    {
    }

    public function delete(object $item): void
    {
    }
}
