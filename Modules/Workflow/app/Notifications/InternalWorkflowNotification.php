<?php

namespace Modules\Workflow\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Workflow\Models\WorkflowInstance;

class InternalWorkflowNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected WorkflowInstance $instance,
        protected string $title,
        protected ?string $body = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'workflow_instance_id' => $this->instance->getKey(),
            'workflow_id' => $this->instance->workflow_id,
            'subject_label' => $this->instance->subject_label,
            'status' => $this->instance->status?->value ?? $this->instance->status,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->line($this->body ?? $this->title);
    }
}
