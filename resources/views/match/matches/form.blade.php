<div class="form-group {{ $errors->has('missing_id') ? 'has-error' : ''}}">
    <label for="missing_id" class="control-label">{{ 'Missing Id' }}</label>
    <select class="form-control" name="found_id" type="text" id="found_id" value="{{ isset( $match->found_id ) ? $match->found_id : '' }}" required>
      @foreach($missing_reports as $missing_report)
        <option value="{{ $missing_report->id }}">{{ $missing_report->id }}</option>
      @endforeach
    </select>

    {!! $errors->first('missing_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('found_id') ? 'has-error' : ''}}">
    <label for="found_id" class="control-label">{{ 'Found Id' }}</label>
    <select class="form-control" name="found_id" type="text" id="found_id" value="{{ isset( $match->found_id ) ? $match->found_id : '' }}" required>
      @foreach($found_reports as $found_report)
        <option value="{{ $found_report->id }}">{{ $found_report->id }}</option>
      @endforeach
    </select>

    {!! $errors->first('found_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('serial') ? 'has-error' : ''}}">
    <label for="serial" class="control-label">{{ 'Serial' }}</label>
    <input class="form-control" name="serial" type="text" id="serial" value="{{ isset( $match->serial ) ? $match->serial : '' }}" required>
    {!! $errors->first('serial', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
