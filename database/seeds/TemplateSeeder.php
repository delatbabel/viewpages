<?php

use Illuminate\Database\Seeder;
use Delatbabel\ViewPages\Models\Vptemplate;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vptemplates')->delete();

        // Sample template directory
        $dirname = base_path('examples/templates');

        foreach (scandir($dirname) as $filename) {
            if (($filename == '.') || ($filename == '..')) {
                continue;
            }

            $template_name = str_replace('.blade.php', '', $filename);

            // Create the template
            Vptemplate::create([
                'key'           => 'layout.' . $template_name,
                'name'          => $template_name,
                'description'   => $template_name . ' template',
                'content'       => file_get_contents($dirname . DIRECTORY_SEPARATOR . $filename),
            ]);
        }
    }
}
