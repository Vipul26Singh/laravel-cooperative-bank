<?php
/*
 * CoopBank ERP - Cooperative Bank Management System
 * Copyright (c) 2026. All rights reserved.
 * Licensed under the Envato Regular/Extended License.
 * https://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Artisan, DB, Hash, File};
use App\Models\{User, Role};

class InstallController extends Controller
{
    public function welcome()
    {
        if ($this->isInstalled()) return redirect('/');
        return view('install.welcome');
    }

    public function requirements()
    {
        if ($this->isInstalled()) return redirect('/');

        $checks = [
            'PHP >= 8.4'           => version_compare(PHP_VERSION, '8.4.0', '>='),
            'PDO Extension'        => extension_loaded('pdo'),
            'SQLite Extension'     => extension_loaded('pdo_sqlite'),
            'MySQL Extension'      => extension_loaded('pdo_mysql'),
            'Mbstring Extension'   => extension_loaded('mbstring'),
            'OpenSSL Extension'    => extension_loaded('openssl'),
            'Tokenizer Extension'  => extension_loaded('tokenizer'),
            'JSON Extension'       => extension_loaded('json'),
            'GD Extension'         => extension_loaded('gd'),
            'BCMath Extension'     => extension_loaded('bcmath'),
            'storage/ writable'    => is_writable(storage_path()),
            'bootstrap/cache/ writable' => is_writable(base_path('bootstrap/cache')),
            '.env writable'        => is_writable(base_path()) || is_writable(base_path('.env')),
        ];

        $allPassed = !in_array(false, $checks);
        return view('install.requirements', compact('checks', 'allPassed'));
    }

    public function database()
    {
        if ($this->isInstalled()) return redirect('/');
        return view('install.database');
    }

    public function saveDatabase(Request $request)
    {
        $request->validate([
            'db_connection' => 'required|in:sqlite,mysql,pgsql',
            'db_host'       => 'required_unless:db_connection,sqlite',
            'db_port'       => 'required_unless:db_connection,sqlite',
            'db_database'   => 'required',
            'db_username'   => 'required_unless:db_connection,sqlite',
            'db_password'   => 'nullable',
        ]);

        // Test connection
        if ($request->db_connection !== 'sqlite') {
            try {
                $pdo = new \PDO(
                    "{$request->db_connection}:host={$request->db_host};port={$request->db_port};dbname={$request->db_database}",
                    $request->db_username,
                    $request->db_password
                );
                $pdo = null;
            } catch (\Exception $e) {
                return back()->withErrors(['db_host' => 'Could not connect: ' . $e->getMessage()])->withInput();
            }
        }

        session([
            'install_db' => $request->only('db_connection', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password'),
        ]);

        return redirect()->route('install.admin');
    }

    public function admin()
    {
        if ($this->isInstalled()) return redirect('/');
        return view('install.admin');
    }

    public function saveAdmin(Request $request)
    {
        $request->validate([
            'app_name'       => 'required|string|max:255',
            'app_url'        => 'required|url',
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        session([
            'install_admin' => $request->only('app_name', 'app_url', 'admin_name', 'admin_email', 'admin_password'),
        ]);

        return redirect()->route('install.finish');
    }

    public function finish()
    {
        if ($this->isInstalled()) return redirect('/');

        $db    = session('install_db', []);
        $admin = session('install_admin', []);

        if (empty($db) || empty($admin)) {
            return redirect()->route('install.database');
        }

        return view('install.finish', compact('db', 'admin'));
    }

    public function run()
    {
        if ($this->isInstalled()) return redirect('/');

        $db    = session('install_db', []);
        $admin = session('install_admin', []);

        // 1. Create .env
        $env = File::get(base_path('.env.example'));
        $env = $this->setEnv($env, 'APP_NAME', $admin['app_name'] ?? 'CoopBank');
        $env = $this->setEnv($env, 'APP_URL', $admin['app_url'] ?? 'http://localhost');
        $env = $this->setEnv($env, 'DB_CONNECTION', $db['db_connection'] ?? 'sqlite');

        if (($db['db_connection'] ?? 'sqlite') !== 'sqlite') {
            $env = $this->setEnv($env, 'DB_HOST', $db['db_host'] ?? '127.0.0.1');
            $env = $this->setEnv($env, 'DB_PORT', $db['db_port'] ?? '3306');
            $env = $this->setEnv($env, 'DB_DATABASE', $db['db_database'] ?? 'coopbank');
            $env = $this->setEnv($env, 'DB_USERNAME', $db['db_username'] ?? 'root');
            $env = $this->setEnv($env, 'DB_PASSWORD', $db['db_password'] ?? '');
        } else {
            $dbPath = database_path('database.sqlite');
            if (!file_exists($dbPath)) touch($dbPath);
        }

        File::put(base_path('.env'), $env);

        // 2. Generate key
        Artisan::call('key:generate', ['--force' => true]);

        // 3. Clear config cache so new .env is read
        Artisan::call('config:clear');

        // 4. Run migrations
        Artisan::call('migrate', ['--force' => true]);

        // 5. Seed roles
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'CountrySeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'CompanySetupSeeder', '--force' => true]);

        // 6. Create admin user
        $role = Role::where('name', 'SuperAdmin')->first();
        User::create([
            'name'      => $admin['admin_name'],
            'email'     => $admin['admin_email'],
            'password'  => Hash::make($admin['admin_password']),
            'role_id'   => $role?->id,
            'is_active' => true,
        ]);

        // 7. Mark as installed
        File::put(storage_path('installed'), now()->toDateTimeString());

        // 8. Clear session
        session()->forget(['install_db', 'install_admin']);

        return redirect()->route('install.complete');
    }

    public function complete()
    {
        return view('install.complete');
    }

    private function isInstalled(): bool
    {
        return File::exists(storage_path('installed'));
    }

    private function setEnv(string $env, string $key, string $value): string
    {
        $value = str_contains($value, ' ') ? '"' . $value . '"' : $value;
        if (preg_match("/^{$key}=.*/m", $env)) {
            return preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
        }
        return $env . "\n{$key}={$value}";
    }
}
