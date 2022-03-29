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

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivitiesHaveImages
 *
 * Interface to uploaded images for activities
 */
class ActivitiesHaveImages extends Model
{
    /**
     * Check if file is a valid image
     *
     * @param string $imgFile
     * @return boolean
     */
    public static function isValidImage($imgFile)
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
     * Get image type
     *
     * @param $ext
     * @param $file
     * @return mixed|null
     */
    public static function getImageType($ext, $file)
    {
        $imagetypes = array(
            array('png', IMAGETYPE_PNG),
            array('jpg', IMAGETYPE_JPEG),
            array('jpeg', IMAGETYPE_JPEG),
            array('gif', IMAGETYPE_GIF)
        );

        for ($i = 0; $i < count($imagetypes); $i++) {
            if (strtolower($ext) == $imagetypes[$i][0]) {
                if (exif_imagetype($file . '.' . $ext) == $imagetypes[$i][1])
                    return $imagetypes[$i][1];
            }
        }

        return null;
    }

    /**
     * Correct image rotation of uploaded image
     *
     * @param $filename
     * @param $image
     */
    private static function correctImageRotation($filename, &$image)
    {
        $exif = @exif_read_data($filename);

        if (!isset($exif['Orientation']))
            return;

        switch($exif['Orientation'])
        {
            case 8:
                $image = imagerotate($image, 90, 0);
                break;
            case 3:
                $image = imagerotate($image, 180, 0);
                break;
            case 6:
                $image = imagerotate($image, 270, 0);
                break;
            default:
                break;
        }
    }

    /**
     * Create thumb file of image
     *
     * @param $srcfile
     * @param $imgtype
     * @param $basefile
     * @param $fileext
     * @return bool
     */
    public static function createThumbFile($srcfile, $imgtype, $basefile, $fileext)
    {
        list($width, $height) = getimagesize($srcfile);

        $factor = 1.0;

        if ($width > $height) {
            if (($width >= 800) and ($width < 1000)) {
                $factor = 0.5;
            } else if (($width >= 1000) and ($width < 1250)) {
                $factor = 0.4;
            } else if (($width >= 1250) and ($width < 1500)) {
                $factor = 0.4;
            } else if (($width >= 1500) and ($width < 2000)) {
                $factor = 0.3;
            } else if ($width >= 2000) {
                $factor = 0.2;
            }
        } else {
            if (($height >= 800) and ($height < 1000)) {
                $factor = 0.5;
            } else if (($height >= 1000) and ($height < 1250)) {
                $factor = 0.4;
            } else if (($height >= 1250) and ($height < 1500)) {
                $factor = 0.4;
            } else if (($height >= 1500) and ($height < 2000)) {
                $factor = 0.3;
            } else if ($height >= 2000) {
                $factor = 0.2;
            }
        }

        $newwidth = $factor * $width;
        $newheight = $factor * $height;

        $dstimg = imagecreatetruecolor($newwidth, $newheight);
        if (!$dstimg)
            return false;

        $srcimage = null;
        switch ($imgtype) {
            case IMAGETYPE_PNG:
                $srcimage = imagecreatefrompng($srcfile);
                imagecopyresampled($dstimg, $srcimage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                static::correctImageRotation($srcfile, $dstimg);
                imagepng($dstimg, $basefile . "_thumb." . $fileext);
                break;
            case IMAGETYPE_JPEG:
                $srcimage = imagecreatefromjpeg($srcfile);
                imagecopyresampled($dstimg, $srcimage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                static::correctImageRotation($srcfile, $dstimg);
                imagejpeg($dstimg, $basefile . "_thumb." . $fileext);
                break;
            case IMAGETYPE_GIF:
                copy($srcfile, $basefile . "_thumb." . $fileext);
                break;
            default:
                return false;
                break;
        }

        return true;
    }

    /**
     * Handle uploaded image
     *
     * @param $activity
     * @param string $fileName
     * @throws \Exception
     */
    public static function addFile($activity, $fileName = '')
    {
        try {
            $att = request()->file('image');
            if ($att != null) {
                $fname = $att->getClientOriginalName() . '_' . uniqid('', true) . '_' . md5($att->getClientOriginalName());
                $fext = $att->getClientOriginalExtension();
                $att->move(public_path() . '/gfx/uploads/', $fname . '.' . $fext);
                if (!static::isValidImage(public_path() . '/gfx/uploads/' . $fname . '.' . $fext)) {
                    unlink(public_path() . '/gfx/uploads/', $fname . '.' . $fext);
                    throw new Exception(__('app.post_invalid_image'));
                }

                $baseFile = public_path() . '/gfx/uploads/' . $fname;
                $fullFile = $baseFile . '.' . $fext;

                if (!static::createThumbFile($fullFile, static::getImageType($fext, $baseFile), $baseFile, $fext)) {
                    throw new Exception('createThumbFile failed', 500);
                }

                rename(public_path() . '/gfx/uploads/' . $fname . '_thumb.' . $fext, public_path() . '/gfx/uploads/' . $fname . '.' . $fext);

                if ($fileName == '') {
                    $fileName = $fname;
                }

                $item = new ActivitiesHaveImages();
                $item->activity = $activity;
                $item->owner = auth()->id();
                $item->name = $fileName;
                $item->file = $fname . '.' . $fext;
                $item->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get files for activity
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public static function getForActivity($id)
    {
        try {
            return ActivitiesHaveImages::where('activity', '=', $id)->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete a file
     *
     * @param $id
     * @throws \Exception
     */
    public static function deleteFile($id)
    {
        try {
            $item = ActivitiesHaveImages::where('id', '=', $id)->first();
            if ((!$item) || ($item->owner !== auth()->id())) {
                throw new Exception(__('app.file_not_found_or_insufficient_permissions'));
            }

            unlink(public_path() . '/gfx/uploads/' . $item->file);
            $item->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
