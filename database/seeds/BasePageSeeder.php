<?php

use Delatbabel\ViewPages\Models\Vpage;
use Illuminate\Database\Seeder;

class BasePageSeeder extends Seeder
{
    /**
     * Override this function to provide a base path
     *
     * @return string
     */
    protected function getBasePath()
    {
        return base_path('database/seeds/examples');
    }

    /**
     * Page creation hook.
     *
     * Override this function in sub classes to do something with the page after it is created.
     *
     * @param Vpage $page
     * @return void
     */
    protected function pageHook(Vpage $page)
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

                // Find the page name and the page type
                if (strpos($filename, '.blade.php')) {
                    $page_name = str_replace('.blade.php', '', $filename);
                    $pagetype  = '.blade.php';
                } elseif (strpos($filename, '.twig')) {
                    $page_name = str_replace('.twig', '', $filename);
                    $pagetype  = '.twig';
                } else {
                    echo "No template type for $filename, skipping\n";
                    continue;
                }

                // The directory key is the directory name with / replaced by .
                // This is prepended to the page name to get the full page key
                $dirkey = str_replace('/', '.', $dirname);

                // Create the page
                if (empty($dirname)) {
                    $page = Vpage::create([
                        'pagekey'           => $page_name,
                        'url'               => $page_name,
                        'name'              => $page_name,
                        'pagetype'          => $pagetype,
                        'description'       => $page_name . ' page loaded from ' . $filename,
                        'content'           => file_get_contents($scanthis . DIRECTORY_SEPARATOR . $filename),
                    ]);
                } else {
                    $page = Vpage::create([
                        'pagekey'           => $dirkey . '.' . $page_name,
                        'url'               => $dirname . '/' . $page_name,
                        'name'              => $dirkey . '.' . $page_name,
                        'pagetype'          => $pagetype,
                        'description'       => $page_name . ' page loaded from ' . $dirname . '/' . $filename,
                        'content'           => file_get_contents($scanthis . DIRECTORY_SEPARATOR . $filename),
                    ]);
                }

                // Page creation hook
                $this->pageHook($page);
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
