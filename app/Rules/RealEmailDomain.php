<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RealEmailDomain implements ValidationRule
{
    /**
     * Common disposable & temporary email domains blacklist.
     */
    protected static array $disposableDomains = [
        'mailinator.com', 'tempmail.com', 'temp-mail.org', 'temp-mail.io',
        'guerrillamail.com', 'guerrillamail.net', 'guerrillamail.org', 'guerrillamail.biz', 'guerrillamailblock.com',
        'yopmail.com', 'yopmail.fr', 'yopmail.net', 'cool.fr.nf', 'jetable.fr.nf',
        '10minutemail.com', '10minutemail.net', '10minutemail.org', '10minutemail.co.uk',
        'trashmail.com', 'trashmail.net', 'trashmail.me', 'trashmail.org',
        'sharklasers.com', 'grr.la', 'pokemail.net', 'spam4.me',
        'dispostable.com', 'fakeinbox.com', 'fakemailgenerator.com',
        'generator.email', 'crazymailing.com', 'mohmal.com', 'emailondeck.com',
        'dropmail.me', 'inboxkitten.com', 'mytemp.email', 'tempr.email',
        'disposablemail.com', 'spambox.us', 'mytempemail.com', 'tmpmail.net',
        'tmpmail.org', 'fakemail.net', 'tempmailaddress.com', 'maildrop.cc',
        'tempail.com', 'armyspy.com', 'cuvox.de', 'dayrep.com', 'fleckens.hu',
        'gustr.com', 'jourrapide.com', 'rhyta.com', 'superrito.com', 'teleworm.us',
        'einrot.com', 'getairmail.com', 'throwawaymail.com', 'nada.ltd',
        'burnermail.io', 'mailnesia.com', 'spambog.com', 'trashymail.com',
        'generator.email', 'emailfake.com', 'crazymail.com', 'tempmailo.com',
        'test.com', 'example.com', 'dummy.com', 'asdf.com', 'test.test',
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('Format alamat email tidak valid.');
            return;
        }

        $parts = explode('@', strtolower(trim($value)));
        if (count($parts) !== 2) {
            $fail('Format alamat email tidak valid.');
            return;
        }

        $domain = $parts[1];

        // 1. Reject blocked disposable domains
        if (in_array($domain, self::$disposableDomains, true)) {
            $fail('Pendaftaran menggunakan penyedia email sementara (disposable/temporary) tidak diizinkan. Gunakan email pribadi resmi (seperti Gmail, Yahoo, Outlook, iCloud, atau email kantor).');
            return;
        }

        // 2. Reject fake / invalid TLDs
        if (preg_match('/\.(test|local|invalid|example|dummy|localhost)$/i', $domain)) {
            $fail('Domain email tidak valid. Gunakan alamat email asli yang aktif.');
            return;
        }

        // 3. DNS MX / A record check (when server has internet connectivity)
        if (function_exists('checkdnsrr')) {
            $hasMx = @checkdnsrr($domain, 'MX');
            $hasA  = @checkdnsrr($domain, 'A');

            if (!$hasMx && !$hasA) {
                $fail('Domain email (' . $domain . ') tidak ditemukan atau tidak dapat menerima email. Pastikan Anda memasukkan alamat email yang benar dan aktif.');
            }
        }
    }
}
