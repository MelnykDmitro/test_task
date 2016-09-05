<?php

namespace TestTask\Lib;

class ImageUploader
{
    const ALLOWED_EXTENSIONS = ['jpg', 'gif', 'png'];
    const UPLOADS_FOLDER = ROOT_PATH . 'public/uploads/';

    const MAX_WIDTH = 320;
    const MAX_HEIGHT = 240;

    public static function getUploadedImage()
    {
        if (empty($_FILES['image'])){
            return false;
        }

        $extension = self::getFileExtension();
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return false;
        }

        $file_name = sha1(uniqid()) . '.' . $extension;
        $uploaded = self::upload($file_name);
        if (!$uploaded) {
            return false;
        }

        $imagick = new \Imagick(self::UPLOADS_FOLDER . $file_name);
        $image_sizes = $imagick->getImageGeometry();
        if ($image_sizes['width'] > self::MAX_WIDTH) {
            $changed = true;
            $imagick->resizeImage(self::MAX_WIDTH, 0, 0, 1);
        }
        if ($image_sizes['height'] > self::MAX_HEIGHT) {
            $changed = true;
            $imagick->resizeImage(0, self::MAX_HEIGHT, 0, 1);
        }
        if (isset($changed)) {
            $imagick->writeImage(self::UPLOADS_FOLDER . $file_name);
        }

        return $file_name;
    }

    private static function getFileExtension()
    {
        return strtolower(end(explode('.', $_FILES['image']['name'])));
    }

    private static function upload($file_name)
    {
        return move_uploaded_file($_FILES['image']['tmp_name'], self::UPLOADS_FOLDER . $file_name);
    }
}
