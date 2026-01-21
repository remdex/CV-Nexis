<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
             Menu::make(__('candidates.title'))
            ->icon('bs.person')
            ->title(__('candidates.human_resources'))
            ->route('platform.hrm.candidate.list')
            ->permission('platform.systems.candidates'),
            
            Menu::make(__('specialities.title'))
            ->icon('bs.briefcase')
            ->route('platform.hrm.speciality')
            ->permission('platform.systems.specialities'),

            Menu::make(__('competences.title'))
            ->icon('bs.award')
            ->route('platform.hrm.competence')
            ->permission('platform.systems.competences'),
            

            Menu::make(__('documents.title'))
            ->title(__('documents.title'))
            ->icon('bs.folder2-open')
            ->route('platform.hrm.documents')
            ->permission('platform.systems.documents'),

            Menu::make(__('document_types.title'))
            ->icon('bs.file-earmark')
            ->route('platform.hrm.document_type')
            ->permission('platform.systems.document_types')->divider(),

            Menu::make(__('companies.title'))
                ->title(__('companies.title'))
                ->icon('bs.building')
                ->route('platform.hrm.company.list')
                ->permission('platform.systems.companies'),

            Menu::make(__('municipalities.title'))
                ->icon('bs.geo-alt')
                ->route('platform.hrm.municipality.list')
                ->permission('platform.systems.municipalities'),

            Menu::make(__('activities.title'))
                ->icon('bs.diagram-3')
                ->route('platform.hrm.companyactivity')
                ->permission('platform.systems.activities')
                ->divider(),

            /*Menu::make('Get Started')
                ->icon('bs.book')
                ->title('Navigation')
                ->route(config('platform.index')),

            Menu::make('Sample Screen')
                ->icon('bs.collection')
                ->route('platform.example')
                ->badge(fn () => 6),

            Menu::make('Form Elements')
                ->icon('bs.card-list')
                ->route('platform.example.fields')
                ->active('* /examples/form/ *'),

            Menu::make('Layouts Overview')
                ->icon('bs.window-sidebar')
                ->route('platform.example.layouts'),

            Menu::make('Grid System')
                ->icon('bs.columns-gap')
                ->route('platform.example.grid'),

            Menu::make('Charts')
                ->icon('bs.bar-chart')
                ->route('platform.example.charts'),

            Menu::make('Cards')
                ->icon('bs.card-text')
                ->route('platform.example.cards')
                ->divider(),*/

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),

            /*Menu::make('Documentation')
                ->title('Docs')
                ->icon('bs.box-arrow-up-right')
                ->url('https://orchid.software/en/docs')
                ->target('_blank'),

            Menu::make('Changelog')
                ->icon('bs.box-arrow-up-right')
                ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
                ->target('_blank')
                ->badge(fn () => Dashboard::version(), Color::DARK)
                ->divider(),*/

            
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),

            ItemPermission::group(__('candidates.human_resources'))
                ->addPermission('platform.systems.candidates', __('candidates.title'))
                ->addPermission('platform.systems.candidates.delete', __('candidates.delete'))
                ->addPermission('platform.systems.specialities',__('specialities.title'))
                ->addPermission('platform.systems.competences',__('competences.title')),

            ItemPermission::group(__('companies.title'))
                ->addPermission('platform.systems.companies',__('companies.title'))
                ->addPermission('platform.systems.activities',__('activities.title'))
                ->addPermission('platform.systems.municipalities',__('municipalities.title')),

            ItemPermission::group(__('documents.title'))
                ->addPermission('platform.systems.document_types',__('document_types.title'))
                ->addPermission('platform.systems.documents',__('documents.title'))
                ->addPermission('platform.systems.documents.delete', __('documents.delete')),
        ];
    }
}
