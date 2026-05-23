<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table): void {
                if (! Schema::hasColumn('rooms', 'is_rentable')) {
                    $table->boolean('is_rentable')->default(false)->after('status');
                }
                if (! Schema::hasColumn('rooms', 'rental_rate_hourly')) {
                    $table->decimal('rental_rate_hourly', 18, 2)->nullable()->after('is_rentable');
                }
                if (! Schema::hasColumn('rooms', 'rental_rate_daily')) {
                    $table->decimal('rental_rate_daily', 18, 2)->nullable()->after('rental_rate_hourly');
                }
                if (! Schema::hasColumn('rooms', 'is_bookable')) {
                    $table->boolean('is_bookable')->default(true)->after('rental_rate_daily');
                }
            });
        }

        if (Schema::hasTable('room_bookings')) {
            Schema::table('room_bookings', function (Blueprint $table): void {
                foreach ([
                    'room_id' => fn (Blueprint $t) => $t->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete(),
                    'booking_status' => fn (Blueprint $t) => $t->string('booking_status')->nullable(),
                    'start_at' => fn (Blueprint $t) => $t->timestamp('start_at')->nullable(),
                    'end_at' => fn (Blueprint $t) => $t->timestamp('end_at')->nullable(),
                    'requester_user_id' => fn (Blueprint $t) => $t->foreignId('requester_user_id')->nullable()->constrained('users')->nullOnDelete(),
                ] as $col => $adder) {
                    if (! Schema::hasColumn('room_bookings', $col)) {
                        $adder($table);
                    }
                }
            });
        }

        Schema::create('facility_rentals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('rental_type')->default('external');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rental_deposits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('facility_rental_id')->constrained('facility_rentals')->cascadeOnDelete();
            $table->decimal('amount', 18, 2);
            $table->decimal('forfeited_amount', 18, 2)->default(0);
            $table->string('status')->default('held');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_deposits');
        Schema::dropIfExists('facility_rentals');
    }
};
