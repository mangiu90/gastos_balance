<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test_mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            Mail::to("giudimanuel@gmail.com")->send(new \App\Mail\TestMail());
            $this->info('mail enviado');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }


        return 0;
    }
}
