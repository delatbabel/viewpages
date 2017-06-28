<?php

use Delatbabel\ViewPages\Models\Vpage;
use Illuminate\Database\Seeder;
use Delatbabel\NestedCategories\Models\Category;

class NamespacedPageSeeder extends Seeder
{
    /** @var Category */
    protected $adminPageCategory;

    public function __construct()
    {
        $this->adminPageCategory = Category::where('description', '=', 'Page Types > Admin')
            ->first();
    }

    /**
     * Override this function to provide a base path
     *
     * @return string
     */
    protected function getBasePath()
    {
        return base_path('database/seeds/namespaced-views');
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
        if (! empty($this->adminPageCategory)) {
            $page->category()->associate($this->adminPageCategory);
            $page->save();
        }
    }

    protected function loadFiles($dirname = null, $namespace = null)
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
                    $this->loadFiles($filename, $namespace);
                } else {
                    $this->loadFiles($dirname . DIRECTORY_SEPARATOR . $filename, $namespace);
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

                // Strip off the leading namespace component
                $dirkey = substr($dirkey, strlen($namespace));

                // Create the page
                if (empty($dirkey)) {
                    $page = Vpage::create([
                        'namespace'         => $namespace,
                        'pagekey'           => $page_name,
                        'url'               => $page_name,
                        'name'              => $page_name,
                        'pagetype'          => $pagetype,
                        'description'       => $page_name . ' page loaded from ' . $filename,
                        'content'           => file_get_contents($scanthis . DIRECTORY_SEPARATOR . $filename),
                    ]);
                } else {
                    $page = Vpage::create([
                        'namespace'         => $namespace,
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
        $topdir = $this->getBasePath();

        foreach (scandir($topdir) as $filename) {

            // Skip directory link files
            if (($filename == '.') || ($filename == '..')) {
                continue;
            }

            if (is_dir($topdir . DIRECTORY_SEPARATOR . $filename)) {
                // Recurse into lower level directory, providing the namespace
                $this->loadFiles($filename, $filename);
            }
        }
    }
}
