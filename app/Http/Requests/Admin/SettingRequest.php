<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Setting;

class SettingRequest extends FormRequest
{

    public function __construct()
    {
        \Validator::extend("emails", function ($attribute, $value, $parameters) {
            $rules = [
                'email' => 'required|email',
            ];
  
            foreach (explode(',', $value) as $email) {
                $data = [
                    'email' => trim($email)
                ];
                $validator = \Validator::make($data, $rules);
                if ($validator->fails()) {
                    return false;
                }
            }
            return true;
        });
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validationRules = $this->buildValidationRules($this->all());
        
        return $validationRules;
    }

    private function buildValidationRules($params)
    {
        $settingKeys = array_keys($params);
        return Setting::whereIn('setting_key', $settingKeys)->pluck('validation_rules', 'setting_key')->toArray();
    }
}
