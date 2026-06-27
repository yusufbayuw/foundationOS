<?php

namespace Modules\Exam\Support;

use Modules\Exam\Enums\GradebookExportStatus;

readonly class GradebookExportOutcome
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public GradebookExportStatus $status,
        public ?string $targetReferenceType = null,
        public ?int $targetReferenceId = null,
        public ?string $errorMessage = null,
        public array $metadata = [],
    ) {}

    /**
     * @param  array<string, mixed>  $metadata
     */
    public static function success(string $targetReferenceType, int $targetReferenceId, array $metadata = []): self
    {
        return new self(
            status: GradebookExportStatus::Success,
            targetReferenceType: $targetReferenceType,
            targetReferenceId: $targetReferenceId,
            metadata: $metadata,
        );
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public static function skipped(string $message, array $metadata = []): self
    {
        return new self(
            status: GradebookExportStatus::Skipped,
            errorMessage: $message,
            metadata: $metadata,
        );
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public static function failed(string $message, array $metadata = []): self
    {
        return new self(
            status: GradebookExportStatus::Failed,
            errorMessage: $message,
            metadata: $metadata,
        );
    }
}
