<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
