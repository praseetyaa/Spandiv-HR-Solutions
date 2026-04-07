<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class ApiTokenManager extends Component
{
    public bool $hasToken = false;
    public string $maskedToken = '';
    public ?string $newPlaintextToken = null;
    public bool $showRegenerateConfirm = false;
    public ?string $tokenCreatedAt = null;

    public function mount(): void
    {
        $this->loadTokenState();
    }

    protected function loadTokenState(): void
    {
        $tenant = auth()->user()->tenant;

        $this->hasToken = !empty($tenant->api_token);

        if ($this->hasToken) {
            // Show masked version: first 8 chars of the hash + ••••••••
            $this->maskedToken = substr($tenant->api_token, 0, 8) . '••••••••••••••••';
        }
    }

    /**
     * Generate a brand-new API token for the tenant.
     */
    public function generateToken(): void
    {
        $tenant = auth()->user()->tenant;
        $this->newPlaintextToken = $tenant->generateApiToken();
        $this->hasToken = true;
        $this->maskedToken = substr($tenant->fresh()->api_token, 0, 8) . '••••••••••••••••';
        $this->showRegenerateConfirm = false;

        session()->flash('api_success', 'API token berhasil dibuat! Salin dan simpan token ini — tidak akan ditampilkan lagi.');
    }

    /**
     * Show the regeneration confirmation dialog.
     */
    public function confirmRegenerate(): void
    {
        $this->showRegenerateConfirm = true;
    }

    /**
     * Cancel regeneration.
     */
    public function cancelRegenerate(): void
    {
        $this->showRegenerateConfirm = false;
    }

    /**
     * Regenerate the token — replaces the old one permanently.
     */
    public function regenerateToken(): void
    {
        $this->generateToken();
    }

    /**
     * Revoke (delete) the API token.
     */
    public function revokeToken(): void
    {
        $tenant = auth()->user()->tenant;
        $tenant->update(['api_token' => null]);

        $this->hasToken = false;
        $this->maskedToken = '';
        $this->newPlaintextToken = null;
        $this->showRegenerateConfirm = false;

        session()->flash('api_warning', 'API token telah dicabut. Semua integrasi menggunakan token ini akan berhenti berfungsi.');
    }

    /**
     * Dismiss the plaintext token display.
     */
    public function dismissToken(): void
    {
        $this->newPlaintextToken = null;
    }

    public function render()
    {
        return view('livewire.settings.api-token-manager')
            ->layout('layouts.app', ['pageTitle' => 'API Management']);
    }
}
