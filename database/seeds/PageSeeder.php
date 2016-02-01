<?php

use Illuminate\Database\Seeder;
use Delatbabel\ViewPages\Models\Vpage;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vpages')->delete();

        // Sample page directory
        $topdir = base_path('database/seeds/examples');

        foreach (scandir($topdir) as $dirname) {
            // Read all of the directories under the top level directory.
            if (($dirname == '.') || ($dirname == '..')) {
                continue;
            }

            if (! is_dir($topdir . DIRECTORY_SEPARATOR . $dirname)) {
                continue;
            }

            // Read all of the files in each directory.
            foreach (scandir($topdir . DIRECTORY_SEPARATOR . $dirname) as $filename) {
                if (($filename == '.') || ($filename == '..')) {
                    continue;
                }

                $page_name = $dirname . '.' . str_replace('.blade.php', '', $filename);

                // Create the page
                Vpage::create([
                    'key'               => $dirname . '.' . $page_name,
                    'url'               => $dirname . '/' . $page_name,
                    'name'              => $dirname . '.' . $page_name,
                    'description'       => $page_name . ' page loaded from ' . $filename,
                    'content'           => file_get_contents($dirname . DIRECTORY_SEPARATOR . $filename),
                ]);
            }
        }
    }
}
