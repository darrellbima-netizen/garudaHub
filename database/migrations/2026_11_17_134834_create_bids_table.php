
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id');
            $table->foreignId('developer_id');

            $table->bigInteger('harga');
            $table->integer('durasi');

            $table->text('proposal');

            $table->boolean('is_winner')->default(false);

            $table->timestamp('created_at')->useCurrent();

            // Foreign Key ke projects
            $table->foreign('project_id', 'fk_bids_project')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            // Foreign Key ke users
            $table->foreign('developer_id', 'fk_bids_developer')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Satu developer hanya boleh bid sekali per project
            $table->unique(
                ['project_id', 'developer_id'],
                'uq_bids_project_developer'
            );
        });

        // Validasi nilai bid
        DB::statement("
            ALTER TABLE bids
            ADD CONSTRAINT chk_bids_nilai
            CHECK (
                harga > 0
                AND durasi > 0
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
