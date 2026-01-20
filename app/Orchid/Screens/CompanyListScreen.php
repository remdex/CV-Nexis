<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Company;
use Orchid\Screen\Actions\Link;
use App\Orchid\Layouts\CompanyListLayout;
use App\Orchid\Layouts\CompanySelection;

class CompanyListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'companies' => Company::with('activityClassificators')
                ->filters(CompanySelection::class)
                ->orderBy('created_at', 'desc')
                ->paginate()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('companies.title');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.companies',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('companies.add'))
                ->icon('pencil')
                ->route('platform.hrm.company.edit')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            CompanySelection::class,
            CompanyListLayout::class
        ];
    }
}
