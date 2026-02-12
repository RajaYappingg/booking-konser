<?php

declare(strict_types=1);

class Controller
{
    protected function view(string $template, array $data = []): void
    {
        view($template, $data);
    }

    protected function redirect(string $path): void
    {
        redirect($path);
    }
}
