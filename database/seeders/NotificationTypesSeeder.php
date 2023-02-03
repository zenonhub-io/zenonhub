<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NotificationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'New project',
                'code' => 'az-project-created',
                'type' => 'general',
                'description' => 'A new Accelerator project is submitted',
                'content' => 'A new Accelerator Z project has been submitted',
                'link' => 'az.overview',
                'is_basic' => true,
                'is_active' => true,
            ],
            [
                'name' => 'New phase',
                'code' => 'az-phase-added',
                'type' => 'general',
                'description' => 'A new project phase is added',
                'content' => 'A new project phase has been added',
                'link' => 'az.overview',
                'is_basic' => true,
                'is_active' => true,
            ],
            [
                'name' => 'New pillar',
                'code' => 'pillar-registered',
                'type' => 'general',
                'description' => 'A new pillar is registered',
                'content' => 'A new pillar has been registered',
                'link' => 'pillars.overview',
                'is_basic' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Pillar updated',
                'code' => 'pillar-updated',
                'type' => 'general',
                'description' => 'A pillar updates their rewards',
                'content' => 'A pillar updated their rewards',
                'link' => 'pillars.overview',
                'is_basic' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Pillar revoked',
                'code' => 'pillar-revoked',
                'type' => 'general',
                'description' => 'A pillar is revoked',
                'content' => 'A pillar has been revoked',
                'link' => 'pillars.overview',
                'is_basic' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Delegating pillar updated',
                'code' => 'delegating-pillar-updated',
                'type' => 'delegate',
                'description' => 'A pillar you delegate to changes their rewards',
                'content' => 'A pillar you are delegating to has updated their rewards',
                'link' => 'pillars.overview',
                'is_basic' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Voting reminders',
                'code' => 'pillar-project-vote-reminder',
                'type' => 'pillar',
                'description' => 'A reminder to vote on a project or phase before it closes',
                'content' => 'You have not yet voted',
                'link' => 'az.overview',
                'is_basic' => false,
                'is_active' => true,
            ],
            [
                'name' => 'New delegator',
                'code' => 'pillar-delegator-added',
                'type' => 'pillar',
                'description' => 'An address starts delegating to you',
                'content' => 'You have a new delegator',
                'link' => 'pillars.overview',
                'is_basic' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Lose delegator',
                'code' => 'pillar-delegator-lost',
                'type' => 'pillar',
                'description' => 'An address stops delegating to you',
                'content' => 'You have lost a delegator',
                'link' => 'pillars.overview',
                'is_basic' => false,
                'is_active' => true,
            ],
        ];

        \App\Models\NotificationType::insert($data);
    }
}
