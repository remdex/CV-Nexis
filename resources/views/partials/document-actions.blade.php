@use('Orchid\Screen\Actions\ModalToggle')
@use('Orchid\Screen\Actions\Button')

<div class="btn-group" role="group">
    {!! ModalToggle::make(__('documents.view'))
        ->modal('viewDocumentModal')
        ->asyncParameters(['document' => $document->id])
        ->icon('eye')
        ->render() !!}
    {!! ModalToggle::make(__('documents.edit'))
        ->modal('editDocumentModal')
        ->method('update')
        ->asyncParameters(['document' => $document->id])
        ->icon('pencil')
        ->render() !!}

    @if(auth()->user() && auth()->user()->hasAccess('platform.systems.documents.delete'))
        {!! Button::make(__('documents.delete'))
            ->confirm(__('documents.delete_confirm'))
            ->method('delete', ['document' => $document->id])
            ->icon('trash')
            ->render() !!}
    @endif
</div>