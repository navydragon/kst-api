<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\LogResource;
use App\Models\Log;
use App\Models\Filial;
use App\Http\Resources\FilialReportResource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Exports\LogExport;
use App\Exports\FilialReportExport;
use Maatwebsite\Excel\Facades\Excel;


class AdminController extends Controller
{
    public function logs()
    {
        return LogResource::collection(Log::orderBy('created_at', 'desc')->paginate(12));
    }


    public function delete_old_logs(Request $request)
    {
        $old_days = $request->days;
        $thresholdDate = Carbon::now()->subDays($old_days);

        Log::where('created_at', '<', $thresholdDate)->delete();

        return response()->json(['message' => 'Удалены все записи, старше '.$old_days.' дней.'], 200);
    }

    public function export_logs ()
    {
        //return Excel::download(new LogExport, 'logs.xlsx');
        $table = Log::all();
        $filename = "logs.csv";
        $handle = fopen($filename, 'w+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, array('ID','type','Message','Context','Extra','created_at','updated_at'),';');

        foreach($table as $row) {
            fputcsv($handle, array($row['id'], $row['level'], $row['message'], $row['context'], $row['extra'],$row['created_at'],$row['updated_at']),';');
        }

        fclose($handle);

        return response()->download($filename);
        //return Response::download($filename, 'logs.csv', $headers);
    }

    function report(Request $request) {
        $from = $request->input('startDate');
        $to = $request->input('endDate');

        // Преобразование строк в объекты Carbon
        $fromDate = $from ? Carbon::createFromFormat('Y-m-d', $from)->startOfDay() : null;
        $toDate = $to ? Carbon::createFromFormat('Y-m-d', $to)->endOfDay() : null;

        $filials = Filial::withCount(['users' => function ($query) use ($fromDate, $toDate) {
            if ($fromDate) {
                $query->where('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $query->where('created_at', '<=', $toDate);
            }
        }])->get();

        return FilialReportResource::collection($filials);
    }

    function export_report(Request $request) {
        $from = $request->input('startDate');
        $to = $request->input('endDate');
        $fromDate = $from ? Carbon::createFromFormat('Y-m-d', $from)->startOfDay() : null;
        $toDate = $to ? Carbon::createFromFormat('Y-m-d', $to)->endOfDay() : null;
        $filename = "filial_report.csv";
        $handle = fopen($filename, 'w+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, array('Филиал','Кол-во тестирований'),';');
        $filials = Filial::withCount(['users' => function ($query) use ($fromDate, $toDate) {
            if ($fromDate) {
                $query->where('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $query->where('created_at', '<=', $toDate);
            }
        }])->get();
        $totalTests = 0;
        foreach($filials as $row) {
            fputcsv($handle, array($row['name'], $row['users_count']),';');
            $totalTests += $row['users_count'];
        }
        fputcsv($handle, array('Итого', $totalTests), ';');

        fclose($handle);
        Log::create([
            'level' => 'report',
            'message' => 'Экспортирован сводный отчет по участника тестирования',
            'context' => 'IP: ' .$request->ip()
        ]);
        return response()->download($filename);

    }

    function sql(Request $request) {
        $sql = $request->input("query");
        $result = DB::select($sql);

        $isSelectQuery = stripos($sql, 'SELECT') === 0;

        // Возвращение результата в виде JSON, если это был запрос SELECT
        if ($isSelectQuery) {
            $jsonString = json_encode($result);
            return $jsonString;
        } else {
            // Ваш код обработки других типов запросов (INSERT, UPDATE, DELETE и т.д.)
            return response()->json(['message' => 'Query executed successfully']);
        }
    }

}
