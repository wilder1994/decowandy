<?php

namespace App\Support;

final class PublicContact
{
    public static function email(): string
    {
        return (string) config('contact.email');
    }

    public static function whatsappLocal(): string
    {
        return preg_replace('/\D+/', '', (string) config('contact.whatsapp')) ?: '';
    }

    public static function hasWhatsapp(): bool
    {
        return strlen(self::whatsappLocal()) >= 10;
    }

    public static function whatsappE164(): string
    {
        $digits = self::whatsappLocal();
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '57') && strlen($digits) >= 12) {
            return $digits;
        }

        return '57'.$digits;
    }

    public static function whatsappHref(string $message = 'Hola DecoWandy, quiero realizar un pedido'): string
    {
        if (! self::hasWhatsapp()) {
            return '#contacto';
        }

        return 'https://wa.me/'.self::whatsappE164().'?text='.rawurlencode($message);
    }

    public static function whatsappDisplay(): string
    {
        $digits = self::whatsappLocal();
        if (strlen($digits) === 10) {
            return substr($digits, 0, 3).' '.substr($digits, 3, 3).' '.substr($digits, 6);
        }

        return $digits;
    }
}
