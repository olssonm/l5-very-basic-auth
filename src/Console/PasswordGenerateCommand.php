<?php

namespace Olssonm\VeryBasicAuth\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('very-basic-auth:password-generate')]
class PasswordGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'very-basic-auth:password-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows to securely set the `BASIC_AUTH_PASSWORD` in your Laravel `.env` file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $approved = false;

        do {
            $password = $this->secret('Please enter a password for the very basic auth');

            if (empty($password) || strlen($password) < 8) {
                $this->error('The password must be at least 8 characters.');
                continue;
            }

            $confirmation = $this->secret('Please confirm your password');

            if ($password !== $confirmation) {
                $this->error('The passwords do not match. Please try again.');
                $approved = false;
            } else {
                $approved = true;
            }
        } while (!$approved);

        if ($this->writeNewEnvironmentFileWith(app()->make('hash')->make($password))) {
            $this->info('The password has been set successfully.');
        }

        return static::SUCCESS;
    }

    /**
     * Write a new environment file with the given password.
     *
     * @param string $password
     * @return bool
     */
    protected function writeNewEnvironmentFileWith(string $password): bool
    {
        $envPath = $this->laravel->environmentFilePath();
        $input = file_get_contents($envPath);

        $replaced = preg_replace_callback(
            $this->passwordReplacementPattern(),
            fn($matches) => 'BASIC_AUTH_PASSWORD="' . $password . '"',
            $input
        );

        if ($replaced === $input || $replaced === null) {
            $this->error('Unable to set password. No BASIC_AUTH_PASSWORD variable was found in the .env file. Please add it first');
            return false;
        }

        file_put_contents($envPath, $replaced);

        return true;
    }

    /**
     * Get a regex pattern that will match env BASIC_AUTH_PASSWORD with any password.
     *
     * @return string
     */
    protected function passwordReplacementPattern(): string
    {
        return '/^BASIC_AUTH_PASSWORD=.*$/m';
    }
}
