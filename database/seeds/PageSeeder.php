<?php

use Illuminate\Database\Seeder;
use Delatbabel\ViewPages\Models\Vppage;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vppages')->delete();

        // Sample page directory
        $dirname = base_path('database/seeds/examples/pages');

        foreach (scandir($dirname) as $filename) {
            if (($filename == '.') || ($filename == '..')) {
                continue;
            }

            $page_name = str_replace('.blade.php', '', $filename);

            // Create the page
            Vppage::create([
                'vptemplate_key'    => 'layout.main',
                'key'               => 'page.' . $page_name,
                'url'               => $page_name,
                'name'              => $page_name,
                'description'       => $page_name . ' page',
                'content'           => file_get_contents($dirname . DIRECTORY_SEPARATOR . $filename),
            ]);
        }
    }
}
