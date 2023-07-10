<?php

use App\Actions\Nom\Accelerator\SyncProjectStatus;
use App\Actions\Nom\Accelerator\UpdateProjectFunding;
use App\Models\Nom\AcceleratorProject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nom_accelerator_projects', function (Blueprint $table) {
            $table->after('qsr_requested', function (Blueprint $table) {
                $table->bigInteger('znn_paid')->default(0);
                $table->bigInteger('qsr_paid')->default(0);
                $table->bigInteger('znn_remaining')->default(0);
                $table->bigInteger('qsr_remaining')->default(0);
            });
        });

        $acceptedProjects = AcceleratorProject::get();
        $acceptedProjects->each(function ($project) {
            (new SyncProjectStatus($project))->execute();
            (new UpdateProjectFunding($project))->execute();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nom_accelerator_projects', function (Blueprint $table) {
            $table->dropColumn([
                'znn_paid',
                'qsr_paid',
                'znn_remaining',
                'qsr_remaining',
            ]);
        });
    }
};
