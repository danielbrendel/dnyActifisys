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

use Illuminate\Database\Eloquent\Model;
use Exception;

/**
 * Class LocationModel
 *
 * Interface to locations
 */
class LocationModel extends Model
{
    /**
     * Add new location
     * 
     * @param $name
     * @return void
     * @throws Exception
     */
    public static function add($name)
    {
        try {
            $item = new LocationModel();
            $item->name = strtolower($name);
            $item->active = true;
            $item->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit location
     * 
     * @param $id
     * @param $new_name
     * @return void
     * @throws Exception
     */
    public static function edit($id, $new_name)
    {
        try {
            $item = LocationModel::where('id', '=', $id)->first();
            if (!$item) {
                throw new Exception('Item not found: ' . $id);
            }

            $item->name = strtolower($new_name);
            $item->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set active status of an item
     *
     * @param $id
     * @param $status
     * @return void
     * @throws Exception
     */
    public static function setActiveStatus($id, $status)
    {
        try {
            $item = LocationModel::where('id', '=', $id)->first();
            if (!$item) {
                throw new Exception('Item not found: ' . $id);
            }

            $item->active = $status;
            $item->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Fetch all active locations
     * 
     * @return mixed
     * @throws Exception
     */
    public static function fetch()
    {
        try {
            return LocationModel::where('active', '=', true)->get();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get amount of active locations
     * 
     * @return int
     * @throws Exception
     */
    public static function amount()
    {
        try {
            return LocationModel::where('active', '=', true)->count();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Query locations by term
     * 
     * @param $term
     * @return mixed
     * @throws Exception
     */
    public static function queryByTerm($term)
    {
        try {
            return LocationModel::whereRaw('LOWER(name) LIKE "%' . strtolower($term) . '%"')->get();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
