@use('Orchid\Screen\Actions\ModalToggle')
@use('Orchid\Screen\Actions\Button')

<div class="btn-group" role="group">
    {!! ModalToggle::make(__('competences.edit'))
        ->modal('editCompetenceModal')
        ->method('update')
        ->asyncParameters(['competence' => $competence->id])
        ->icon('pencil')
        ->render() !!}
    {!! Button::make(__('competences.delete'))
        ->confirm(__('competences.delete_confirm'))
        ->method('delete', ['competence' => $competence->id])
        ->icon('trash')
        ->render() !!}
</div>
