<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;
use App\Models\DocumentType;

class DocumentTypeFilter extends Filter
{
    public function name(): string
    {
        return __('documents.fields.type');
    }

    public function parameters(): array
    {
        return ['document_type_id'];
    }

    public function run(Builder $builder): Builder
    {
        $type = $this->request->get('document_type_id');

        if (empty($type)) {
            return $builder;
        }

        return $builder->where('document_type_id', $type);
    }

    public function display(): array
    {
        return [
            Select::make('document_type_id')
                ->fromModel(DocumentType::class, 'name', 'id')
                ->empty()
                ->value($this->request->get('document_type_id'))
                ->title(__('documents.fields.type')),
        ];
    }

    public function value(): string
    {
        $type = DocumentType::find($this->request->get('document_type_id'));

        return $type ? $this->name().': '.$type->name : '';
    }
}
