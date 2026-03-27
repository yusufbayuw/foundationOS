<?php

namespace Modules\Core\Support;

use Filament\Notifications\Notification;
use Modules\Core\Models\User;

/**
 * Centralized service to dispatch Filament database notifications
 * for key business events across all modules.
 *
 * Usage:
 *   NotificationService::paymentVerified($payment, $verifier);
 *   NotificationService::workflowStepAssigned($instance, $assignee);
 */
class NotificationService
{
    /**
     * Notify relevant users when a payment is verified.
     */
    public static function paymentVerified(
        \Modules\Finance\Models\Payment $payment,
        User $verifier,
    ): void {
        $invoice = $payment->studentInvoice;
        $amount = number_format((float) $payment->amount, 0, ',', '.');

        // Notify the verifier (confirmation)
        Notification::make()
            ->title('Pembayaran Diverifikasi')
            ->body("Pembayaran Rp {$amount} untuk tagihan {$invoice?->invoice_number} berhasil diverifikasi.")
            ->icon('heroicon-o-check-circle')
            ->success()
            ->sendToDatabase($verifier);
    }

    /**
     * Notify relevant users when a payment is rejected.
     */
    public static function paymentRejected(
        \Modules\Finance\Models\Payment $payment,
        User $rejector,
    ): void {
        $amount = number_format((float) $payment->amount, 0, ',', '.');

        Notification::make()
            ->title('Pembayaran Ditolak')
            ->body("Pembayaran Rp {$amount} (#{$payment->payment_number}) telah ditolak.")
            ->icon('heroicon-o-x-circle')
            ->danger()
            ->sendToDatabase($rejector);
    }

    /**
     * Notify assignees when a workflow step is assigned to them.
     */
    public static function workflowStepAssigned(
        \Modules\Workflow\Models\WorkflowInstance $instance,
        User $assignee,
    ): void {
        $label = $instance->subject_label ?: 'Workflow #' . $instance->getKey();

        Notification::make()
            ->title('Tugas Workflow Baru')
            ->body("Anda mendapat tugas approval untuk: {$label}.")
            ->icon('heroicon-o-clipboard-document-list')
            ->warning()
            ->sendToDatabase($assignee);
    }

    /**
     * Notify requester when workflow is completed.
     */
    public static function workflowCompleted(
        \Modules\Workflow\Models\WorkflowInstance $instance,
        User $requester,
    ): void {
        $label = $instance->subject_label ?: 'Workflow #' . $instance->getKey();

        Notification::make()
            ->title('Workflow Selesai')
            ->body("Workflow untuk \"{$label}\" telah selesai dan disetujui.")
            ->icon('heroicon-o-check-badge')
            ->success()
            ->sendToDatabase($requester);
    }

    /**
     * Notify requester when workflow is rejected.
     */
    public static function workflowRejected(
        \Modules\Workflow\Models\WorkflowInstance $instance,
        User $requester,
        ?string $reason = null,
    ): void {
        $label = $instance->subject_label ?: 'Workflow #' . $instance->getKey();
        $body = "Workflow untuk \"{$label}\" ditolak.";
        if ($reason) {
            $body .= " Alasan: {$reason}";
        }

        Notification::make()
            ->title('Workflow Ditolak')
            ->body($body)
            ->icon('heroicon-o-x-circle')
            ->danger()
            ->sendToDatabase($requester);
    }

    /**
     * Notify a library member about an overdue loan.
     */
    public static function loanOverdue(
        \Modules\Library\Models\Loan $loan,
        User $borrower,
    ): void {
        $bookTitle = $loan->bookCopy?->book?->title ?? 'Buku';
        $dueDate = $loan->due_date?->translatedFormat('d F Y') ?? '-';

        Notification::make()
            ->title('Peminjaman Terlambat')
            ->body("Buku \"{$bookTitle}\" jatuh tempo pada {$dueDate}. Segera kembalikan untuk menghindari denda.")
            ->icon('heroicon-o-exclamation-triangle')
            ->warning()
            ->sendToDatabase($borrower);
    }

    /**
     * Notify employee when leave request status changes.
     */
    public static function leaveRequestStatusChanged(
        \Modules\Employee\Models\LeaveRequest $request,
        User $employee,
        string $newStatus,
    ): void {
        $statusLabel = match ($newStatus) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($newStatus),
        };

        $icon = match ($newStatus) {
            'approved' => 'heroicon-o-check-circle',
            'rejected' => 'heroicon-o-x-circle',
            default => 'heroicon-o-information-circle',
        };

        $color = match ($newStatus) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'info',
        };

        Notification::make()
            ->title("Permohonan Cuti {$statusLabel}")
            ->body("Permohonan cuti Anda telah {$statusLabel}.")
            ->icon($icon)
            ->{$color}()
            ->sendToDatabase($employee);
    }

    /**
     * Notify a budget owner when budget status changes.
     */
    public static function budgetApproved(
        \Modules\Finance\Models\Budget $budget,
        User $recipient,
    ): void {
        Notification::make()
            ->title('Anggaran Disetujui')
            ->body("Anggaran \"{$budget->name}\" ({$budget->code}) telah disetujui.")
            ->icon('heroicon-o-check-badge')
            ->success()
            ->sendToDatabase($recipient);
    }

    /**
     * Send a generic notification to a user.
     */
    public static function send(
        User $recipient,
        string $title,
        string $body,
        string $icon = 'heroicon-o-bell',
        string $color = 'info',
    ): void {
        Notification::make()
            ->title($title)
            ->body($body)
            ->icon($icon)
            ->{$color}()
            ->sendToDatabase($recipient);
    }
}
