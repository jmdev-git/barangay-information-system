<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountStatusController extends Controller
{
    /**
     * Show "account pending verification" informational page (Req 3 AC5).
     */
    public function pending()
    {
        return view('account.pending');
    }

    /**
     * Show "account rejected" informational page with reason (Req 3 AC6).
     */
    public function rejected(Request $request)
    {
        return view('account.rejected', [
            'reason' => $request->user()->rejection_reason,
        ]);
    }
}
