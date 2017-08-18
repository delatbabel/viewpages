<?php
/**
 * Class ImageHelper
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Helpers;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Log;

/**
 * Class ImageHelper
 *
 * Provides various functions including getting the raw data of an image or getting
 * the URL of an image when it is stored on cloud (AWS S3) storage.
 *
 * ### Example
 *
 * <code>
 * $image_path = ImageHelper::getImageUrl('path/to/stored/image.jpg');
 * </code>
 *
 * @link https://stackoverflow.com/questions/25323753/laravel-league-flysystem-getting-file-url-with-aws-s3
 */
class ImageHelper
{
    /**
     * Get raw image attribute based on its path on file storage
     *
     * @param $path string
     * @return string
     */
    public static function getRawImage($path)
    {
        $disk = config('filesystems.default');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk($disk);

        // Check that the path is valid
        if (! $storage->exists($path)) {
            return '';
        }

        // Fetch the raw data from the storage
        $mime_type = $storage->mimeType($path);
        $raw_image = "data:$mime_type;base64," . base64_encode($storage->get($path));
        return $raw_image;
    }

    /**
     * Get image URL attribute based on its path on file storage
     *
     * @param $path string
     * @return string
     * @link https://stackoverflow.com/questions/25323753/laravel-league-flysystem-getting-file-url-with-aws-s3
     */
    public static function getImageUrl($path)
    {
        $disk = config('filesystems.default');
        #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        #    'Get Image URL for path = ' . $path);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk($disk);

        // Check that the path is valid
        if (! $storage->exists($path)) {
            return '';
        }

        #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        #    'Filesystem driver = ' . $disk);
        switch ($disk) {
            case 's3':
                /** @var Filesystem $driver */
                $driver = $storage->getDriver();

                /** @var AwsS3Adapter $adapter */
                $adapter = $driver->getAdapter();

                /** @var S3Client $client */
                $client = $adapter->getClient();
                $bucket = config('filesystems.disks.s3.bucket');
                $url    = $client->getObjectUrl($bucket, $path);
                #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
                #    'URL = ' . $url);
                return $url;
                break;

            case 'local':
                return config('url') . '/' . $path;
                break;

            default:
                return static::getRawImage($path);
                break;
        }
    }
}
