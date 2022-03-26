<?php

namespace Webkul\Mobikul\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mobikul:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrate and seed command, publish assets and config, link storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Install and configure bagisto.
     */
    public function handle()
    {
        // running `composer dump-autoload`
        $this->warn('Step: Composer autoload...');
        $result = shell_exec('composer dump-autoload');
        $this->info($result);
        
        // running `php artisan migrate`
        $this->warn('Step: Migrating all tables of Mobikul package into database...');
        $migrate = shell_exec('php artisan migrate --path=packages/Webkul/Mobikul/src/Database/Migrations');
        $this->info($migrate);

        // running `php artisan db:seed`
        $this->warn('Step: Seeding data of Mobikul package...');
        $result = shell_exec('php artisan db:seed --class=Webkul\\Mobikul\\Database\\Seeders\\DatabaseSeeder');
        $this->info($result);

        // optimizing stuffs
        $this->warn('Step: Optimizing...');
        $result = $this->call('optimize');
        $this->info($result);

        // running `php artisan jwt:secret`
        $this->warn('Step: Generate JWT secret key ...');
        $result = $this->call('jwt:secret');
        $this->info($result);

        // running `php artisan vendor:publish --all`
        $this->warn('Step: Publishing assets and configurations...');
        $result = $this->call('vendor:publish', ['--force' => true]);
        $this->info($result);

        // check for .env
        $this->checkForEnvFile();

        // final information
        $this->info('-----------------------------');
        $this->info('Congratulations!');
        $this->info('The installation has been finished and you can now use Bagisto Mobikul Extension.');
        $this->info('Cheers!');
    }

    /**
    *  Checking .env file and if not found then create .env file.
    *  Then ask for database name, password & username to set
    *  On .env file so that we can easily migrate to our db.
    */
    protected function checkForEnvFile()
    {
        $envExists = File::exists(base_path() . '/.env');

        if ($envExists) {
            $this->info('Making entry for APP_COUNTRY in .env file.');
            $this->entryCountryEnvFile();
        }
    }

    /**
     * Make entry for country in .env file.
     */
    protected function entryCountryEnvFile()
    {
        try {
            $country = $this->choice('Please enter the default country', ['USA', 'UK', 'IN'], 'IN');
            $this->envEntryAdd('APP_COUNTRY', $country);
            
            $inputTimeToLive = $this->choice('Please Enter the JWT Time To Live : ', ['525600'], '525600');
            $this->envEntryAdd('JWT_TTL', $inputTimeToLive);

        } catch (\Exception $e) {
            $this->error('Error in adding APP_COUNTRY key value in .env file, please try again.');
        }
    }

    /**
     * Update the .env values.
     */
    protected static function envEntryAdd($index, $indexValue)
    {
        $path = base_path() . '/.env';
        $data = file($path);
        $keyValueData = $changedData = [];

        if ($data) {
            foreach ($data as $line) {
                $line = preg_replace('/\s+/', '', $line);
                $rowValues = explode('=', $line);

                if (strlen($line) !== 0) {
                    $keyValueData[$rowValues[0]] = $rowValues[1];

                    if (strpos($index, $rowValues[0]) !== false) {
                        $keyValueData[$rowValues[0]] = $indexValue;
                    } else {
                        $keyValueData[$index] = $indexValue;
                    }
                }
            }
        }

        foreach ($keyValueData as $key => $value) {
            $changedData[] = $key . '=' . $value;
        }

        $changedData = implode(PHP_EOL, $changedData);

        file_put_contents($path, $changedData);
    }
}
