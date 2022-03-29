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
 * Class CategoryModel
 *
 * Interface to categories
 */
class CategoryModel extends Model
{
    const SIZE_IMAGE_WIDTH = 327;
    const SIZE_IMAGE_HEIGHT = 99;

    /**
     * Add new category item
     *
     * @param $attr
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public static function add($attr)
    {
        try {
            $item = new CategoryModel();
            $item->name = $attr['name'];
            $item->description = $attr['description'];
            $item->image = '';
            $item->save();

            $image = request()->file('image');
            if ($image != null) {
                $tmpName = md5(random_bytes(55));

                $image->move(base_path() . '/public/gfx/categories', $tmpName . '.' . $image->getClientOriginalExtension());

                list($width, $height) = getimagesize(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension());

                $resimg = imagecreatetruecolor(self::SIZE_IMAGE_WIDTH, self::SIZE_IMAGE_HEIGHT);
                if (!$resimg)
                    throw new \Exception('imagecreatetruecolor() failed');

                $srcimage = null;
                $newname =  md5_file(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension()) . '.' . $image->getClientOriginalExtension();
                switch (AppModel::getImageType(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension())) {
                    case IMAGETYPE_PNG:
                        $srcimage = imagecreatefrompng(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension());
                        imagecopyresampled($resimg, $srcimage, 0, 0, 0, 0, self::SIZE_IMAGE_WIDTH, self::SIZE_IMAGE_HEIGHT, $width, $height);
                        imagepng($resimg, base_path() . '/public/gfx/categories/' . $newname);
                        break;
                    case IMAGETYPE_JPEG:
                        $srcimage = imagecreatefromjpeg(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension());
                        imagecopyresampled($resimg, $srcimage, 0, 0, 0, 0, self::SIZE_IMAGE_WIDTH, self::SIZE_IMAGE_HEIGHT, $width, $height);
                        imagejpeg($resimg, base_path() . '/public/gfx/categories/' . $newname);
                        break;
                    default:
                        return back()->with('error', __('app.settings_category_invalid_image_type'));
                        break;
                }

                unlink(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension());

                $item->image = $newname;
                $item->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit category item
     *
     * @param $id
     * @param $attr
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public static function edit($id, $attr)
    {
        try {
            $item = CategoryModel::where('id', '=', $id)->first();
            if (!$item) {
                throw new \Exception('Item not found: ' . $id);
            }
            $item->name = $attr['name'];
            $item->description = $attr['description'];
            $item->save();

            $image = request()->file('image');
            if ($image != null) {
                $tmpName = md5(random_bytes(55));

                $image->move(base_path() . '/public/gfx/categories', $tmpName . '.' . $image->getClientOriginalExtension());

                list($width, $height) = getimagesize(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension());

                $resimg = imagecreatetruecolor(self::SIZE_IMAGE_WIDTH, self::SIZE_IMAGE_HEIGHT);
                if (!$resimg)
                    throw new \Exception('imagecreatetruecolor() failed');

                $srcimage = null;
                $newname =  md5_file(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension()) . '.' . $image->getClientOriginalExtension();
                switch (AppModel::getImageType(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension())) {
                    case IMAGETYPE_PNG:
                        $srcimage = imagecreatefrompng(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension());
                        imagecopyresampled($resimg, $srcimage, 0, 0, 0, 0, self::SIZE_IMAGE_WIDTH, self::SIZE_IMAGE_HEIGHT, $width, $height);
                        imagepng($resimg, base_path() . '/public/gfx/categories/' . $newname);
                        break;
                    case IMAGETYPE_JPEG:
                        $srcimage = imagecreatefromjpeg(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension());
                        imagecopyresampled($resimg, $srcimage, 0, 0, 0, 0, self::SIZE_IMAGE_WIDTH, self::SIZE_IMAGE_HEIGHT, $width, $height);
                        imagejpeg($resimg, base_path() . '/public/gfx/categories/' . $newname);
                        break;
                    default:
                        return back()->with('error', __('app.settings_category_invalid_image_type'));
                        break;
                }

                unlink(base_path() . '/public/gfx/categories/' . $tmpName . '.' . $image->getClientOriginalExtension());

                $item->image = $newname;
                $item->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set inactive status of an item
     *
     * @param $id
     * @param $status
     * @throws \Exception
     */
    public static function setInactiveStatus($id, $status)
    {
        try {
            $item = CategoryModel::where('id', '=', $id)->first();
            if (!$item) {
                throw new \Exception('Item not found: ' . $id);
            }

            $item->inactive = $status;
            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all active categories
     *
     * @return mixed
     * @throws \Exception
     */
    public static function fetch()
    {
        try {
            return CategoryModel::where('inactive', '=', false)->orderBy('name', 'asc')->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
