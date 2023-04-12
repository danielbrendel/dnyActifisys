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
 * Class GalleryThreadModel
 * 
 * Interface to Gallery item commenting
 */
class GalleryThreadModel extends Model
{
    use HasFactory;

    /**
     * Add new gallery thread message
     * 
     * @param $message
     * @param $itemId
     * @return void
     * @throws \Exception
     */
    public static function addThread($message, $itemId)
    {
        try {
            $entry = new self();
            $entry->content = $message;
            $entry->userId = auth()->id();
            $entry->itemId = $itemId;
            $entry->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Fetch comment thread of gallery item
     * 
     * @param $itemId
     * @param $pagination
     * @return mixed
     * @throws \Exception
     */
    public static function fetch($itemId, $pagination = null)
    {
        try {
            $query = static::where('itemId', '=', $itemId);

            if ($pagination !== null) {
                $query->where('id', '<', $pagination);
            }

            return $query->orderBy('id', 'DESC')->limit(env('APP_GALLERYTHREADPACKLIMIT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Lock gallery thread item
     * 
     * @param $id
     * @return void
     * @throws \Exception
     */
    public static function lock($id)
    {
        try {
            $item = static::where('id', '=', $id)->first();
            if (!$item) {
                throw new \Exception('Item not found');
            }

            $user = User::getByAuthId();
            if ((!$user) || (((!$user->maintainer) || (!$user->admin)) && ($user->id !== $item->userId))) {
                throw new \Exception(__('app.insufficient_permissions'));
            }

            $item->locked = true;
            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit gallery thread item
     * 
     * @param $id
     * @param $message
     * @return void
     * @throws \Exception
     */
    public static function edit($id, $message)
    {
        try {
            $item = static::where('id', '=', $id)->first();
            if ((!$item) || ($item->locked)) {
                throw new \Exception('Item not found or locked');
            }

            $user = User::getByAuthId();
            if ((!$user) || (((!$user->maintainer) || (!$user->admin)) && ($user->id !== $item->userId))) {
                throw new \Exception(__('app.insufficient_permissions'));
            }

            $item->content = $message;
            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
