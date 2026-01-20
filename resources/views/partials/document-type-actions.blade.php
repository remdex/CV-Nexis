@use('Orchid\Screen\Actions\ModalToggle')
@use('Orchid\Screen\Actions\Button')

<div class="btn-group" role="group">
    {!! ModalToggle::make(__('document_types.edit'))
        ->modal('editDocumentTypeModal')
        ->method('update')
        ->asyncParameters(['documentType' => $documentType->id])
        ->icon('pencil')
        ->render() !!}
    {!! Button::make(__('document_types.delete'))
        ->confirm(__('document_types.delete_confirm'))
        ->method('delete', ['documentType' => $documentType->id])
        ->icon('trash')
        ->render() !!}
</div>
