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
        Schema::table('pages',function(Blueprint $table){
            $table->integer('status')->default(1)->after('content');
            $table->string('meta_title')->nullable()->after('status');
            $table->string('meta_canonical_url')->nullable()->after('meta_title');
            $table->string('meta_description')->nullable()->after('meta_canonical_url');
            $table->string('meta_keyword')->nullable()->after('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users',function(Blueprint $table){
            $table->dropColumn('status');
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_canonical_url');
            $table->dropColumn('meta_description');
            $table->dropColumn('meta_keyword');
        });
    }
};
