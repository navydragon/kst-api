<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationMail;
use App\Models\Filial;
use App\Models\Log;
use App\Models\Test;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'filial_id' => 'required|integer|exists:filials,id',
        ], [
            'lastname.required' => 'Укажите фамилию',
            'firstname.required' => 'Укажите имя',
            'email.required' => 'Укажите email',
            'email.email' => 'Укажите корректный email',
            'email.unique' => 'Этот email уже зарегистрирован',
            'filial_id.required' => 'Выберите филиал',
            'filial_id.exists' => 'Выбранный филиал не найден',
            'filial_id.integer' => 'Выберите филиал',
        ]);

        $filial = Filial::find($request->input('filial_id'));
        $test = Test::find(1);
        if (!$test) {
            return response()->json([
                'status' => 'error',
                'message' => 'Тест для регистрации не настроен. Обратитесь к администратору.',
            ], 422);
        }

        $middlename = trim((string) $request->input('middlename', ''));
        $number = User::where('filial_id', $filial->id)->count() + 1;
        $login = $filial->prefix.$number;
        while (User::where('login', $login)->exists()) {
            $number++;
            $login = $filial->prefix.$number;
        }

        $user = new User();
        $user->lastname = $request->input('lastname');
        $user->firstname = $request->input('firstname');
        $user->middlename = $middlename;
        $user->name = trim($user->lastname.' '.$user->firstname.' '.$middlename);
        $user->login = $login;
        $user->password = Hash::make('159753');
        $user->filial_id = $filial->id;
        $user->role_id = 2;
        $user->email = $request->input('email');
        $user->code = null;
        $user->change_password = 0;
        $user->valid_till = Carbon::now()->addYears(2);
        $user->save();

        $user->tests()->attach($test->id, [
            'attempts' => 99,
            'used_attempts' => 0,
            'use_photo' => 0,
            'use_train' => 1,
            'current_score' => 0,
            'current_percent' => 0,
            'is_passed' => 0,
        ]);

        Log::create([
            'level' => 'create',
            'message' => 'Самостоятельная регистрация '.$user->name.' (id='.$user->id.')',
            'context' => 'IP: '.$request->ip(),
        ]);

        try {
            Mail::to($user->email)->send(new RegistrationMail($user));
        } catch (\Throwable $e) {
            Log::create([
                'level' => 'error',
                'message' => 'Письмо регистрации не отправлено, user id='.$user->id,
                'context' => $e->getMessage(),
            ]);
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
        ]);
    }
}
