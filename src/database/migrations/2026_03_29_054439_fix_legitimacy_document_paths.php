<?php

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
        // Fix user ID 3: Update JPG path to correct PDF path
        DB::table('users')
            ->where('id', 3)
            ->where('legitimacy_document', 'legitimacy_documents/ioUNdVzApjJVORr8XCjzkWcoiuyS3pZoYaqT54JQ.jpg')
            ->update(['legitimacy_document' => 'legitimacy_documents/3tv93RAs0iLTaZJXG5y99agRUtsaBpbyjV8VBhsl.pdf']);

        // Fix user ID 24: Update PNG path to correct PDF path
        DB::table('users')
            ->where('id', 24)
            ->where('legitimacy_document', 'legitimacy_documents/KYESBF1BCXCkIiPCJJ5mHmGj7G2LeVt6pKUveb1o.png')
            ->update(['legitimacy_document' => 'legitimacy_documents/K8fy2ZpdT5BVIcW22sXEfgO2XRaFW1svZImjwJof.pdf']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
