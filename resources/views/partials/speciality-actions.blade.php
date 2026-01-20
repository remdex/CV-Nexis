@use('Orchid\Screen\Actions\ModalToggle')
@use('Orchid\Screen\Actions\Button')

<div class="btn-group" role="group">
    {!! ModalToggle::make(__('specialities.edit'))
        ->modal('editSpecialityModal')
        ->method('update')
        ->asyncParameters(['speciality' => $speciality->id])
        ->icon('pencil')
        ->render() !!}
    {!! Button::make(__('specialities.delete'))
        ->confirm(__('specialities.delete_confirm'))
        ->method('delete', ['speciality' => $speciality->id])
        ->icon('trash')
        ->render() !!}
</div>
