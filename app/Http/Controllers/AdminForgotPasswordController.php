<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use  Illuminate\Foundation\Auth\SendsPasswordResetEmails;
class AdminForgotPasswordController extends Controller
{
  use SendsPasswordResetEmails;

  protected function guard()
  {
    return Auth::guard('admin');
  }

  protected function broker()
  {
    return Password::broker('admins');
  }
}
