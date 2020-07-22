<?php

namespace App\Http\Controllers;

use App\CaptchaModel;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $captchaData = CaptchaModel::createSum(session()->getId());

        return view('home.index', [
            'captchadata' => $captchaData
        ]);
    }
}
