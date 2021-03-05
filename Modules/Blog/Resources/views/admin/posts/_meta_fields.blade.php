@if ($metaFields)
    <div class="card">
        <div class="card-header">
            <h4>Meta Fields</h4>
        </div>
        <div class="card-body">
            @foreach ($metaFields as $field => $fieldAttributes)
                <div class="form-group row">
                    {!! Form::label($field, $fieldAttributes['label'], ['class' => 'col-sm-3 col-form-label ']) !!}
                    <div class="col-sm-9">
                        @php
                            $metaFieldValue = (!empty($post->metas)) && !empty($post->metas[$field]) ? $post->metas[$field] : null;
                            if ($fieldAttributes['type'] == 'select' && $metaFieldValue == null) {
                                $metaFieldValue = [];
                            }

                        @endphp
                        @switch($fieldAttributes['type'])
                            @case('textarea')
                                {!! Form::textarea($fieldAttributes['field_name'], $metaFieldValue, ['class' => $fieldAttributes['class']]) !!}
                                @break
                            @case('select')
                                @php
                                    $metaFieldOptions = [];
                                    foreach ($metaFieldValue as $key => $value) {
                                        $metaFieldOptions[$value] = $value;
                                    }
                                @endphp
                                {!! Form::select($fieldAttributes['field_name'], $metaFieldOptions, array_values($metaFieldValue), ['class' => $fieldAttributes['class'], 'multiple' => $fieldAttributes['multiple']]) !!}
                                @break
                            @default
                                {!! Form::text($fieldAttributes['field_name'], $metaFieldValue, ['class' => $fieldAttributes['class']]) !!}
                        @endswitch
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif