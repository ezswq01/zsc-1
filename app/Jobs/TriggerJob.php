<?php

namespace App\Jobs;

use App\Mail\TriggerMail;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class TriggerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $val, $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($val, $type)
    {
        $this->val = $val;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach(Setting::first()->load('users')->users as $user){
            Mail::to($user)->send(new TriggerMail($this->val, $this->type));
        }
    }
}
