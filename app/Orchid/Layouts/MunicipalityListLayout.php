<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Municipality;
use Orchid\Screen\Actions\Link;

class MunicipalityListLayout extends Table
{
    protected $target = 'municipalities';

    protected function columns(): iterable
    {
        return [
            TD::make('code', __('municipalities.fields.code'))
                ->render(function (Municipality $m) {
                    return Link::make($m->code)->route('platform.hrm.municipality.edit', $m);
                }),
            TD::make('name', __('municipalities.fields.name')),
            TD::make('valid_from', __('municipalities.fields.valid_from'))
                ->render(function (Municipality $m) {
                    return $m->valid_from?->format('Y-m-d');
                }),
        ];
    }
}
