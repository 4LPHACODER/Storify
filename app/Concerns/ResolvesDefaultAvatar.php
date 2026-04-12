<?php

namespace App\Concerns;

trait ResolvesDefaultAvatar
{
    protected function defaultAvatarUrl(?string $name): string
    {
        $label = trim((string) $name) !== '' ? $name : 'Storify User';

        return 'https://ui-avatars.com/api/?name='
            .rawurlencode($label)
            .'&background=1DB954&color=FFFFFF&bold=true&size=256&rounded=true';
    }
}
