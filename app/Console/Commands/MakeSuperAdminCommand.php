<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Core\Models\User;

class MakeSuperAdminCommand extends Command
{
    protected $signature = 'foundation:make-super-admin
        {email : Email user super-admin}
        {--name= : Nama user}
        {--username= : Username user}
        {--password= : Password user (opsional, akan digenerate jika user baru)}';

    protected $description = 'Create or promote a global super-admin user';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) $this->argument('email')));
        $nameOption = trim((string) $this->option('name'));
        $usernameOption = trim((string) $this->option('username'));
        $passwordOption = (string) ($this->option('password') ?? '');

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();
        $isNewUser = false;
        $generatedPassword = null;

        if (! $user) {
            $isNewUser = true;
            $user = new User;
            $user->email = $email;
            $user->name = $nameOption !== '' ? $nameOption : str($email)->before('@')->replace('.', ' ')->title()->toString();
            $user->username = $usernameOption !== '' ? $usernameOption : str($email)->before('@')->replace('.', '_')->lower()->toString();
            $user->status = 'active';
            $user->email_verified_at = now();
        } else {
            if ($nameOption !== '') {
                $user->name = $nameOption;
            }

            if ($usernameOption !== '') {
                $user->username = $usernameOption;
            }
        }

        if ($passwordOption !== '') {
            $user->password = Hash::make($passwordOption);
        } elseif ($isNewUser) {
            $generatedPassword = Str::password(16);
            $user->password = Hash::make($generatedPassword);
        }

        $user->save();
        $user->promoteToGlobalSuperAdmin();

        $this->info($isNewUser
            ? "Global super-admin berhasil dibuat untuk {$email}."
            : "User {$email} berhasil dipromosikan menjadi global super-admin.");

        if ($generatedPassword !== null) {
            $this->warn("Password sementara: {$generatedPassword}");
            $this->line('Segera ganti password setelah login pertama.');
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Username', 'Is Super Admin'],
            [[
                $user->id,
                $user->name,
                $user->email,
                $user->username ?? '-',
                $user->is_super_admin ? 'yes' : 'no',
            ]]
        );

        return self::SUCCESS;
    }
}
