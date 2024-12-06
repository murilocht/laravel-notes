<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function loginSubmit(Request $request)
    {
        $request->validate(
            // rules
            [
                'text_email' => 'required|email',
                'text_password' => 'required|min:6|max:16'
            ],
            // error messages
            [
                'text_email.required' => 'Email obrigatório',
                'text_email.email' => 'Email inválido',
                'text_password.required' => 'Senha obrigatória',
                'text_password.min' => 'Senha deve ter pelo menos :min caracteres',
                'text_password.max' => 'Senha deve ter no máximo :max caracteres',
            ]
        );

        // get user input
        $text_email = $request->input('text_email');
        $text_password = $request->input('text_password');

        // try {
        //     DB::connection()->getPdo();
        //     echo 'database ok';
        // } catch (\PDOException $e) {
        //     echo $e->getMessage();
        // }

        // $users = User::all()->toArray();
        // print_r($users);

        // check userif exists
        $user = User::where('email', $text_email)
                    ->where('deleted_at', null)
                    ->first();

        // print_r($user);

        if (!$user) {
            return redirect()
                    ->back()
                    ->withInput()
                    ->with('loginError', 'O Email não existe!');
        }

        // check password is correct
        if (!password_verify($text_password, $user->password)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('loginError', 'A senha do email está errada!');
        }

        // update last login
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        // add user on session
        session([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ]);

        echo 'ok';
    }

    public function logout()
    {

    }
}
