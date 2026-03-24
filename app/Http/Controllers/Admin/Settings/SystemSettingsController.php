<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class SystemSettingsController extends Controller
{
    public function edit()
    {
        $openAiKey = config('services.openai.key') ?: env('OPEN_API_KEY');
        
        // If not found in environment, try manually parsing .env (in case it is commented out)
        if (empty($openAiKey)) {
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);
                if (preg_match('/^(?:#\s*)?OPEN_API_KEY=(.*)$/m', $envContent, $matches)) {
                    $openAiKey = trim($matches[1], " \"'");
                }
            }
        }

        return Inertia::render('Admin/Settings/System', [
            'settings' => [
                'mail_driver' => config('mail.mailer', 'smtp'),
                'mail_host' => config('mail.mailers.smtp.host', ''),
                'mail_port' => config('mail.mailers.smtp.port', '587'),
                'mail_username' => config('mail.mailers.smtp.username', ''),
                'mail_password' => config('mail.mailers.smtp.password', ''),
                'mail_encryption' => config('mail.mailers.smtp.encryption', 'tls'),
                'mail_from_address' => config('mail.from.address', ''),
                'mail_from_name' => config('mail.from.name', ''),
                'openai_api_key' => $openAiKey ?: '',
                'openai_model' => config('services.openai.model', 'gpt-4o-mini'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|string',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'openai_api_key' => 'nullable|string',
            'openai_model' => 'nullable|string',
        ]);

        $envData = [
            'MAIL_MAILER' => $validated['mail_driver'],
            'MAIL_HOST' => $validated['mail_host'],
            'MAIL_PORT' => $validated['mail_port'],
            'MAIL_USERNAME' => $validated['mail_username'] ?? '',
            'MAIL_PASSWORD' => $validated['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => $validated['mail_encryption'] ?? '',
            'MAIL_FROM_ADDRESS' => $validated['mail_from_address'],
            'MAIL_FROM_NAME' => $validated['mail_from_name'],
            'OPEN_API_KEY' => $validated['openai_api_key'] ?? '',
            'OPENAI_MODEL' => $validated['openai_model'] ?? 'gpt-4o-mini',
        ];

        $this->updateEnv($envData);

        // Clear config cache to apply changes
        Artisan::call('config:clear');

        return back()->with('success', 'System settings updated successfully.');
    }

    protected function updateEnv(array $data)
    {
        $path = base_path('.env');

        if (! file_exists($path)) {
            return;
        }

        $content = file_get_contents($path);

        foreach ($data as $key => $value) {
            $value = trim($value);

            // Wrap in quotes if it contains sensitive characters or spaces
            if (str_contains($value, ' ') || preg_match('/[#&"\'$]/', $value)) {
                $value = '"'.str_replace('"', '\"', $value).'"';
            }

            $pattern = "/^(?:#\s*)?{$key}=.*/m";
            $replace = "{$key}={$value}";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replace, $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($path, $content);
    }

}
