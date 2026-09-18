<?php

namespace Tests\Feature;

use Illuminate\Encryption\Encrypter;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\EncryptedStore;
use Illuminate\Session\Store;
use Tests\TestCase;

class SessionEncryptionSecurityTest extends TestCase
{
    private function encrypter(): Encrypter
    {
        return new Encrypter(
            str_repeat('a', 32),
            'AES-256-CBC'
        );
    }

    public function test_encrypted_store_encrypts_session_payload_at_rest(): void
    {
        $handler =
            new ArraySessionHandler(120);

        $encrypter =
            $this->encrypter();

        $store =
            new EncryptedStore(
                'fams_session',
                $handler,
                $encrypter
            );

        $store->start();

        $sessionId =
            $store->getId();

        $this->assertNotSame(
            '',
            $sessionId
        );

        $store->put(
            'security_probe',
            'sensitive-session-value'
        );

        $store->save();

        $raw =
            $handler->read(
                $sessionId
            );

        $this->assertIsString(
            $raw
        );

        $this->assertNotSame(
            '',
            $raw
        );

        $this->assertStringNotContainsString(
            'sensitive-session-value',
            $raw
        );

        $reader =
            new EncryptedStore(
                'fams_session',
                $handler,
                $encrypter,
                $sessionId
            );

        $reader->start();

        $this->assertSame(
            'sensitive-session-value',
            $reader->get(
                'security_probe'
            )
        );
    }

    public function test_legacy_plain_session_becomes_empty_when_encryption_is_enabled(): void
    {
        $handler =
            new ArraySessionHandler(120);

        $plain =
            new Store(
                'fams_session',
                $handler
            );

        $plain->start();

        $sessionId =
            $plain->getId();

        $this->assertNotSame(
            '',
            $sessionId
        );

        $plain->put(
            'security_probe',
            'legacy-value'
        );

        $plain->save();

        $legacyRaw =
            $handler->read(
                $sessionId
            );

        $this->assertIsString(
            $legacyRaw
        );

        $this->assertNotSame(
            '',
            $legacyRaw
        );

        $this->assertStringContainsString(
            'legacy-value',
            $legacyRaw
        );

        $encrypted =
            new EncryptedStore(
                'fams_session',
                $handler,
                $this->encrypter(),
                $sessionId
            );

        $encrypted->start();

        $this->assertNull(
            $encrypted->get(
                'security_probe'
            )
        );
    }
}