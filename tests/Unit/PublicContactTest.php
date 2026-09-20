<?php

namespace Tests\Unit;

use App\Support\PublicContact;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PublicContactTest extends TestCase
{
    public function test_colombian_mobile_uses_country_code_for_whatsapp(): void
    {
        Config::set('contact.whatsapp', '3178362567');
        Config::set('contact.email', 'decowandy2025@hotmail.com');

        $this->assertTrue(PublicContact::hasWhatsapp());
        $this->assertSame('573178362567', PublicContact::whatsappE164());
        $this->assertSame('317 836 2567', PublicContact::whatsappDisplay());
        $this->assertStringContainsString('https://wa.me/573178362567', PublicContact::whatsappHref());
        $this->assertSame('decowandy2025@hotmail.com', PublicContact::email());
    }

    public function test_empty_whatsapp_falls_back_to_contact_anchor(): void
    {
        Config::set('contact.whatsapp', '');

        $this->assertFalse(PublicContact::hasWhatsapp());
        $this->assertSame('#contacto', PublicContact::whatsappHref());
    }
}
