<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\RawResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use SimpleXMLElement;
use DateTime;
use Illuminate\Support\Facades\DB;




class AuthController extends Controller
{
    public function login(Request $request)
    {
      try {
          $request->validate([
            'login' => 'required',
            'password' => 'required'
          ]);

          $identifier = $request->login;
          $user = User::where(function ($query) use ($identifier) {
            $query->where('login', $identifier)
              ->orWhere('email', $identifier);
          })->first();

          if (!$user || !Hash::check($request->password, $user->password)) {
            Log::create([
              'level' => 'login',
              'message' => 'Неудачная попытка входа пользователя',
              'context' => 'IP: ' . $request->ip(),
              'extra' => 'login: '. $request->login
            ]);
            if ($user) {
              $user->login_attempts +=1;
              $user->save();
            }
            return response('Login invalid', 503);
          }

          if ($user->login_attempts >= 5) {
            return response()->json([
                'message' => 'Учетная запись заблокирована из-за множества неудачных попыток входа'
            ], 403);
          }

          $now = new DateTime("now");
          $valid = new DateTime($user->valid_till);

            if ($now > $valid && !is_null($user->valid_till)) {
                return response()->json([
                    'message' => 'Срок действия учетной записи истек ' . $valid->format('d.m.Y')
                ], 403);
            }

          $user->login_attempts = 0;
          $user->save();
          Auth::login($user);
          Log::create([
            'level' => 'login',
            'message' => 'Успешная попытка входа пользователя ' . $user->name . ' (id=' . $user->id . ')',
            'context' => 'IP: ' . $request->ip()
        ]);

          $tokenResult = $user->createToken('authToken')->plainTextToken;
          //$tokenResult = $user->createToken('bearerToken')->accessToken;

          return response()->json([
            'status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
          ]);
      } catch (Exception $error) {
          return response()->json([
            'status_code' => 500,
            'message' => 'Error in Login',
            'error' => $error,
          ]);
      }
    }

    function test_login(Request $request) {
      try {
        $request->validate([
          'name' => 'required',
          'code' => 'required'
        ]);

        $user = User::where('lastname', $request->name)->where('code',$request->code)->first();
        if (!$user) {
          Log::create([
            'level' => 'login',
            'message' => 'Неудачная попытка входа для тестирования',
            'context' => 'IP: ' . $request->ip(),
            'extra' => 'Фамилия: '. $request->name.', код: '.$request->code
          ]);
          return response('Lastname or code invalid', 503);
        }

        $now = new DateTime("now");
          $valid = new DateTime($user->valid_till);

            if ($now > $valid && !is_null($user->valid_till)) {
                return response()->json([
                    'message' => 'Срок действия учетной записи истек ' . $valid->format('d.m.Y')
                ], 403);
            }
        Auth::login($user);
        Log::create([
          'level' => 'login',
          'message' => 'Успешная попытка входа '.$user->name.' для тестирования',
          'context' => 'IP: ' . $request->ip()
        ]);

        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json([
          'status_code' => 200,
          'access_token' => $tokenResult,
          'token_type' => 'Bearer',
        ]);
      } catch (Exception $error) {
          return response()->json([
            'status_code' => 500,
            'message' => 'Error in Login',
            'error' => $error,
          ]);
      }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $user = $request->user();
        Log::create([
            'level' => 'logout',
            'message' => 'Выход из системы '.$user->name.' (id=' . $user->id . ')',
            'context' => 'IP: ' .$request->ip()
        ]);
        return response()->json([
          'status_code' => 200,
          'message' => 'Logged out',
        ]);
    }

    public function store_raw(Request $request)
    {
        $res = new RawResult;
        $res->user_name = $request->USER_NAME;
        $res->earned_score = $request->sp;
        $res->passing_score = $request->ps;
        $res->max_score = $request->tp;
        $res->used_time = $request->ut;
        // $res->full_used_time = $request->fut;
        $res->result = $request->dr;
        $res->save();
       // return "OK";
    }

    public function export_protocol($id)
    {

        $res = RawResult::find($id);

        $xml = new SimpleXMLElement($res->result);

        //$xmlObject = simplexml_load_string($xmlDataString);
        //dd($xml->questions[0]->multipleChoiceQuestion->direction->text->__toString());
        $t = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('protocol_template.docx'));
        $t->setValue('fullname', $res->user_name);
        $t->setValue('earned', $res->earned_score);
        $t->setValue('total', $res->max_score);
        $earned_percent = round($res->earned_score/$res->max_score*100);
        $t->setValue('earned_percent', $earned_percent );
        $t->setValue('passing', $res->passing_score);
        $hours = intdiv($res->used_time,3600);
        $minutes = intdiv($res->used_time - $hours * 3600,60);
        $seconds = $res->used_time - $hours * 3600 - $minutes * 60;
        if ($hours != 0) {$ph = $hours." ч ";}else{$ph="";}
        if ($minutes != 0) {$pm = $minutes." мин ";}else{$pm="";}
        if ($seconds != 0) {$ps = $seconds." сек";}else{$ps="";}
        $t->setValue('time', $ph.$pm.$ps );

        $t->setValue('date', Carbon::parse($res->created_at)->format('d.m.Y') );

       // $t->setValue('year', date("Y"));
        //dd(count($xml->questions->multipleChoiceQuestion));
        //$t->cloneRow('questionNum', count($xml->questions->multipleChoiceQuestion));
        $t->cloneBlock('block_question', count($xml->questions->multipleChoiceQuestion), true, true);
        $n = 1;

        foreach ($xml->questions->multipleChoiceQuestion as $question)
        {
            $t->setValue('questionNum#'.$n, $n);
            $t->setValue('questionStatus#'.$n, $question["status"] == "correct" ? "Верно" : "Неверно");
            $t->setValue('questionPoints#'.$n, $question["awardedPoints"]."/".$question["maxPoints"]);
            $t->setValue('questionText#'.$n, $question->direction->text->__toString());
            $userAnserIndex = (int) $question->answers["correctAnswerIndex"]->__toString();
            $rightAnserIndex = (int) $question->answers["userAnswerIndex"]->__toString();
            //dd($question->answers->answer[$userAnserIndex]->text->__toString());
            $t->setValue('userAnswer#'.$n, $question->answers->answer[$userAnserIndex]->text->__toString());
            $t->setValue('rightAnswer#'.$n, $question->answers->answer[$rightAnserIndex]->text->__toString());
          //  $t->setValue('rightAnswer#'.$n, $question->direction->text->__toString());
            $n++;
        }
        $pathToSave = storage_path('test.docx');
        $t->saveAs($pathToSave);
        return response()->download(storage_path('test.docx'));
    }
}
