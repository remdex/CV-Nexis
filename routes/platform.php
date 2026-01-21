<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

\Tabuna\Breadcrumbs\Breadcrumbs::for('platform.index', function (Trail $trail) {
    $trail->push(__('hrm.home'), route(config('platform.index')));
});

use App\Orchid\Screens\SpecialityScreen;
use App\Orchid\Screens\CompetenceScreen;
use App\Orchid\Screens\CandidateEditScreen;
use App\Orchid\Screens\CandidateListScreen;
use App\Orchid\Screens\CandidateCommentsScreen;
use App\Orchid\Screens\DocumentTypeScreen;
use App\Orchid\Screens\DocumentScreen;
use App\Orchid\Screens\CompanyActivityScreen;
use App\Orchid\Screens\CompanyListScreen;
use App\Orchid\Screens\CompanyEditScreen;
use App\Orchid\Screens\MunicipalityListScreen;
use App\Orchid\Screens\MunicipalityEditScreen;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

        /*
Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');*/

// HRM
Route::screen('speciality', SpecialityScreen::class)->name('platform.hrm.speciality')->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.index')
    ->push(__('hrm.specialities'));
});

Route::screen('competence', CompetenceScreen::class)->name('platform.hrm.competence')->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.index')
    ->push(__('hrm.competences'));
});

Route::screen('companyactivity', CompanyActivityScreen::class)->name('platform.hrm.companyactivity')->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.index')
    ->push(__('activities.title'));
});

Route::screen('document-type', DocumentTypeScreen::class)->name('platform.hrm.document_type')->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.index')
    ->push(__('hrm.document_types'));
});

Route::screen('documents', DocumentScreen::class)->name('platform.hrm.documents')->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.index')
    ->push(__('hrm.documents'));
});



Route::get('candidate/{candidate}/download-cv', [CandidateEditScreen::class, 'downloadCV'])
    ->name('platform.hrm.candidate.download-cv');

Route::get('candidate/{candidate}/attachment/{attachment}/download', [CandidateEditScreen::class, 'downloadAttachment'])
    ->name('platform.hrm.candidate.download-attachment');

Route::get('candidate/{candidate}/attachment/{attachment}/view', [CandidateEditScreen::class, 'viewAttachment'])
    ->name('platform.hrm.candidate.view-attachment');

Route::get('document/{document}/attachment/{attachment}/download', [\App\Orchid\Screens\DocumentScreen::class, 'downloadAttachment'])
    ->name('platform.hrm.document.download-attachment');

Route::get('document/{document}/attachment/{attachment}/view', [\App\Orchid\Screens\DocumentScreen::class, 'viewAttachment'])
    ->name('platform.hrm.document.view-attachment');

Route::screen('candidate/{candidate}/comments', CandidateCommentsScreen::class)
    ->name('platform.hrm.candidate.comments');

Route::screen('candidates', CandidateListScreen::class)
    ->name('platform.hrm.candidate.list')->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.index')
    ->push(__('hrm.candidates'), route('platform.hrm.candidate.list'));
});

Route::screen('candidate/{candidate?}', CandidateEditScreen::class)
    ->name('platform.hrm.candidate.edit')->breadcrumbs(function (Trail $trail, $candidate = null){
    return $trail
        ->parent('platform.hrm.candidate.list')
    ->push($candidate ? __('hrm.edit_candidate') : __('hrm.create_candidate'), route('platform.hrm.candidate.edit', $candidate));
});

// Companies Routes
Route::screen('companies', CompanyListScreen::class)
    ->name('platform.hrm.company.list')->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.index')
    ->push(__('companies.title'), route('platform.hrm.company.list'));
});

Route::screen('company/{company?}', CompanyEditScreen::class)
    ->name('platform.hrm.company.edit')->breadcrumbs(function (Trail $trail, $company = null){
    return $trail
        ->parent('platform.hrm.company.list')
    ->push($company ? __('companies.edit') : __('companies.add'), route('platform.hrm.company.edit', $company));
});

// Municipalities
Route::screen('municipalities', MunicipalityListScreen::class)
    ->name('platform.hrm.municipality.list')->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.index')
    ->push(__('municipalities.title'), route('platform.hrm.municipality.list'));
});

Route::screen('municipality/{municipality?}', MunicipalityEditScreen::class)
    ->name('platform.hrm.municipality.edit')->breadcrumbs(function (Trail $trail, $municipality = null){
    return $trail
        ->parent('platform.hrm.municipality.list')
    ->push($municipality ? 'Edit' : 'Create', route('platform.hrm.municipality.edit', $municipality));
});

// AI Processing Routes
use App\Orchid\Screens\AiScreen;

Route::screen('ai', AiScreen::class)
    ->name('platform.ai')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('AI Processing')));

Route::post('ai/process', [AiScreen::class, 'process'])
    ->name('platform.ai.process');
Route::post('ai/analyze', [AiScreen::class, 'analyze'])
    ->name('platform.ai.analyze');

// Route::screen('idea', Idea::class, 'platform.screens.idea');
