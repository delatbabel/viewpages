<?php

use Delatbabel\ViewPages\Models\Vobject;
use Illuminate\Database\Seeder;

class BaseObjectSeeder extends Seeder
{
    /**
     * Override this function to provide a base path
     *
     * @return string
     */
    protected function getBasePath()
    {
        return base_path('database/seeds/objects');
    }

    /**
     * Page creation hook.
     *
     * Override this function in sub classes to do something with the object after it is created.
     *
     * @param Vobject $object
     * @return void
     */
    protected function objectHook(Vobject $object)
    {
        // default no-op
    }

    protected function loadFiles($dirname = null)
    {
        $topdir = $this->getBasePath();

        // Find the full path name to scan
        if (empty($dirname)) {
            $scanthis = $topdir;
        } else {
            $scanthis = $topdir . DIRECTORY_SEPARATOR . $dirname;
        }

        foreach (scandir($scanthis) as $filename) {

            // Skip directory link files
            if (($filename == '.') || ($filename == '..')) {
                continue;
            }

            if (is_dir($scanthis . DIRECTORY_SEPARATOR . $filename)) {
                // Recurse into lower level directory
                if (empty($dirname)) {
                    $this->loadFiles($filename);
                } else {
                    $this->loadFiles($dirname . DIRECTORY_SEPARATOR . $filename);
                }
            } else {

                // This is a file.  Load it into the database.

                // Find the object name
                if (strpos($filename, '.html')) {
                    $object_name = str_replace('.html', '', $filename);
                } else {
                    echo "No template type for $filename, skipping\n";
                    continue;
                }

                // The directory key is the directory name with / replaced by .
                // This is prepended to the object name to get the full object key
                $dirkey = str_replace('/', '.', $dirname);

                // Create the object
                if (empty($dirname)) {
                    $object = Vobject::create([
                        'objectkey'         => $object_name,
                        'name'              => $object_name,
                        'description'       => $object_name . ' object loaded from ' . $filename,
                        'content'           => file_get_contents($scanthis . DIRECTORY_SEPARATOR . $filename),
                    ]);
                } else {
                    $object = Vobject::create([
                        'objectkey'         => $dirkey . '.' . $object_name,
                        'name'              => $dirkey . '.' . $object_name,
                        'description'       => $object_name . ' object loaded from ' . $dirname . '/' . $filename,
                        'content'           => file_get_contents($scanthis . DIRECTORY_SEPARATOR . $filename),
                    ]);
                }

                // Page creation hook
                $this->objectHook($object);
            }
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Read all of the files and directories under the top level directory.
        $this->loadFiles();
    }
}
