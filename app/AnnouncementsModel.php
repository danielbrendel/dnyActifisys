<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class AnnouncementsModel
 *
 * Interface to announcements
 */
class AnnouncementsModel extends Model
{
    /**
     * Add new announcement
     * 
     * @param $title
     * @param $content
     * @param $until
     * @return void
     * @throws \Exception
     */
    public static function add($title, $content, $until)
    {
        try {
            $item = new self();
            $item->title = $title;
            $item->content = $content;
            $item->until = $until;
            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Query all current announcements
     * 
     * @return mixed
     * @throws \Exception
     */
    public static function queryAll()
    {
        try {
            $dateNow = Carbon::now();

            return static::where('until', '>=', $dateNow)->get();
        } catch (\Exceptions $e) {
            throw $e;
        }
    }
}
