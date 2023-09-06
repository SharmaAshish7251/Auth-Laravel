<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function index(){
        return view('admin.login');
    }

    public function authenticate(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->passes()){
// Admin Authorization
            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password'=>
             $request->password], $request->get('remember'))){

                    $admin = Auth::guard('admin')->user();
                    if ($admin->role==2){
                        return redirect()->route('admin.dashboard');
                    }
                        else{

                            Auth::guard('admin')->logout();
                            return redirect()->route('admin.login')->with('error', 'You Are not Authorized Admin');
                        }

             }
             else{
                return redirect()->route('admin.login')->with('error', 'Email or Password is Wrong');
             }

        }
        else{

            return redirect()->route('admin.login')
            ->withErros($validator)
            ->withInput($request->only('email'));
        }



    }

}
