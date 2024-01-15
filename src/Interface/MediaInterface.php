<?php

declare(strict_types=1);

namespace App\Interface;

use Symfony\Component\HttpFoundation\Response;

interface MediaInterface
{
    public function add(object $item): Response;

    public function edit(object $item): void;

    public function delete(object $item): void;
}
