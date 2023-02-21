<?php

namespace App\Services;

use App\Utils\Helper;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class MediaService {
    const IMAGE_MIMES = ['jpeg','jpg','png','JPG','JPEG','PNG'];
    const IMAGE_MIME_TYPES = ['image/apng','image/avif','image/gif','image/jpeg',
        'image/png','image/svg+xml','image/webp','image/bmp','image/x-icon'];

    const DOCUMENT_MIMES = ['xls','xlsx','doc','docx','ppt','pptx','ods','odt',
        'odp','pdf','XLS','XLSX','DOC','DOCX','PPT','PPTX','ODS','ODT','ODP','PDF'];
    const DOCUMENT_MIME_TYPES = ['application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.oasis.opendocument.spreadsheet','application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.presentation','application/pdf'];

    const ARCHIVE_MIMES = ['zip','7z','gz','rar','tar','ZIP','7Z','GZ','RAR','TAR'];
    const ARCHIVE_MIME_TYPES = ['application/zip','application/x-7z-compressed',
        'application/gzip','application/vnd.rar','application/x-tar'];

    const MIME = [
        "image" => [
            "mimes" => self::IMAGE_MIMES,
            "mimetypes" => self::IMAGE_MIME_TYPES,
        ],
        "document" => [
            "mimes" => self::DOCUMENT_MIMES,
            "mimetypes" => self::DOCUMENT_MIME_TYPES,
        ],
        "archive" => [
            "mimes" => self::ARCHIVE_MIMES,
            "mimetypes" => self::ARCHIVE_MIME_TYPES,
        ],
    ];

    const BLACKLIST_EXTENSION = ['php','js','css','sh','PHP','JS','CSS','SH'];

    public static function fileRule($mime): array
    {
        $mimes = [];
        $mimetypes = [];

        foreach ($mime as $key) {
            if (array_key_exists($key,self::MIME)) {
                $mimes = array_merge($mimes,self::MIME[$key]['mimes']);
                $mimetypes = array_merge($mimetypes,self::MIME[$key]['mimetypes']);
            }
        }

        return [
            'mimes:'.implode(",",$mimes),
            'mimetypes:'.implode(",",$mimetypes)
        ];
    }

    public static function extensionIsNotBlacklist($extension): bool
    {
        return !in_array($extension,self::BLACKLIST_EXTENSION);
    }

    /**
     * @param HasMedia $model
     * @param Request $request
     * @param string|string[] $keys
     * @param boolean $isParent
     * @return HasMedia
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public static function sync(HasMedia $model, Request $request, string|array $keys, bool $isParent = true): HasMedia
    {
        foreach (Helper::arrayStrict($keys) as $fileKey => $mediaKey) {
            if (is_int($fileKey)) {
                $fileKey = $mediaKey;
            }

            if (!$isParent) {
                $fileKey = str_replace(".", "_", $fileKey);
            }

            $keep = $request->get($fileKey . '_keep', []);
            $keepMedia = empty($keep) ? [] : array_map(fn($arr) => ['id' => $arr], Helper::arrayStrict($keep));

            $files = Helper::arrayStrict($request->file($fileKey, []));
            if (!empty($files)) {
                foreach ($files as $file) {
                    if (static::extensionIsNotBlacklist($file->getClientOriginalExtension())) {
                        $media = $model->addMedia($file)
                            ->toMediaCollection($mediaKey);

                        $keepMedia[] = ['id' => $media->id];
                    }
                }
            }

            $model->updateMedia($keepMedia, $mediaKey);
        }

        return $model;
    }
}
