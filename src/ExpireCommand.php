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

    public function __construct()
    {
        $this->signature = config('expire.signature'). ' {--check}';
        parent::__construct();
    }

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
            label: __('expire::expire.what_do_you_want_to_do'),
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
            label: __('expire::expire.enter_the_expire_date'),
            placeholder: now()->addYear()->format('Y-m-d'),
            required: true,
            validate: fn ($value) => match (true) {
                Validator::make(['expire' => $value], ['expire' => 'date|after:now'])->fails() => __('expire::expire.the_date_is_not_valid_or_must_be_greater_than_today'),
                default => null,
            },
        );

        $expire = Carbon::parse($expire);

        $this->storeExpire($expire);
    }

    protected function storeExpire(Carbon $expire)
    {
        Storage::put($this->file, $expire);
        $this->info(__('expire::expire.stored_expire_date', ['date' => $expire->format('Y-m-d')]));
    }

    protected function checkExpire()
    {
        if (!Storage::exists($this->file)) {
            $this->info(__('expire::expire.expire_date_is_not_set'));
            return;
        }

        $expire = Storage::get($this->file);

        if (empty($expire)) {
            $this->info(__('expire::expire.expire_date_is_not_set'));
            return;
        }

        $expire = Carbon::parse($expire);

        if (now()->greaterThan($expire)) {
            Artisan::call('down', [], $this->output);
            $this->error(__('expire::expire.expire_date_is_expired'));
        } else {
            $this->info(__('expire::expire.expire_date_is_valid', ['date' => $expire->diffForHumans(now(), true, true, 2)]));
        }
    }

    protected function upSite()
    {
        if (Storage::exists($this->file)) {
            Storage::delete($this->file);
        }

        Artisan::call('up', [], $this->output);
        $this->info(__('expire::expire.site_is_up'));
    }
}
