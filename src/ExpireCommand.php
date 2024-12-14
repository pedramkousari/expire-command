<?php

namespace Pedramkousari\ExpireCommand;

use Validator;
use Carbon\Carbon;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ExpireCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'z-abshar:expire {--check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * The file name to store the expire date.
     *
     * @var string
     */
    protected $file = 'expire.txt';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $check = $this->option('check');


        if ($check) {
            $this->checkExpire();
            return;
        }


        $role = select(
            label: 'What do you want to do?',
            options: ['Set Expire', 'Check Expire', 'Up Site']
        );

        if ($role === 'Set Expire') {
            $this->setExpire();
        } elseif ($role === 'Check Expire') {
            $this->checkExpire();
        } elseif ($role === 'Up Site') {
            $this->upSite();
        }
    }

    protected function setExpire()
    {
        $expire = text(
            label: 'Enter the expire date',
            placeholder: now()->addYear()->format('Y-m-d'),
            required: true,
            validate: fn ($value) => match (true) {
                Validator::make(['expire' => $value], ['expire' => 'date|after:now'])->fails() => 'The date is not valid or must be greater than today',
                default => null,
            },
        );

        $expire = Carbon::parse($expire);

        $this->storeExpire($expire);
    }

    protected function storeExpire(Carbon $expire)
    {
        Storage::put($this->file, $expire);
        $this->info('Store Expire for ' . $expire->format('Y-m-d'));
    }

    protected function checkExpire()
    {
        if (!Storage::exists($this->file)) {
            $this->info('Expire date is not set');
            return;
        }

        $expire = Storage::get($this->file);

        if (empty($expire)) {
            $this->info('Expire date is not set');
            return;
        }

        $expire = Carbon::parse($expire);

        if (now()->greaterThan($expire)) {
            Artisan::call('down', [], $this->output);
            $this->error('Expire date is expired');
        } else {
            $this->info('Expire date is valid ' . $expire->diffForHumans(now(), true, true, 2));
        }
    }

    protected function upSite()
    {
        if (Storage::exists($this->file)) {
            Storage::delete($this->file);
        }

        Artisan::call('up', [], $this->output);
        $this->info('Site is up');
    }
}
