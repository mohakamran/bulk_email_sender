<?php

namespace App\Jobs;

use App\Mail\BulkEmailMailable;
use App\Models\EmailJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailJob;

    /**
     * Create a new job instance.
     */
    public function __construct(EmailJob $emailJob)
    {
        $this->emailJob = $emailJob;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->emailJob->email)
                ->send(new BulkEmailMailable(
                    $this->emailJob->name,
                    $this->emailJob->university,
                    $this->emailJob->research,
                    $this->emailJob->cv_path
                ));

            $this->emailJob->update(['status' => 'sent']);
        } catch (\Exception $e) {
            $this->emailJob->update(['status' => 'failed']);
            throw $e;
        }
    }
}
