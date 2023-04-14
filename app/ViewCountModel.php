<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class ViewCountModel
 *
 * Interface to user view counting
 */
class ViewCountModel extends Model
{
    use HasFactory;

    /**
     * Add view entry to table
     * 
     * @return void
     * @throws \Exception
     */
    public static function addView()
    {
        try {
            $token = md5(request()->ip());
            $curdate = date('Y-m-d');

            $exists = static::where('token', '=', $token)->whereRaw('DATE(created_at) = ?', [$curdate])->first();
            if ($exists) {
                $exists->touch();
            } else {
                $entry = new self();
                $entry->token = $token;
                $entry->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get current online count
     * 
     * @return int
     * @throws \Exception
     */
    public static function getOnlineCount()
    {
        try {
            $limit = env('APP_ONLINECOUNTLIMIT', 30);
            $checkdate = date('Y-m-d H:i:s', strtotime('-' . strval($limit) . ' minutes'));

            $count = static::where('updated_at', '>=', $checkdate)->count();

            return $count;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get visits per day
     * 
     * @param $start
     * @param $end
     * @return mixed
     * @throws \Exception
     */
    public static function getVisitsPerDay($start, $end)
    {
        try {
            $data = DB::table(with(new static)->getTable())
                ->select(DB::raw('DATE(created_at) AS created_at, COUNT(token) AS count'))
                ->whereRaw('DATE(created_at) >= ?', [$start])
                ->whereRaw('DATE(created_at) <= ?', [$end])
                ->groupByRaw('DATE(created_at)')
                ->orderBy('created_at', 'ASC')
                ->get();

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
