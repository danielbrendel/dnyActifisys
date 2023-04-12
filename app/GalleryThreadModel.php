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
}
