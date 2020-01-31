<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="name" type="text" id="name" value="{{ isset( $suspect->name ) ? $suspect->name : '' }}">

    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('appearance_times') ? 'has-error' : ''}}">
    <label for="appearance_times" class="control-label">{{ 'Appearance Times' }}</label>
    <input class="form-control" name="appearance_times" type="number" id="appearance_times" value="{{ isset( $suspect->appearance_times ) ? $suspect->appearance_times : '' }}" >

    {!! $errors->first('appearance_times', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
