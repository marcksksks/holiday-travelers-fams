<?php

namespace App\Services;

use App\Models\ArchiveDocument;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

/** Port of base44/functions/documentAccess/entry.ts */
class DocumentAccessService
{
    private const GENERAL = ['employee', 'receptionist', 'admin_officer', 'manager', 'legal_officer', 'sys_admin'];
    private const RESTRICTED = ['admin_officer', 'manager', 'legal_officer', 'sys_admin'];
    private const CONFIDENTIAL = ['admin_officer', 'legal_officer', 'sys_admin'];

    public function __construct(private AuditService $audit) {}

    /**
     * Authorises access to a privately-stored document, then returns a
     * short-lived (5 minute) signed URL — same TTL as the original.
     */
    public function signedUrl(User $user, ArchiveDocument $doc): string
    {
        if (! $doc->file_uri) {
            throw ValidationException::withMessages(['file_uri' => 'No file attached to this record.']);
        }

        $this->assertCanAccess($user, $doc);
        $role = $user->app_role;
        $isOwner = $doc->owner_email === $user->email || $doc->uploaded_by_email === $user->email;
        $level = $doc->confidentiality ?: 'general';

        $allowed = match ($level) {
            'general' => in_array($role, self::GENERAL, true),
            'restricted' => in_array($role, self::RESTRICTED, true) || $isOwner,
            default => in_array($role, self::CONFIDENTIAL, true) || $isOwner, // confidential
        };

        if (! $allowed) {
            $this->audit->log($user, 'update', 'documents', "Denied access • {$doc->title}", (string) $doc->id, "Confidentiality: {$level}");
            throw ValidationException::withMessages(['confidentiality' => 'You are not authorised to open this document.'])->status(403);
        }

        $url = URL::temporarySignedRoute('documents.download', now()->addMinutes(5), ['document' => $doc->id]);

        $this->audit->log($user, 'update', 'documents', "Opened • {$doc->title}", (string) $doc->id, 'Version '.($doc->version ?: 1));

        return $url;
    }

    public function assertCanAccess(User $user, ArchiveDocument $doc): void
    {
        $role = $user->app_role;
        $isOwner = $doc->owner_email === $user->email || $doc->uploaded_by_email === $user->email;
        $level = $doc->confidentiality ?: 'general';
        $allowed = match ($level) {
            'general' => in_array($role, self::GENERAL, true),
            'restricted' => in_array($role, self::RESTRICTED, true) || $isOwner,
            default => in_array($role, self::CONFIDENTIAL, true) || $isOwner,
        };
        if (! $allowed) {
            $this->audit->log($user, 'access_denied', 'documents', "Denied access • {$doc->title}", (string) $doc->id, "Confidentiality: {$level}");
            throw ValidationException::withMessages(['confidentiality' => 'You are not authorised to open this document.'])->status(403);
        }
    }
}
