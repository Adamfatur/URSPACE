<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ses:test {to : Recipient email} {--subject=SES test} {--body=This is a test email sent via AWS SES.}', function () {
    $to = (string) $this->argument('to');
    $subject = (string) $this->option('subject');
    $body = (string) $this->option('body');

    $fromAddress = (string) config('mail.from.address');
    $fromName = (string) config('mail.from.name');
    $region = (string) config('filesystems.disks.s3.region') ?: (string) env('AWS_DEFAULT_REGION');

    $this->info('Sending SES test email...');
    $this->line('From: ' . ($fromName ? "$fromName <$fromAddress>" : $fromAddress));
    $this->line('To: ' . $to);
    $this->line('Region: ' . ($region ?: '[unknown]'));

    try {
        Mail::mailer('ses')->raw($body, function ($message) use ($to, $subject, $fromAddress, $fromName) {
            $message->to($to)->subject($subject);

            if ($fromAddress !== '') {
                $message->from($fromAddress, $fromName ?: null);
            }
        });

        $this->info('OK: Email queued/sent via SES (check inbox/spam).');
    } catch (Throwable $e) {
        Log::error('SES test email failed', [
            'to' => $to,
            'subject' => $subject,
            'exception' => $e,
        ]);

        $this->error('FAILED: ' . get_class($e));
        $this->error($e->getMessage());
        $this->line('Tips: pastikan MAIL_FROM_ADDRESS terverifikasi di SES, dan jika SES Sandbox, recipient juga harus terverifikasi.');
        $this->line('Lihat detail: storage/logs/laravel.log');
        return 1;
    }

    return 0;
})->purpose('Send a test email using AWS SES mailer');
