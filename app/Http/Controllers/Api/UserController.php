<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\RegistrationMail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\MyTestResource;
use App\Http\Resources\FilialResource;
use App\Http\Resources\TestResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\MyUsersResultsResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Log;
use App\Models\Filial;
use App\Models\Test;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;



class UserController extends Controller
{

    public function store(Request $request)
    {
        $filial = Filial::find($request->input('user.filialId'));
        $users = User::where('filial_id',$request->input('user.filialId'))->get()->count();
        $user = new User();
        $user->lastname = $request->input('user.lastname');
        $user->firstname = $request->input('user.firstname');
        $user->middlename = $request->input('user.middlename');
        $user->name = $request->input('user.lastname')." ".$request->input('user.firstname')." ".$request->input('user.middlename');
        $new_number = $users+1;
        $user->login = $filial->prefix.$new_number;
        $pass = rand(100000,999999);
        $user->password = Hash::make($pass);
        //$user->password_plain = $pass;
        $user->filial_id = $request->input('user.filialId');
        $user->role_id = $request->input('user.roleId');
        $user->division = $request->input('user.division');
        $user->job = $request->input('user.position');
        $user->phone = $request->input('user.phone');
        $user->email = $request->input('user.email');
        $user->code = $this->generateRandomString();

        $user->save();
        if ($user->role_id != 3) {
            $user->password = Hash::make('159753');
            $user->change_password = true;
            $user->valid_till = Carbon::now()->addYears(2);
            $user->save();
            Log::create([
                'level' => 'create',
                'message' => 'Создан пользователь ' . $user->name . ' (id=' . $user->id . ')',
                'context' => 'IP: ' . $request->ip()
            ]);
            Mail::to($user->email)->send(new RegistrationMail($user));
        }else{
            $user->valid_till = Carbon::now()->addMonth();
            $user->save();
            Log::create([
                'level' => 'create',
                'message' => 'Создан тестируемый '.$user->name.' (id=' . $user->id . ')',
                'context' => 'IP: ' . $request->ip()
            ]);
        }
        $test_id = $request->input('user.testId');
        $user->tests()->attach($test_id, ['attempts' => $request->input('user.attempts'),'used_attempts' => 0,'use_photo' => $request->input('user.userPhoto'),'use_train'=> $request->input('user.practiceAttempts')]);


        return new UserResource($user);
    }

    public function update(User $user, Request $request)
    {
        $filial = Filial::find($request->input('user.filialId'));
        $user->lastname = $request->input('user.lastname');
        $user->firstname = $request->input('user.firstname');
        $user->middlename = $request->input('user.middlename');
        $user->name = $request->input('user.lastname')." ".$request->input('user.firstname')." ".$request->input('user.middlename');
        $user->filial_id = $request->input('user.filialId');
        if ($user->id != Auth::user()->id)
        {
            $user->role_id = $request->input('user.roleId');
        }
        $user->division = $request->input('user.division');
        $user->job = $request->input('user.position');
        $user->phone = $request->input('user.phone');
        $user->email = $request->input('user.email');
        $user->valid_till = $request->input('user.valid_till');
        $user->save();
        Log::create([
            'level' => 'update',
            'message' => 'Обновлены данные '.$user->name.' (id=' . $user->id . ')',
            'context' => 'IP: ' .$request->ip()
        ]);
        $test_id = $request->input('user.testId');
        $user->tests()->updateExistingPivot($test_id, ['attempts' => $request->input('user.attempts'),'use_photo' => $request->input('user.userPhoto'),'use_train'=> $request->input('user.practiceAttempts')], false);
        return new UserResource($user);
    }

    public function my_tests()
    {
        return MyTestResource::collection(Auth::user()->tests);
    }


    public function control(Request $request)
    {
        //$res = [];
        //return  UserResource::collection(Auth::user()->people());
        $search = $request->input('search');
        return UserResource::collection(Auth::user()->people($search));

    }

    public function result(Request $request)
    {
        //dd(Auth::user()->people());
        $search = $request->input('search');
        return MyUsersResultsResource::collection(Auth::user()->people($search));
    }

    public function export_data(User $user)
    {

        $test = $user->tests()->first();
        if ($user->role_id == 2)
        {
            $t = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('templates/filial_data.docx'));
            $t->setValue('fio', $user->name);
            $t->setValue('filial', $user->filial->name);
            $t->setValue('login', $user->login);
            $t->setValue('password_plain', 'Базовый пароль: 159753 (нужно сменить при входе)');
            $t->setValue('url', env('APP_URL'));
            $pathToSave = storage_path('Данные пользователя.docx');
            $t->saveAs($pathToSave);
            return response()->download($pathToSave);
        }else{
            $t = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('templates/user_data.docx'));
            $t->setValue('fio', $user->name);
            $t->setValue('division', $user->division);
            $t->setValue('job', $user->job);
            //$t->setValue('login', $user->login);
            $t->setValue('code', $user->code);
            $t->setValue('url', env('APP_URL'));
            $t->setValue('attempts', $test->parameters->attempts);

            $pathToSave = storage_path('Данные для тестирования.docx');
            $t->saveAs($pathToSave);
            return response()->download($pathToSave);
        }

    }

    public function export_test_result(User $user, Test $test)
    {
        $t = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('templates/user_result.docx'));
        $t->setValue('userFio', $user->name);
        $t->setValue('userDivision', $user->division);
        $t->setValue('userjob', $user->job);
        $attempts = $test->user_attempts($user->id);
        $t->cloneRow('attemptNum', $attempts->count());

        $n = 1;
        foreach ($attempts as $attempt)
        {
            $t->setValue('attemptNum#'.$n, $n);
            $t->setValue('attemptDate#'.$n, Carbon::parse($attempt->created_at)->format('d.m.Y'));
            $t->setValue('attemptResult#'.$n, $attempt->percent);
            $t->setValue('attemptStatus#'.$n, $attempt->is_passed ? "Пройден" : "Не пройден");
            $n++;
        }

        $pathToSave = storage_path('protocols/'.$user->name.'.docx');
        $t->saveAs($pathToSave);
        return response()->download(storage_path('protocols/'.$user->name.'.docx'));
    }

    function generateRandomString($length = 6) {
        $characters = '0123456789';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    function reset_password (User $user, Request $request){
        if ($user->role_id != 3) {
            $user->password = Hash::make('159753');
            $user->change_password = 1;
            $user->login_attempts = 0;
            Mail::to($user->email)->send(new PasswordResetMail($user));
            $user->save();
            Log::create([
                'level' => 'password_reset',
                'message' => 'Сброшен пароль у пользователя '.$user->name.' (id=' . $user->id . ')',
                'context' => 'IP: ' .$request->ip()
            ]);
            return response()->json(['message' => 'Пароль изменен на 159753']);
        }else{
            $user->code = '111111';
            $user->change_password = 0;
            $user->save();
            return response()->json(['message' => 'Код изменен на 111111']);
        }

    }

    function update_password(Request $request) {
        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->change_password = 0;
        $user->save();
        return response()->json(['message' => 'Пароль изменен']);
    }
}



