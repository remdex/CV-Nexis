<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use App\Models\DocumentType as ModelDocumentType;
use Illuminate\Http\Request;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;

class DocumentTypeScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'document_types' => ModelDocumentType::latest()->get(),
        ];
    }

    public function name(): ?string
    {
        return __('document_types.title');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.document_types',
        ];
    }

    public function description(): ?string
    {
        return __('document_types.description');
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('document_types.add'))
                ->modal('documentTypeModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    public function asyncEditModal(ModelDocumentType $documentType): iterable
    {
        return ['document_type' => $documentType];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('document_types', [
                TD::make('name', __('document_types.fields.name')),
                TD::make('Actions', __('document_types.title'))
                    ->alignRight()
                    ->render(function (ModelDocumentType $documentType) {
                        return view('partials.document-type-actions', ['documentType' => $documentType]);
                    }),
            ]),

            Layout::modal('documentTypeModal', Layout::rows([
                Input::make('document_type.name')
                    ->title(__('document_types.fields.name'))
                    ->placeholder(__('document_types.fields.placeholder'))
                    ->help(__('document_types.fields.help_create')),
            ]))
                ->title(__('document_types.create'))
                ->applyButton(__('document_types.create_button')),

            Layout::modal('editDocumentTypeModal', Layout::rows([
                Input::make('document_type.name')
                    ->title(__('document_types.fields.name'))
                    ->placeholder(__('document_types.fields.placeholder'))
                    ->help(__('document_types.fields.help')),
            ]))
                ->title(__('document_types.edit_title'))
                ->applyButton(__('document_types.update_button'))
                ->async('asyncEditModal'),
        ];
    }

    public function create(Request $request)
    {
        $request->validate(['document_type.name' => 'required|max:255']);
        $type = new ModelDocumentType();
        $type->name = $request->input('document_type.name');
        $type->save();
    }

    public function update(Request $request, ModelDocumentType $documentType)
    {
        $request->validate(['document_type.name' => 'required|max:255']);
        $documentType->name = $request->input('document_type.name');
        $documentType->save();
    }

    public function delete(ModelDocumentType $documentType)
    {
        $documentType->delete();
    }
}
