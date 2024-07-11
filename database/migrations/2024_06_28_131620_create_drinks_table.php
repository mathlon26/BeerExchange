<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drinks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo');
            $table->decimal('market_price', 8, 2);
            $table->decimal('bottom_price', 8, 2);
            $table->decimal('upper_price', 8, 2);
            $table->decimal('cost_price', 8, 2);
            $table->boolean('in_discount');
            $table->boolean('pumping');
            $table->boolean('dumping');
            $table->boolean('allow_discount');
            $table->boolean('allow_autocrash');
            $table->boolean('allow_manualcrash');
            $table->integer('amount_sold');
            $table->json('transactions')->default('{}');
            $table->json('price_history')->default('{"0":0, "1":0, "2":0, "3":0, "4":0, "5":0, "6":0, "7":0, "8":0, "9":0, "10":0, "11":0, "12":0, "13":0, "14":0, "15":0, "16":0, "17":0, "18":0, "19":0, "20":0}');
            $table->timestamps();
            $table->foreignId('market_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drinks');
    }
};
