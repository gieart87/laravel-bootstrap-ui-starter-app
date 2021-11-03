@switch($setting->setting_type)
    @case('string')
        <input type="text" name="{{ $setting->setting_key }}" class="form-control" id="{{ $setting->setting_key }}" value="{{ $setting[$setting->setting_type . '_value'] }}">
        @break
    @case('file')
        <div class="custom-file">
            <input type="file" name="{{ $setting->setting_key }}" class="custom-file-input" id="{{ $setting->setting_key }}">
            <label class="custom-file-label">Choose File</label>
        </div>
        @break
    @default
@endswitch
