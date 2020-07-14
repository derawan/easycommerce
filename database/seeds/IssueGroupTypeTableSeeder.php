<?php

use App\Models\IssueGroupType;
use Illuminate\Database\Seeder;

class IssueGroupTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $issues = [
            ['name'=>'PRODUCT_SPECIALIST_ISSUES'],
            ['name'=>'MERCHANDISER_ISSUES']
        ];
        foreach ($issues as $k => $issue) {
            IssueGroupType::create($issue);
        }
    }
}
