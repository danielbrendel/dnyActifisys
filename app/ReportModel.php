<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class ReportModel
 *
 * Represents the interface for reporting
 */
class ReportModel extends Model
{
    const REPORT_PACK_COUNT = 15;

    /**
     * Throw if type is unknown
     *
     * @param $type
     * @throws \Exception
     */
    private static function validateEntityType($type)
    {
        try {
            $types = array('ENT_ACTIVITY', 'ENT_USER', 'ENT_COMMENT');

            if (!in_array($type, $types)) {
                throw new \Exception('Unknown type: ' . $type, 404);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add report to post if not already
     *
     * @param $userId
     * @param $entityId
     * @param $entType
     * @throws \Exception
     */
    public static function addReport($userId, $entityId, $entType)
    {
        try {
            static::validateEntityType($entType);

            $report = ReportModel::where('userId', '=', $userId)->where('entityId', '=', $entityId)->where('type', '=', $entType)->first();
            if ($report) {
                //throw new \Exception(__('app.already_reported'));
                return;
            }

            $report = new ReportModel();
            $report->userId = $userId;
            $report->entityId = $entityId;
            $report->type = $entType;
            $report->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get report pack of entity
     *
     * @param $entType
     * @return mixed
     * @throws \Exception
     */
    public static function getReportPack($entType)
    {
        try {
            $list = DB::select("SELECT entityId, id, type, COUNT(*) as 'count' FROM report_models WHERE type = ? GROUP BY entityId, id, type ORDER BY count DESC LIMIT ?", array($entType, self::REPORT_PACK_COUNT));

            $result = array();

            foreach ($list as $item) {
                if (!static::packItemExists($item, $result)) {
                    $item->count = ReportModel::where('type', '=', $item->type)->where('entityId', '=', $item->entityId)->count();
                    $result[] = $item;
                }
            }

            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if item does already exist in list
     *
     * @param $item
     * @param array $list
     * @return bool
     */
    private static function packItemExists($item, array $list)
    {
        foreach ($list as $entry) {
            if (($entry->entityId === $item->entityId) && ($entry->type === $item->type)) {
                return true;
            }
        }

        return false;
    }
}
