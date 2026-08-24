<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\Customer;
use App\Services\CartService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('storefront.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $this->ensureNotRateLimited($request);

        // Find customer by email to check lockout status
        $customer = Customer::where('email', $credentials['email'])->first();

        if ($customer && $customer->isLocked()) {
            $minutes = $customer->getLockoutRemainingMinutes();
            throw ValidationException::withMessages([
                'email' => "Tài khoản đã bị khóa do đăng nhập sai quá nhiều lần. Vui lòng thử lại sau {$minutes} phút.",
            ]);
        }

        if (Auth::guard('customer')->attempt($credentials, $remember)) {
            $this->clearLoginAttempts($request);

            // Migrate session — transfers guest cart (session-keyed) to the authenticated session.
            // Session::migrate(true) regenerates session ID while preserving all session data,
            // including the cart. This is the cart handoff on login.
            Session::migrate(true);

            // Reset failed login attempts on successful login
            $customer = Auth::guard('customer')->user();
            $customer->resetFailedLoginAttempts();

            // Regenerate remember token on successful login for security
            if ($remember) {
                $customer->regenerateRememberToken();
            }

            return redirect()->intended(route('account.orders'))
                ->with('success', 'Đăng nhập thành công. Chào mừng quay lại!');
        }

        // Increment failed login attempts on the customer record
        if ($customer) {
            $customer->incrementFailedLoginAttempts();
        }

        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('storefront.auth.register');
    }

    public function register(RegisterRequest $request, CartService $cartService)
    {
        $validated = $request->validated();

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        Auth::guard('customer')->login($customer);
        Session::migrate(true);

        return redirect()->route('account.orders')
            ->with('success', 'Đăng ký tài khoản thành công. Chào mừng bạn đến với cửa hàng!');
    }

    public function showForgotPasswordForm()
    {
        return view('storefront.auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $this->ensureNotRateLimited($request, 'password_reset');

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Chúng tôi đã gửi liên kết đặt lại mật khẩu vào email của bạn.');
        }

        return back()->withErrors(['email' => 'Không tìm thấy tài khoản với email này.']);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('storefront.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Customer $customer, string $password) {
                $customer->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Reset failed login attempts on password reset
                $customer->resetFailedLoginAttempts();

                event(new PasswordReset($customer));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('account.login')
                ->with('status', 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập lại.');
        }

        return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
    }

    public function logout(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if ($customer) {
            // Clear active sessions tracking for this customer
            $sessionId = $request->session()->getId();
            $sessions = $customer->active_sessions ?? [];
            $sessions = array_values(array_filter($sessions, fn ($id) => $id !== $sessionId));
            $customer->active_sessions = $sessions;
            $customer->saveQuietly();
        }

        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('products.index')
            ->with('success', 'Bạn đã đăng xuất thành công.');
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureNotRateLimited(Request $request, string $key = 'login'): void
    {
        $key = $key . ':' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Quá nhiều lần thử đăng nhập. Vui lòng thử lại sau {$seconds} giây.",
            ]);
        }
    }

    /**
     * Increment the login attempts for the request.
     */
    protected function incrementLoginAttempts(Request $request, string $key = 'login'): void
    {
        RateLimiter::hit($key . ':' . $request->ip(), 60);
    }

    /**
     * Clear the login attempts for the request.
     */
    protected function clearLoginAttempts(Request $request, string $key = 'login'): void
    {
        RateLimiter::clear($key . ':' . $request->ip());
    }
}