
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();

            $table->string('provider')->default('smtp');
            $table->string('host');
            $table->unsignedSmallInteger('port')->default(587);
            $table->string('encryption')->default('tls');

            $table->string('username');
            $table->text('password')->nullable();

            $table->string('from_address');
            $table->string('from_name');

            $table->boolean('is_enabled')->default(true);

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};
