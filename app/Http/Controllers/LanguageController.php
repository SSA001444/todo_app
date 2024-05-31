<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLocale(Request $request)
    {
        $locale = $request->input('locale');
        if (in_array($locale, ['en', 'lv', 'ru'])) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        }
        return redirect()->back();
    }
}
