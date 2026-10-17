<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PublicTarget implements ValidationRule
{

    public function __construct(private string $type)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->type === 'http') {
            $parts = parse_url($value);

            if (! $parts || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true) || empty($parts['host'])) {
                $fail('L\'URL doit commencer par http:// ou https://.');
                return;
            }

            $host = $parts['host'];
        } else {
            if (! preg_match('/^([^:\s]+):(\d{1,5})$/', $value, $matches)) {
                $fail('Le format attendu est host:port.');
                return;
            }

            $host = $matches[1];
        }

        // Un domaine qui ne résout pas est autorisé : on doit pouvoir surveiller un site déjà en panne.
        foreach (gethostbynamel($host) ?: [] as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $fail('Les adresses privées ou réservées ne sont pas autorisées.');
                return;
            }
        }
    }
}
