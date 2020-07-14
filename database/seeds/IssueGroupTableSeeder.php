<?php

use App\Models\IssueGroup;
use App\Models\IssueGroupType;
use Illuminate\Database\Seeder;

class IssueGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $issues = [
            ['name'=>'Not able to work', 'group_id' => IssueGroupType::where('name','PRODUCT_SPECIALIST_ISSUES')->first()->id],
            ['name'=>'Customer health escalation', 'group_id' => IssueGroupType::where('name','PRODUCT_SPECIALIST_ISSUES')->first()->id],
            ['name'=>'Harassment Issue', 'group_id' => IssueGroupType::where('name','PRODUCT_SPECIALIST_ISSUES')->first()->id],
            ['name'=>'Security concern', 'group_id' => IssueGroupType::where('name','PRODUCT_SPECIALIST_ISSUES')->first()->id],
            ['name'=>'Store Manager Issue', 'group_id' => IssueGroupType::where('name','PRODUCT_SPECIALIST_ISSUES')->first()->id],
            ['name'=>'Others', 'group_id' => IssueGroupType::where('name','PRODUCT_SPECIALIST_ISSUES')->first()->id],
            ['name'=>'Out of Stock Issue', 'group_id' => IssueGroupType::where('name','PRODUCT_SPECIALIST_ISSUES')->first()->id],
            ['name'=>'Broken Tasting Bar/Stand Issue', 'group_id' => IssueGroupType::where('name','PRODUCT_SPECIALIST_ISSUES')->first()->id],
            ['name'=>'Approached By Officials Issue', 'group_id' => IssueGroupType::where('name','PRODUCT_SPECIALIST_ISSUES')->first()->id],
            ['name'=>'Not able to work', 'group_id' => IssueGroupType::where('name','MERCHANDISER_ISSUES')->first()->id],
            ['name'=>'Stock Issue', 'group_id' => IssueGroupType::where('name','MERCHANDISER_ISSUES')->first()->id],
            ['name'=>'Fixture Issue', 'group_id' => IssueGroupType::where('name','MERCHANDISER_ISSUES')->first()->id],
            ['name'=>'Store manager problem', 'group_id' => IssueGroupType::where('name','MERCHANDISER_ISSUES')->first()->id],
            ['name'=>'Others', 'group_id' => IssueGroupType::where('name','MERCHANDISER_ISSUES')->first()->id],
            ['name'=>'Price Issue', 'group_id' => IssueGroupType::where('name','MERCHANDISER_ISSUES')->first()->id],
            ['name'=>'POSM Issue', 'group_id' => IssueGroupType::where('name','MERCHANDISER_ISSUES')->first()->id],
            ['name'=>'Outlet Support Issue', 'group_id' => IssueGroupType::where('name','MERCHANDISER_ISSUES')->first()->id]
        ];
        foreach ($issues as $k => $issue) {
            IssueGroup::create($issue);
        }
    }
}
