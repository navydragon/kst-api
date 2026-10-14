<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        return Setting::orderBy('key')->get(['id', 'key', 'value']);
    }

    public function store(Request $request)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $data = $this->validateSetting($request);

        $setting = Setting::create($data);

        return response()->json($setting, 201);
    }

    public function update(Request $request, Setting $setting)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $data = $this->validateSetting($request, $setting->id);
        $setting->update($data);

        return response()->json($setting);
    }

    public function destroy(Setting $setting)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $setting->delete();

        return response()->json(['message' => 'Настройка удалена']);
    }

    public function publicSettings()
    {
        $allowedStates = ['warning', 'danger', 'success', 'info'];
        $state = Setting::getValue('alert_state', 'info');
        if (!in_array($state, $allowedStates, true)) {
            $state = 'info';
        }

        $showAlert = filter_var(Setting::getValue('show_alert', 'false'), FILTER_VALIDATE_BOOLEAN);

        return response()->json([
            'show_alert' => $showAlert,
            'alert_state' => $state,
            'alert_message' => Setting::getValue('alert_message', ''),
        ]);
    }

    private function denyUnlessAdmin()
    {
        $user = Auth::user();
        if (!$user || (int) $user->role_id !== 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Недостаточно прав',
            ], 403);
        }

        return null;
    }

    private function validateSetting(Request $request, $ignoreId = null)
    {
        $keyRules = ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_]+$/'];
        $unique = Rule::unique('settings', 'key');
        if ($ignoreId) {
            $unique = $unique->ignore($ignoreId);
        }
        $keyRules[] = $unique;

        $valueRules = ['required', 'string'];
        if ($request->input('key') === 'alert_state') {
            $valueRules[] = 'in:warning,danger,success,info';
        }
        if ($request->input('key') === 'show_alert') {
            $valueRules[] = 'in:true,false';
        }

        return $request->validate([
            'key' => $keyRules,
            'value' => $valueRules,
        ], [
            'key.required' => 'Укажите ключ',
            'key.regex' => 'Ключ может содержать только латинские буквы, цифры и знак _',
            'key.unique' => 'Настройка с таким ключом уже есть',
            'key.max' => 'Ключ слишком длинный',
            'value.required' => 'Укажите значение',
            'value.in' => 'Недопустимое значение для этого ключа',
        ]);
    }
}
