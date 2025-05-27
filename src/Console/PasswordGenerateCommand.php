<?php

namespace Olssonm\VeryBasicAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Console\Attribute\AsCommand;

use function file_get_contents;
use function file_put_contents;
use function Laravel\Prompts\password;
use function preg_quote;
use function preg_replace;

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
        $password = password(
            label: 'Please enter a password for the very basic auth',
            required: true,
            validate: ['required', 'confirmed', Password::min(8)],
            hint: 'The password must be at least 8 characters long.'
        );

        if ($this->writeNewEnvironmentFileWith($this->laravel->make('hash')->make($password))) {
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
        $replaced = preg_replace(
            $this->passwordReplacementPattern(),
            'BASIC_AUTH_PASSWORD=' . $password,
            $input = file_get_contents($this->laravel->environmentFilePath())
        );

        if ($replaced === $input || $replaced === null) {
            $this->error('Unable to set password. No BASIC_AUTH_PASSWORD variable was found in the .env file.');

            return false;
        }

        file_put_contents($this->laravel->environmentFilePath(), $replaced);

        return true;
    }

    /**
     * Get a regex pattern that will match env BASIC_AUTH_PASSWORD with any password.
     *
     * @return string
     */
    protected function passwordReplacementPattern(): string
    {
        $escaped = preg_quote('=' . $this->laravel['config']['very_basic_auth.password'], '/');

        return "/^BASIC_AUTH_PASSWORD{$escaped}/m";
    }
}
