<?php

namespace Tests\Feature;

use App\Support\DocumentUploadPolicy;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class DocumentUploadPolicyTest extends TestCase
{
    public function test_valid_pdf_is_accepted(): void
    {
        $file = UploadedFile::fake()
            ->createWithContent(
                'document.pdf',
                "%PDF-1.4\n"
                ."1 0 obj\n"
                ."<< /Type /Catalog >>\n"
                ."endobj\n"
                ."%%EOF"
            );

        $validator = Validator::make(
            ['file' => $file],
            [
                'file' =>
                    DocumentUploadPolicy::rules(
                        true
                    ),
            ]
        );

        $this->assertFalse(
            $validator->fails(),
            $validator->errors()->first('file')
        );
    }

    public function test_html_file_is_rejected(): void
    {
        $file = UploadedFile::fake()
            ->createWithContent(
                'page.html',
                '<!doctype html>'
                .'<html>'
                .'<body>unsafe</body>'
                .'</html>'
            );

        $validator = Validator::make(
            ['file' => $file],
            [
                'file' =>
                    DocumentUploadPolicy::rules(
                        true
                    ),
            ]
        );

        $this->assertTrue(
            $validator->fails()
        );
    }

    public function test_php_content_disguised_as_pdf_is_rejected(): void
    {
        $temporaryPath = tempnam(
            sys_get_temp_dir(),
            'fams-upload-'
        );

        $this->assertNotFalse(
            $temporaryPath
        );

        file_put_contents(
            $temporaryPath,
            '<?php echo "unsafe"; ?>'
        );

        try {
            $file = new UploadedFile(
                $temporaryPath,
                'malicious.pdf',
                null,
                null,
                true
            );

            $validator = Validator::make(
                [
                    'file' => $file,
                ],
                [
                    'file' =>
                        DocumentUploadPolicy::rules(
                            true
                        ),
                ]
            );

            $this->assertTrue(
                $validator->fails()
            );
        } finally {
            if (
                is_string($temporaryPath) &&
                is_file($temporaryPath)
            ) {
                unlink($temporaryPath);
            }
        }
    }

    public function test_valid_pdf_content_with_php_extension_is_rejected(): void
    {
        $file = UploadedFile::fake()
            ->createWithContent(
                'document.php',
                "%PDF-1.4\n"
                ."1 0 obj\n"
                ."<< /Type /Catalog >>\n"
                ."endobj\n"
                ."%%EOF"
            );

        $validator = Validator::make(
            ['file' => $file],
            [
                'file' =>
                    DocumentUploadPolicy::rules(
                        true
                    ),
            ]
        );

        $this->assertTrue(
            $validator->fails()
        );
    }

    public function test_file_larger_than_twenty_megabytes_is_rejected(): void
    {
        $file = UploadedFile::fake()
            ->create(
                'large.pdf',
                DocumentUploadPolicy::MAX_SIZE_KB
                    + 1,
                'application/pdf'
            );

        $validator = Validator::make(
            ['file' => $file],
            [
                'file' =>
                    DocumentUploadPolicy::rules(
                        true
                    ),
            ]
        );

        $this->assertTrue(
            $validator->fails()
        );
    }
}