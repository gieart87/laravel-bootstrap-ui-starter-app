<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\SettingRequest;

use App\Authorizable;
use App\Models\Setting;

class SettingController extends Controller
{
    use Authorizable;

    public function __construct()
    {
        parent::__construct();

        $this->data['currentAdminMenu'] = 'settings';
    }

    public function index(Request $request)
    {
        $currentCategory = ($request->get('category') == '') ? 'general' : $request->get('category');
        $this->data['currentCategory'] = $currentCategory;
        $this->data['settings'] = Setting::getSettings()[$currentCategory];
        $this->data['categories'] = Setting::getCategories();
        

        return view('admin.settings.index', $this->data);
    }

    public function update(SettingRequest $request)
    {
        $params = $request->except('_token');

        $updateSetting = true;
        $settingKeys = Setting::whereIn('setting_key', array_keys($params))->get()->pluck('setting_key')->toArray();

        if ($params) {
            foreach ($params as $settingKey => $settingValue) {
                if (in_array($settingKey, $settingKeys) && !$this->updateSetting($settingKey, $settingValue)) {
                    $updateSetting = false;
                    break;
                }
            }
        }

        if ($updateSetting) {
            return redirect('admin/settings')->with('success', 'Setting has been updated.');
        }

        return redirect('admin/settings')->with('error', 'Some setting has not updated.');
    }

    public function remove($id)
    {
        $setting = Setting::findOrFail($id);
        $setting[$setting->setting_type . '_value'] = null;
        if ($setting->save()) {
            return redirect('admin/settings')->with('success', 'Setting has been updated.');
        }

        return redirect('admin/settings')->with('error', 'Some setting has not updated.');
    }

    private function updateSetting($settingKey, $settingValue)
    {
        $setting = Setting::where('setting_key', $settingKey)->first();
        if (!$setting) {
            return;
        }

        if ($setting->setting_type == 'file' && $settingValue) {
            $settingValue = $this->uploadFile($setting, $settingValue);
        }

        $setting[$setting->setting_type . '_value'] = $settingValue;
        return $setting->save();
    }

    private function uploadFile($setting, $settingValue)
    {
        $setting->clearMediaCollection('images');
        $setting->addMediaFromRequest($setting->setting_key)->toMediaCollection('images');
        return $setting->getFirstMedia('images')->getUrl();
    }
}
