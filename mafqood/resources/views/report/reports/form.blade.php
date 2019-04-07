<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="name" type="text" id="name" value="{{ isset( $report->name ) ? $report->name : '' }}" required>

    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('phone') ? 'has-error' : ''}}">
    <label for="phone" class="control-label">{{ 'Phone' }}</label>
    <input class="form-control" name="phone" type="text" id="phone" value="{{ isset( $report->phone ) ? $report->phone : '' }}" required>

    {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
    <label for="email" class="control-label">{{ 'Email' }}</label>
    <input class="form-control" name="email" type="text" id="email" value="{{ isset( $report->email ) ? $report->email : '' }}" required>

    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('gender') ? 'has-error' : ''}}">
    <label for="gender" class="control-label">{{ 'Gender' }}</label>
    <select name="gender" class="form-control" id="gender" required>
    @foreach (json_decode('{"0":"Male","1":"Female"}', true) as $optionKey => $optionValue)
        <option value="{{ $optionKey }}" {{ (isset($report->gender) && $report->gender == $optionKey) ? 'selected' : ''}}>{{ $optionValue }}</option>
    @endforeach
</select>
    {!! $errors->first('gender', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('birth') ? 'has-error' : ''}}">
    <label for="birth" class="control-label">{{ 'Birth' }}</label>
    <input class="form-control" name="birth" type="date" id="birth" value="{{ isset( $report->birth ) ? $report->birth : '' }}" >

    {!! $errors->first('birth', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('area_id') ? 'has-error' : ''}}">
    <label for="area_id" class="control-label">{{ 'Area Id' }}</label>
    <input class="form-control" name="area_id" type="number" id="area_id" value="{{ isset( $report->area_id ) ? $report->area_id : '' }}" required>

    {!! $errors->first('area_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('lat') ? 'has-error' : ''}}">
    <label for="lat" class="control-label">{{ 'Lat' }}</label>
    <input class="form-control" name="lat" type="number" id="lat" value="{{ isset( $report->lat ) ? $report->lat : '' }}" required>

    {!! $errors->first('lat', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('lang') ? 'has-error' : ''}}">
    <label for="lang" class="control-label">{{ 'Lang' }}</label>
    <input class="form-control" name="lang" type="number" id="lang" value="{{ isset( $report->lang ) ? $report->lang : '' }}" required>

    {!! $errors->first('lang', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('mental_condition') ? 'has-error' : ''}}">
    <label for="mental_condition" class="control-label">{{ 'Mental Condition' }}</label>
    <input class="form-control" name="mental_condition" type="text" id="mental_condition" value="{{ isset( $report->mental_condition ) ? $report->mental_condition : '' }}" >

    {!! $errors->first('mental_condition', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
