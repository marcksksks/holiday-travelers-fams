<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\ContractApiController;
use App\Http\Controllers\Api\LegalRecordApiController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\LegalRecordController;
use App\Http\Requests\ContractRequest;
use App\Http\Requests\LegalRecordRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use ReflectionMethod;
use ReflectionNamedType;
use Tests\TestCase;

class ContractLegalUploadSecurityTest extends TestCase
{
    public function test_contract_and_legal_requests_reject_html_uploads(): void
    {
        $contractFile = UploadedFile::fake()
            ->createWithContent(
                'unsafe.html',
                '<html><script>alert(1)</script></html>'
            );

        $legalFile = UploadedFile::fake()
            ->createWithContent(
                'unsafe.html',
                '<html><script>alert(1)</script></html>'
            );

        $contractValidator = Validator::make(
            ['file' => $contractFile],
            [
                'file' => (new ContractRequest())->rules()['file'],
            ]
        );

        $legalValidator = Validator::make(
            ['file' => $legalFile],
            [
                'file' => (new LegalRecordRequest())->rules()['file'],
            ]
        );

        $this->assertTrue(
            $contractValidator->fails()
        );

        $this->assertTrue(
            $legalValidator->fails()
        );
    }

    public function test_contract_and_legal_requests_reject_php_content_disguised_as_pdf(): void
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
            $contractFile = new UploadedFile(
                $temporaryPath,
                'malicious.pdf',
                null,
                null,
                true
            );

            $contractValidator = Validator::make(
                ['file' => $contractFile],
                [
                    'file' => (new ContractRequest())->rules()['file'],
                ]
            );

            $this->assertTrue(
                $contractValidator->fails()
            );

            $legalFile = new UploadedFile(
                $temporaryPath,
                'malicious.pdf',
                null,
                null,
                true
            );

            $legalValidator = Validator::make(
                ['file' => $legalFile],
                [
                    'file' => (new LegalRecordRequest())->rules()['file'],
                ]
            );

            $this->assertTrue(
                $legalValidator->fails()
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

    public function test_contract_and_legal_requests_accept_pdf_uploads(): void
    {
        $contractFile = UploadedFile::fake()
            ->createWithContent(
                'contract.pdf',
                "%PDF-1.4\n"
                ."1 0 obj\n"
                ."<< /Type /Catalog >>\n"
                ."endobj\n"
                ."%%EOF"
            );

        $legalFile = UploadedFile::fake()
            ->createWithContent(
                'legal.pdf',
                "%PDF-1.4\n"
                ."1 0 obj\n"
                ."<< /Type /Catalog >>\n"
                ."endobj\n"
                ."%%EOF"
            );

        $contractValidator = Validator::make(
            ['file' => $contractFile],
            [
                'file' => (new ContractRequest())->rules()['file'],
            ]
        );

        $legalValidator = Validator::make(
            ['file' => $legalFile],
            [
                'file' => (new LegalRecordRequest())->rules()['file'],
            ]
        );

        $this->assertFalse(
            $contractValidator->fails(),
            $contractValidator
                ->errors()
                ->first('file')
        );

        $this->assertFalse(
            $legalValidator->fails(),
            $legalValidator
                ->errors()
                ->first('file')
        );
    }

    public function test_web_and_api_mutations_use_hardened_form_requests(): void
    {
        $expectations = [
            [
                ContractController::class,
                'store',
                ContractRequest::class,
            ],
            [
                ContractController::class,
                'update',
                ContractRequest::class,
            ],
            [
                ContractApiController::class,
                'store',
                ContractRequest::class,
            ],
            [
                ContractApiController::class,
                'update',
                ContractRequest::class,
            ],
            [
                LegalRecordController::class,
                'store',
                LegalRecordRequest::class,
            ],
            [
                LegalRecordController::class,
                'update',
                LegalRecordRequest::class,
            ],
            [
                LegalRecordApiController::class,
                'store',
                LegalRecordRequest::class,
            ],
            [
                LegalRecordApiController::class,
                'update',
                LegalRecordRequest::class,
            ],
        ];

        foreach (
            $expectations as [
                $controller,
                $method,
                $expectedRequest,
            ]
        ) {
            $reflection =
                new ReflectionMethod(
                    $controller,
                    $method
                );

            $parameter =
                $reflection->getParameters()[0];

            $type =
                $parameter->getType();

            $this->assertInstanceOf(
                ReflectionNamedType::class,
                $type
            );

            $this->assertSame(
                $expectedRequest,
                $type->getName(),
                "{$controller}::{$method} must use {$expectedRequest}."
            );
        }
    }
}