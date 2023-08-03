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

/**
 * Class PageModel
 * 
 * Interface to custom page management
 */
class PageModel extends Model
{
    use HasFactory;

    /**
     * Get page by slug
     * 
     * @param $slugOrId
     * @return mixed
     * @throws Exception
     */
    public static function getPage($slugOrId)
    {
        try {
            if (is_numeric($slugOrId)) {
                return PageModel::where('id', '=', $slugOrId)->where('active', '=', true)->first();
            } else {
                return PageModel::where('slug', '=', $slugOrId)->where('active', '=', true)->first();
            }
            
            return null;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get list of linkable pages
     * 
     * @return array
     * @throws Exception
     */
    public static function getLinkablePages()
    {
        try {
            $result = [];

            $pages = PageModel::where('active', '=', true)->orderBy('id', 'asc')->get();
            foreach ($pages as $page) {
                $result[] = (object)[
                    'url' => url('/page/' . $page->slug),
                    'label' => $page->label
                ];
            }

            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
