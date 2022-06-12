<?php

namespace App\Managers;

use App\Managers\Manager;

class UploadManager extends Manager
{
    public static function uploadImage($uploadName): string
    {
        $request = request();
        if ($request->hasFile($uploadName)) {
            if ($request->file($uploadName)->isValid()) {
                $photo = $request->file($uploadName);
                $fileName = $photo->getClientOriginalName();
                $fileName = str_replace(' ', '_', $fileName);
                $date = \Carbon\Carbon::now()->format('Y-m-d');
                $storeDir = "$date/$fileName";
                $storePath = $photo->storeAs('images', $storeDir);
                return $storePath;
            }
        }
        return '';
    }

    public static function uploadAvatar($uploadName): string
    {
        $request = request();
        if ($request->hasFile($uploadName)) {
            if ($request->file($uploadName)->isValid()) {
                $photo = $request->file($uploadName);
                $fileName = $photo->getClientOriginalName();
                $fileName = str_replace(' ', '_', $fileName);
                $date = \Carbon\Carbon::now()->format('Y-m-d');
                $storeDir = "$date/$fileName";
                $storePath = $photo->storeAs('images/avatars', $storeDir);
                return $storePath;
            }
        }
        return '';
    }
}
