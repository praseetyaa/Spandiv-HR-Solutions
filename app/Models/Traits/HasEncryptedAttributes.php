<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Crypt;

trait HasEncryptedAttributes
{
    /**
     * Boot the trait.
     * Auto-encrypt on saving, auto-decrypt on retrieval.
     */
    public static function bootHasEncryptedAttributes(): void
    {
        static::saving(function ($model) {
            foreach ($model->getEncryptedFields() as $field) {
                if (! empty($model->{$field}) && ! $model->isEncrypted($model->{$field})) {
                    $model->{$field} = Crypt::encryptString($model->{$field});
                }
            }
        });
    }

    /**
     * Override getAttribute to auto-decrypt.
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        if (in_array($key, $this->getEncryptedFields()) && ! empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value; // Return raw value if decryption fails
            }
        }

        return $value;
    }

    /**
     * Get the list of fields that should be encrypted.
     * Override $encryptedFields in your model.
     */
    public function getEncryptedFields(): array
    {
        return property_exists($this, 'encryptedFields')
            ? $this->encryptedFields
            : [];
    }

    /**
     * Check if a value is already encrypted.
     */
    protected function isEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
