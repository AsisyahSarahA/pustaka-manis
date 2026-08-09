<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::orderBy('id')->get()->keyBy('key');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'school_name' => ['required', 'string', 'max:255'],
            'school_address' => ['nullable', 'string', 'max:255'],
            'loan_days_siswa' => ['required', 'integer', 'min:1', 'max:365'],
            'loan_days_guru' => ['required', 'integer', 'min:1', 'max:365'],
            'loan_days_staf' => ['required', 'integer', 'min:1', 'max:365'],
            'max_loan_siswa' => ['required', 'integer', 'min:1', 'max:100'],
            'max_loan_guru' => ['required', 'integer', 'min:1', 'max:100'],
            'max_loan_staf' => ['required', 'integer', 'min:1', 'max:100'],
            'fine_enabled' => ['sometimes', 'boolean'],
            'fine_per_day' => ['required', 'integer', 'min:0'],
            'fine_max_days' => ['required', 'integer', 'min:1', 'max:365'],
            'module_visitor_enabled' => ['sometimes', 'boolean'],
            'module_report_enabled' => ['sometimes', 'boolean'],
            'module_fine_enabled' => ['sometimes', 'boolean'],
            'module_member_card_enabled' => ['sometimes', 'boolean'],
            'app_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $filename = 'logo-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);

            $oldLogo = setting('app_logo');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }

            $data['app_logo'] = 'uploads/settings/' . $filename;
        }

        $booleanKeys = [
            'fine_enabled',
            'module_visitor_enabled',
            'module_report_enabled',
            'module_fine_enabled',
            'module_member_card_enabled',
        ];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key);
        }

        $integerKeys = [
            'loan_days_siswa', 'loan_days_guru', 'loan_days_staf',
            'max_loan_siswa', 'max_loan_guru', 'max_loan_staf',
            'fine_per_day', 'fine_max_days',
        ];

        foreach ($integerKeys as $key) {
            $data[$key] = (int) $data[$key];
        }

        $fields = [
            'app_name', 'school_name', 'school_address',
            'loan_days_siswa', 'loan_days_guru', 'loan_days_staf',
            'max_loan_siswa', 'max_loan_guru', 'max_loan_staf',
            'fine_enabled', 'fine_per_day', 'fine_max_days',
            'module_visitor_enabled', 'module_report_enabled',
            'module_fine_enabled', 'module_member_card_enabled',
            'app_logo',
        ];

        foreach ($fields as $key) {
            if ($key === 'app_logo' && !array_key_exists('app_logo', $data)) {
                continue;
            }

            $value = $data[$key] ?? false;

            $type = 'string';
            if (is_bool($value)) {
                $type = 'boolean';
                $value = $value ? 'true' : 'false';
            } elseif (is_int($value)) {
                $type = 'integer';
                $value = (string) $value;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => $type]
            );
        }

        clear_setting_cache();

        return redirect()->route('settings.index')
            ->with('toast', ['type' => 'success', 'message' => 'Pengaturan berhasil disimpan.']);
    }
}