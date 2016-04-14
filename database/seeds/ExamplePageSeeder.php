<?php

use Illuminate\Database\Seeder;
use Delatbabel\ViewPages\Models\Vpage;

class ExamplePageSeeder extends Seeder
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
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample page directory
        $topdir = $this->getBasePath();

        foreach (scandir($topdir) as $dirname) {
            // Read all of the files and directories under the top level directory.
            if (($dirname == '.') || ($dirname == '..')) {
                continue;
            }

            // If it's a file, load it directly.
            // As of Laravel v5.1.32 someone made a change:
            // https://github.com/laravel/framework/commit/70e504da5ad395d87467826e528dc9edf3f36ef3
            // Means that pages must have the "." as part of the page type.  If you are on a prior
            // version of Laravel you can remove the "." from in front of the $pagetype variables.
            if (! is_dir($topdir . DIRECTORY_SEPARATOR . $dirname)) {
                if (strpos($dirname, '.blade.php')) {
                    $page_name = str_replace('.blade.php', '', $dirname);
                    $pagetype = '.blade.php';
                } elseif (strpos($dirname, '.twig')) {
                    $page_name = str_replace('.twig', '', $dirname);
                    $pagetype = '.twig';
                } else {
                    echo "No template type for $dirname, skipping\n";
                    continue;
                }

                // Create the page
                Vpage::create([
                    'pagekey'           => $page_name,
                    'url'               => $page_name,
                    'name'              => $page_name,
                    'pagetype'          => $pagetype,
                    'description'       => $page_name . ' page loaded from ' . $dirname,
                    'content'           => file_get_contents($topdir . DIRECTORY_SEPARATOR .
                        $dirname),
                ]);
                continue;
            }

            // Read all of the files in each directory.
            foreach (scandir($topdir . DIRECTORY_SEPARATOR . $dirname) as $filename) {
                if (($filename == '.') || ($filename == '..')) {
                    continue;
                }

                if (strpos($filename, '.blade.php')) {
                    $page_name = str_replace('.blade.php', '', $filename);
                    $pagetype = '.blade.php';
                } elseif (strpos($filename, '.twig')) {
                    $page_name = str_replace('.twig', '', $filename);
                    $pagetype = '.twig';
                } else {
                    echo "No template type for $filename, skipping\n";
                    continue;
                }

                // Create the page
                Vpage::create([
                    'pagekey'           => $dirname . '.' . $page_name,
                    'url'               => $dirname . '/' . $page_name,
                    'name'              => $dirname . '.' . $page_name,
                    'pagetype'          => $pagetype,
                    'description'       => $page_name . ' page loaded from ' . $dirname . '/' . $filename,
                    'content'           => file_get_contents($topdir . DIRECTORY_SEPARATOR .
                        $dirname . DIRECTORY_SEPARATOR . $filename),
                ]);
            }
        }
    }
}
