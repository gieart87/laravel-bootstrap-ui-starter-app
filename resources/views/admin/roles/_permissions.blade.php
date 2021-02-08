<div class="row">
    @foreach($permissions as $perm)
        <?php
            $per_found = null;

            if( isset($role) ) {
                $per_found = $role->hasPermissionTo($perm->name);
            }

            if( isset($user)) {
                $per_found = $user->hasDirectPermission($perm->name);
            }

            $labelName = Str::of($perm->name)->replace('_', ' ');
        ?>
        <div class="col-sm-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permissions[]" id="{{ $perm->id }}" value="{{ $perm->name }}" {{  ($per_found == true ? ' checked' : '') }} {{ $disabled ?? '' }}>
                <label class="form-check-label {{ Str::contains($perm->name, 'delete') ? 'text-danger' : '' }}" for="{{ $perm->id }}">
                    {{ $labelName }}
                </label>
            </div>
        </div>
    @endforeach
</div>