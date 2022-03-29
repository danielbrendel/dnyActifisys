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

/**
 * Class FaqModel
 *
 * Represents the FAQ of the home
 */
class FaqModel extends Model
{
    /**
     * Get all FAQ items
     *
     * @return FaqModel[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAll()
    {
        return FaqModel::all();
    }
}
