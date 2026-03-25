<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email'     => $request->email,
            'password'  => $request->password,
            'is_active' => true,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            session(['branch_id' => $user->branch_id]);

            return redirect()->route($this->getDashboardRoute($user->role?->name));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function getDashboardRoute(?string $role): string
    {
        return match($role) {
            'SuperAdmin' => 'superadmin.dashboard',
            'Manager'    => 'manager.dashboard',
            'Clerk'      => 'clerk.dashboard',
            'Cashier'    => 'cashier.dashboard',
            'Accountant' => 'accountant.dashboard',
            default      => 'login',
        };
    }
}
