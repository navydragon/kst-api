<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\RawResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Log;

class AttemptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $att = new Attempt;
        //dd($request->USER_NAME);
        $user = User::find($request->USER_NAME);
        $test = $user->tests()->first();

        $att->user_id = $request->USER_NAME;
        $att->test_id = 1;
        $att->score = (int)$request->sp;
        $att->percent = round((int)$request->sp * 100 / (int)$request->tp);
        $att->is_passed = (int)$request->sp >= (int)$request->ps ? true : false;
        $att->date = date('Y-m-d H:i:s');
        $att->save();
        $res = new RawResult;
        $res->earned_score = (int)$request->sp;
        $res->passing_score = $request->ps;
        $res->max_score = $request->tp;
        $res->used_time = $request->ut;
        $res->attempt_id = $att->id;
        // $res->full_used_time = $request->fut;
        $res->result = $request->dr;
        $res->save();
        if ((int)$request->sp > $test->parameters->current_score)
        {
            $is_passed = (int) $request->sp >= (int) $request->ps;
            $user->tests()->updateExistingPivot($test->id, ['current_score' => $att->score,'current_percent' => $att->percent,'is_passed'=>$is_passed], false);
        }
       // return "OK";
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function show(Attempt $attempt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attempt $attempt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attempt $attempt)
    {
        //
    }

    public function export_protocol(Attempt $attempt)
    {

        $last = DB::table('attempts')
        ->orderBy('protocol_number', 'desc')
        ->get()->first()->protocol_number;
        $res = $attempt->raw_result;

        $xml = new SimpleXMLElement($res->result);

        if (is_null($attempt->protocol_number))
        {
            $attempt->protocol_number = $last + 1;
            $attempt->save();
        }
        //$xmlObject = simplexml_load_string($xmlDataString);
        //dd($xml->questions[0]->multipleChoiceQuestion->direction->text->__toString());

        $t = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('protocol_template.docx'));
        $t->setValue('fullname', $attempt->user->name);
        $t->setValue('earned', $res->earned_score);
        $t->setValue('total', $res->max_score);
        $earned_percent = round($res->earned_score/$res->max_score*100);
        $t->setValue('earned_percent', $earned_percent );
        $t->setValue('passing', $res->passing_score);
        $test_result = $attempt->is_passed ? "Пройден":"Не пройден";
        $t->setValue('test_result', $test_result);
        $hours = intdiv($res->used_time,3600);
        $minutes = intdiv($res->used_time - $hours * 3600,60);
        $seconds = $res->used_time - $hours * 3600 - $minutes * 60;
        if ($hours != 0) {$ph = $hours." ч ";}else{$ph="";}
        if ($minutes != 0) {$pm = $minutes." мин ";}else{$pm="";}
        if ($seconds != 0) {$ps = $seconds." сек";}else{$ps="";}
        $t->setValue('time', $ph.$pm.$ps );
        $t->setValue('protocolNum', $attempt->protocol_number );

        $t->setValue('date', Carbon::parse($res->created_at)->format('d.m.Y') );

        $pivot = $attempt->user->tests($attempt->test_id)->first()->parameters;

        if (!is_null($pivot->photo_file))
        {
           $img = Storage::disk('local')->path($pivot->photo_file);
           $t->setImageValue('img',array('path' => $img,'width' => 200, 'height' => 200, 'ratio' => true));
        }else{
            $t->setValue('img','');
        }
        if ($attempt->user->filial->id == 2)
        {
            $t->cloneBlock('block_regalia',1,true,true);

            $fio_arr = explode(' ',$attempt->user->name);
            $short_fio = substr($fio_arr[1],0,2).".".substr($fio_arr[2],0,2).". ".$fio_arr[0];

            $t->setValue('short_fio#1',$short_fio);
        }else{
            $t->cloneBlock('block_regalia',0,true,true);
        }
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
            $userAnserIndex = (int) $question->answers["userAnswerIndex"]->__toString();
            $rightAnserIndex = (int) $question->answers["correctAnswerIndex"]->__toString();
            //dd($question->answers->answer[$userAnserIndex]->text->__toString());
            // if ( $question["awardedPoints"] == 0)
            // {
            //     dd($userAnserIndex);
            //     dd($question->answers->answer[$userAnserIndex]->text->__toString());
            // }
            $t->setValue('userAnswer#'.$n, $question->answers->answer[$userAnserIndex]->text->__toString());
            $t->setValue('rightAnswer#'.$n, $question->answers->answer[$rightAnserIndex]->text->__toString());
          //  $t->setValue('rightAnswer#'.$n, $question->direction->text->__toString());
            $n++;
        }

        $pathToSave = storage_path('protocols/protocol_'.$attempt->protocol_number.'.docx');
        File::ensureDirectoryExists(dirname($pathToSave), 0755, true);
        $t->saveAs($pathToSave);

        Log::create([
            'level' => 'export',
            'message' => 'Экспортирован протокол тестирования '.$attempt->user->name,
            'context' => ''
        ]);
        //dd("KEK");
        return response()->download(storage_path('protocols/protocol_'.$attempt->protocol_number.'.docx'));
    }
}
