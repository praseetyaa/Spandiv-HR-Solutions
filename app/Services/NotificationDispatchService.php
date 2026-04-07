<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\User;

class NotificationDispatchService
{
    public function send(string $eventKey, User $user, array $data = []): void
    {
        $tenantId = $user->tenant_id;

        // In-app notification always
        $this->sendInApp($eventKey, $user, $data);

        // Check templates for other channels
        $templates = NotificationTemplate::where(fn ($q) => $q
                ->where('tenant_id', $tenantId)
                ->orWhereNull('tenant_id')
            )
            ->where('event_key', $eventKey)
            ->where('is_active', true)
            ->get();

        foreach ($templates as $template) {
            $body = $this->parseTemplate($template->body_template, $data);

            match ($template->channel) {
                'email'    => $this->sendEmail($user, $template->subject, $body, $data),
                'whatsapp' => $this->sendWhatsApp($user, $body),
                default    => null, // in_app already sent above
            };
        }
    }

    protected function sendInApp(string $eventKey, User $user, array $data): void
    {
        Notification::create([
            'tenant_id'  => $user->tenant_id,
            'user_id'    => $user->id,
            'type'       => $eventKey,
            'title'      => $data['title'] ?? $this->generateTitle($eventKey),
            'body'       => $data['body'] ?? $data['message'] ?? '',
            'data'       => $data,
            'action_url' => $data['action_url'] ?? null,
        ]);
    }

    protected function sendEmail(User $user, ?string $subject, string $body, array $data): void
    {
        // Uses Laravel Mail — implementation depends on mail driver
        try {
            \Illuminate\Support\Facades\Mail::raw($body, function ($message) use ($user, $subject) {
                $message->to($user->email)->subject($subject ?? config('app.name'));
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Email notification failed: {$e->getMessage()}");
        }
    }

    protected function sendWhatsApp(User $user, string $body): void
    {
        $token = config('services.fonnte.token');
        $url   = config('services.fonnte.url', 'https://api.fonnte.com/send');

        if (!$token || !$user->employee?->phone) return;

        try {
            \Illuminate\Support\Facades\Http::withHeaders(['Authorization' => $token])
                ->post($url, [
                    'target'  => $user->employee->phone,
                    'message' => $body,
                ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("WhatsApp notification failed: {$e->getMessage()}");
        }
    }

    protected function parseTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $template = str_replace("{{{$key}}}", (string) $value, $template);
            }
        }
        return $template;
    }

    protected function generateTitle(string $eventKey): string
    {
        return ucfirst(str_replace(['_', '.'], ' ', $eventKey));
    }
}
