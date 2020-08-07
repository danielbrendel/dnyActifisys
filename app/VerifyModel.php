<?php

/*
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VerifyModel
 *
 * Interface to account verification implementations
 */
class VerifyModel extends Model
{
    const STATE_NOTAPPLIED = -1;
    const STATE_INPROGRESS = 0;
    const STATE_VERIFIED = 1;
    const STATE_DECLINED = 2;

    /**
     * Check if file is a valid image
     *
     * @param string $imgFile
     * @return boolean
     */
    private static function isValidImage($imgFile)
    {
        $imagetypes = array(
            IMAGETYPE_PNG,
            IMAGETYPE_JPEG,
            IMAGETYPE_GIF
        );

        if (!file_exists($imgFile)) {
            return false;
        }

        foreach ($imagetypes as $type) {
            if (exif_imagetype($imgFile) === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add verify account request
     *
     * @param $userId
     * @param array $attr
     * @throws Exception
     */
    public static function addVerifyAccount($userId, array $attr)
    {
        try {
            if ((!isset($attr['confirmation'])) || ((bool)$attr['confirmation'] === false)) {
                throw new Exception(__('app.verify_permission_unconfirmed'));
            }

            $newFrontName = null;
            $front = request()->file('idcard_front');
            if ($front != null) {
                $newFrontName = 'idcard_front_' . md5(random_bytes(55)) . uniqid('', true) . '.' . $front->getClientOriginalExtension();

                $front->move(base_path() . '/public/gfx/idcards', $newFrontName);

                if (!static::isValidImage(base_path() . '/public/gfx/idcards/' . $newFrontName)) {
                    unlink(base_path() . '/public/gfx/avatars/' . $newFrontName);
                    throw new Exception(__('Invalid image specified'));
                }
            }

            $newBackName = null;
            $back = request()->file('idcard_back');
            if ($back != null) {
                $newBackName = 'idcard_back_' . md5(random_bytes(55)) . uniqid('', true) . '.' . $back->getClientOriginalExtension();

                $back->move(base_path() . '/public/gfx/idcards', $newBackName);

                if (!static::isValidImage(base_path() . '/public/gfx/idcards/' . $newBackName)) {
                    unlink(base_path() . '/public/gfx/avatars/' . $newBackName);
                    throw new Exception(__('Invalid image specified'));
                }
            }

            if (($newFrontName === null) || ($newBackName === null)) {
                throw new Exception('At least one file name is null');
            }

            $item = VerifyModel::where('userId', '=', $userId)->first();
            if (!$item) {
                $item = new VerifyModel();
            }
            $item->userId = $userId;
            $item->idcard_front = $newFrontName;
            $item->idcard_back = $newBackName;
            $item->confirmed = true;
            $item->state = self::STATE_INPROGRESS;
            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set verification status
     *
     * @param $userId
     * @param $state
     * @param string $reason
     * @throws Exception
     */
    public static function verifyStatus($userId, $state, $reason = '')
    {
        try {
            $item = VerifyModel::where('userId', '=', $userId)->first();
            if (!$item) {
                throw new Exception(__('app.user_not_found'));
            }
            $item->state = $state;
            $item->last_reason = $reason;
            $item->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get verification status
     *
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    public static function getState($userId)
    {
        try {
            $item = VerifyModel::where('userId', '=', $userId)->first();
            if (!$item) {
                return self::STATE_NOTAPPLIED;
            }

            return $item->state;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Fetch pack of in progress verification requests
     *
     * @param int $limit
     * @return mixed
     * @throws Exception
     */
    public static function fetchPack($limit = 20)
    {
        try {
            $list = VerifyModel::where('state', '=', self::STATE_INPROGRESS)->limit($limit)->orderBy('updated_at', 'asc')->get();
            foreach ($list as &$item) {
                $item->user = User::get($item->userId);
            }

            return $list;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
