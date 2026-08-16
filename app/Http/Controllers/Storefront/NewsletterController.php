<?php

namespace App\Http\Controllers\Storefront;

use App\Actions\SubscribeToNewsletterAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request, SubscribeToNewsletterAction $action): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // Action handles duplicate silently — safe for email enumeration
        $action->handle($request->email, $request->ip());

        return redirect()->back()->with('newsletter_success', 'Cảm ơn! Bạn đã đăng ký nhận bản tin thành công.');
    }
}
