<div class="form-group {{ $errors->has('print') ? 'has-error' : ''}}">
    <label for="print" class="control-label">{{ 'Print' }}</label>
    <textarea class="form-control" rows="5" name="print" type="textarea" id="print" required>{{ isset( $stamp->print ) ? $stamp->print : '' }}</textarea>

    {!! $errors->first('print', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('image') ? 'has-error' : ''}}">
    <label for="image" class="control-label">{{ 'Image' }}</label>
    <input class="form-control" name="image" type="text" id="image" value="{{ isset( $stamp->image ) ? $stamp->image : '' }}" required>

    {!! $errors->first('image', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('bbox') ? 'has-error' : ''}}">
    <label for="bbox" class="control-label">{{ 'Bbox' }}</label>
    <textarea class="form-control" rows="5" name="bbox" type="textarea" id="bbox" required>{{ isset( $stamp->bbox ) ? $stamp->bbox : '' }}</textarea>

    {!! $errors->first('bbox', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('report_id') ? 'has-error' : ''}}">
    <label for="report_id" class="control-label">{{ 'Report Id' }}</label>
    <input class="form-control" name="report_id" type="number" id="report_id" value="{{ isset( $stamp->report_id ) ? $stamp->report_id : '' }}" required>

    {!! $errors->first('report_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
