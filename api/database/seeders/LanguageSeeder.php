<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'pt', 'name' => 'Portuguese'],
            ['code' => 'de', 'name' => 'German'],
            ['code' => 'it', 'name' => 'Italian'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'ja', 'name' => 'Japanese'],
            ['code' => 'zh', 'name' => 'Chinese'],
        ];
        Language::upsert($languages, uniqueBy: ['code'], update: ['name']);
    }
}
