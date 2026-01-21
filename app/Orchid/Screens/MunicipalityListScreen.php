<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Municipality;
use App\Orchid\Layouts\MunicipalityListLayout;

class MunicipalityListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'municipalities' => Municipality::orderBy('code')->paginate(),
        ];
    }

    public function name(): ?string
    {
        return __('municipalities.title');
    }

    public function permission(): ?iterable
    {
        return ['platform.systems.municipalities'];
    }

    public function layout(): iterable
    {
        return [
            MunicipalityListLayout::class,
        ];
    }
}
