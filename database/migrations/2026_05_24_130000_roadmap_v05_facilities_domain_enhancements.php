<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->enhanceAssets();
        $this->enhanceLegal();
        $this->enhanceDms();
        $this->enhanceHelpdesk();
        $this->enhanceFacility();
        $this->enhanceEOffice();
        $this->enhanceItOps();
        $this->enhanceTransport();
        $this->enhancePhysicalSecurity();
        $this->enhanceCafeteria();
    }

    protected function enhanceAssets(): void
    {
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table) {
                if (! Schema::hasColumn('assets', 'asset_category_id')) {
                    $table->foreignId('asset_category_id')->nullable()->after('organization_id')->constrained('asset_categories')->nullOnDelete();
                }
                if (! Schema::hasColumn('assets', 'qr_code')) {
                    $table->string('qr_code')->nullable()->unique();
                }
                if (! Schema::hasColumn('assets', 'condition')) {
                    $table->string('condition')->default('good')->after('status');
                }
                if (! Schema::hasColumn('assets', 'acquisition_value')) {
                    $table->decimal('acquisition_value', 18, 2)->nullable();
                }
                if (! Schema::hasColumn('assets', 'useful_life_months')) {
                    $table->unsignedSmallInteger('useful_life_months')->nullable();
                }
                if (! Schema::hasColumn('assets', 'depreciation_method')) {
                    $table->string('depreciation_method')->nullable();
                }
                if (! Schema::hasColumn('assets', 'maintenance_due_at')) {
                    $table->timestamp('maintenance_due_at')->nullable();
                }
                if (! Schema::hasColumn('assets', 'photos')) {
                    $table->json('photos')->nullable();
                }
            });
        }

        if (Schema::hasTable('asset_movements') && ! Schema::hasColumn('asset_movements', 'asset_id')) {
            Schema::table('asset_movements', function (Blueprint $table) {
                $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            });
        }

        if (Schema::hasTable('asset_maintenances')) {
            Schema::table('asset_maintenances', function (Blueprint $table) {
                if (! Schema::hasColumn('asset_maintenances', 'asset_id')) {
                    $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
                }
                if (! Schema::hasColumn('asset_maintenances', 'scheduled_at')) {
                    $table->timestamp('scheduled_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('asset_insurances')) {
            Schema::table('asset_insurances', function (Blueprint $table) {
                if (! Schema::hasColumn('asset_insurances', 'asset_id')) {
                    $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
                }
                if (! Schema::hasColumn('asset_insurances', 'expires_at')) {
                    $table->date('expires_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('asset_depreciations') && ! Schema::hasColumn('asset_depreciations', 'asset_id')) {
            Schema::table('asset_depreciations', function (Blueprint $table) {
                $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
                $table->decimal('amount', 18, 2)->nullable();
                $table->date('period_date')->nullable();
            });
        }

        if (Schema::hasTable('asset_loans') && ! Schema::hasColumn('asset_loans', 'asset_id')) {
            Schema::table('asset_loans', function (Blueprint $table) {
                $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
                $table->foreignId('borrower_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('loaned_at')->nullable();
                $table->timestamp('returned_at')->nullable();
            });
        }
    }

    protected function enhanceLegal(): void
    {
        if (Schema::hasTable('legal_documents')) {
            Schema::table('legal_documents', function (Blueprint $table) {
                if (! Schema::hasColumn('legal_documents', 'document_type')) {
                    $table->string('document_type')->nullable();
                }
                if (! Schema::hasColumn('legal_documents', 'effective_date')) {
                    $table->date('effective_date')->nullable();
                }
                if (! Schema::hasColumn('legal_documents', 'expires_at')) {
                    $table->date('expires_at')->nullable();
                }
                if (! Schema::hasColumn('legal_documents', 'file_path')) {
                    $table->string('file_path')->nullable();
                }
            });
        }

        if (Schema::hasTable('contract_parties')) {
            Schema::table('contract_parties', function (Blueprint $table) {
                if (! Schema::hasColumn('contract_parties', 'contract_id')) {
                    $table->foreignId('contract_id')->nullable()->constrained('contracts')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('contract_parties', 'party_name')) {
                    $table->string('party_name')->nullable();
                }
                if (! Schema::hasColumn('contract_parties', 'party_type')) {
                    $table->string('party_type')->nullable();
                }
            });
        }

        if (Schema::hasTable('contract_attachments')) {
            Schema::table('contract_attachments', function (Blueprint $table) {
                if (! Schema::hasColumn('contract_attachments', 'contract_id')) {
                    $table->foreignId('contract_id')->nullable()->constrained('contracts')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('contract_attachments', 'file_path')) {
                    $table->string('file_path')->nullable();
                }
            });
        }
    }

    protected function enhanceDms(): void
    {
        if (Schema::hasTable('document_folders') && ! Schema::hasColumn('document_folders', 'parent_folder_id')) {
            Schema::table('document_folders', function (Blueprint $table) {
                $table->foreignId('parent_folder_id')->nullable()->constrained('document_folders')->nullOnDelete();
            });
        }

        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (! Schema::hasColumn('documents', 'document_folder_id')) {
                    $table->foreignId('document_folder_id')->nullable()->constrained('document_folders')->nullOnDelete();
                }
                if (! Schema::hasColumn('documents', 'searchable_content')) {
                    $table->text('searchable_content')->nullable();
                }
                if (! Schema::hasColumn('documents', 'retention_policy')) {
                    $table->string('retention_policy')->nullable();
                }
                if (! Schema::hasColumn('documents', 'retention_expires_at')) {
                    $table->date('retention_expires_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('document_versions')) {
            Schema::table('document_versions', function (Blueprint $table) {
                if (! Schema::hasColumn('document_versions', 'document_id')) {
                    $table->foreignId('document_id')->nullable()->constrained('documents')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('document_versions', 'version_number')) {
                    $table->unsignedInteger('version_number')->default(1);
                }
                if (! Schema::hasColumn('document_versions', 'file_path')) {
                    $table->string('file_path')->nullable();
                }
            });
        }

        if (Schema::hasTable('document_access_logs')) {
            Schema::table('document_access_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('document_access_logs', 'document_id')) {
                    $table->foreignId('document_id')->nullable()->constrained('documents')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('document_access_logs', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('document_access_logs', 'action')) {
                    $table->string('action')->nullable();
                }
            });
        }
    }

    protected function enhanceHelpdesk(): void
    {
        if (Schema::hasTable('ticket_categories')) {
            Schema::table('ticket_categories', function (Blueprint $table) {
                if (! Schema::hasColumn('ticket_categories', 'response_hours')) {
                    $table->unsignedSmallInteger('response_hours')->default(4);
                }
                if (! Schema::hasColumn('ticket_categories', 'resolution_hours')) {
                    $table->unsignedSmallInteger('resolution_hours')->default(24);
                }
                if (! Schema::hasColumn('ticket_categories', 'default_assignee_user_id')) {
                    $table->foreignId('default_assignee_user_id')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('tickets')) {
            Schema::table('tickets', function (Blueprint $table) {
                if (! Schema::hasColumn('tickets', 'ticket_category_id')) {
                    $table->foreignId('ticket_category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
                }
                if (! Schema::hasColumn('tickets', 'priority')) {
                    $table->string('priority')->default('normal');
                }
                if (! Schema::hasColumn('tickets', 'assigned_to_user_id')) {
                    $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('tickets', 'escalated_at')) {
                    $table->timestamp('escalated_at')->nullable();
                }
                if (! Schema::hasColumn('tickets', 'ai_suggested_category')) {
                    $table->string('ai_suggested_category')->nullable();
                }
                if (! Schema::hasColumn('tickets', 'closed_at')) {
                    $table->timestamp('closed_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('ticket_comments') && ! Schema::hasColumn('ticket_comments', 'ticket_id')) {
            Schema::table('ticket_comments', function (Blueprint $table) {
                $table->foreignId('ticket_id')->nullable()->constrained('tickets')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('body')->nullable();
            });
        }

        if (Schema::hasTable('ticket_satisfactions')) {
            Schema::table('ticket_satisfactions', function (Blueprint $table) {
                if (! Schema::hasColumn('ticket_satisfactions', 'ticket_id')) {
                    $table->foreignId('ticket_id')->nullable()->constrained('tickets')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('ticket_satisfactions', 'rating')) {
                    $table->unsignedTinyInteger('rating')->nullable();
                }
                if (! Schema::hasColumn('ticket_satisfactions', 'comment')) {
                    $table->text('comment')->nullable();
                }
            });
        }
    }

    protected function enhanceFacility(): void
    {
        if (Schema::hasTable('floors') && ! Schema::hasColumn('floors', 'building_id')) {
            Schema::table('floors', function (Blueprint $table) {
                $table->foreignId('building_id')->nullable()->constrained('buildings')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                if (! Schema::hasColumn('rooms', 'floor_id')) {
                    $table->foreignId('floor_id')->nullable()->constrained('floors')->nullOnDelete();
                }
                if (! Schema::hasColumn('rooms', 'capacity')) {
                    $table->unsignedSmallInteger('capacity')->nullable();
                }
                if (! Schema::hasColumn('rooms', 'room_type')) {
                    $table->string('room_type')->nullable();
                }
                if (! Schema::hasColumn('rooms', 'is_bookable')) {
                    $table->boolean('is_bookable')->default(true);
                }
                if (! Schema::hasColumn('rooms', 'is_rentable')) {
                    $table->boolean('is_rentable')->default(false);
                }
            });
        }

        if (Schema::hasTable('room_bookings')) {
            Schema::table('room_bookings', function (Blueprint $table) {
                if (! Schema::hasColumn('room_bookings', 'room_id')) {
                    $table->foreignId('room_id')->nullable()->constrained('rooms')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('room_bookings', 'start_at')) {
                    $table->timestamp('start_at')->nullable();
                }
                if (! Schema::hasColumn('room_bookings', 'end_at')) {
                    $table->timestamp('end_at')->nullable();
                }
                if (! Schema::hasColumn('room_bookings', 'purpose')) {
                    $table->string('purpose')->nullable();
                }
                if (! Schema::hasColumn('room_bookings', 'requester_user_id')) {
                    $table->foreignId('requester_user_id')->nullable()->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('room_bookings', 'booking_status')) {
                    $table->string('booking_status')->default('draft');
                }
            });

            Schema::table('room_bookings', function (Blueprint $table) {
                $table->index(['room_id', 'start_at', 'end_at']);
            });
        }

        if (Schema::hasTable('utility_readings')) {
            Schema::table('utility_readings', function (Blueprint $table) {
                if (! Schema::hasColumn('utility_readings', 'building_id')) {
                    $table->foreignId('building_id')->nullable()->constrained('buildings')->nullOnDelete();
                }
                if (! Schema::hasColumn('utility_readings', 'utility_type')) {
                    $table->string('utility_type')->nullable();
                }
                if (! Schema::hasColumn('utility_readings', 'period_month')) {
                    $table->date('period_month')->nullable();
                }
                if (! Schema::hasColumn('utility_readings', 'reading_value')) {
                    $table->decimal('reading_value', 18, 4)->nullable();
                }
                if (! Schema::hasColumn('utility_readings', 'emission_factor')) {
                    $table->decimal('emission_factor', 12, 6)->nullable();
                }
            });
        }
    }

    protected function enhanceEOffice(): void
    {
        if (! Schema::hasTable('letters')) {
            return;
        }

        Schema::table('letters', function (Blueprint $table) {
            if (! Schema::hasColumn('letters', 'letter_category_id')) {
                $table->foreignId('letter_category_id')->nullable()->constrained('letter_categories')->nullOnDelete();
            }
            if (! Schema::hasColumn('letters', 'direction')) {
                $table->string('direction')->nullable();
            }
            if (! Schema::hasColumn('letters', 'letter_number')) {
                $table->string('letter_number')->nullable();
            }
        });
    }

    protected function enhanceItOps(): void
    {
        if (! Schema::hasTable('software_licenses')) {
            return;
        }

        Schema::table('software_licenses', function (Blueprint $table) {
            if (! Schema::hasColumn('software_licenses', 'asset_id')) {
                $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            }
            if (! Schema::hasColumn('software_licenses', 'expires_at')) {
                $table->date('expires_at')->nullable();
            }
        });

        if (Schema::hasTable('user_accounts')) {
            Schema::table('user_accounts', function (Blueprint $table) {
                if (! Schema::hasColumn('user_accounts', 'account_type')) {
                    $table->string('account_type')->nullable();
                }
                if (! Schema::hasColumn('user_accounts', 'expires_at')) {
                    $table->date('expires_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('ip_address_records')) {
            Schema::table('ip_address_records', function (Blueprint $table) {
                if (! Schema::hasColumn('ip_address_records', 'expires_at')) {
                    $table->date('expires_at')->nullable();
                }
            });
        }
    }

    protected function enhanceTransport(): void
    {
        if (Schema::hasTable('vehicles') && ! Schema::hasColumn('vehicles', 'asset_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
                $table->string('plate_number')->nullable();
                $table->unsignedSmallInteger('capacity')->nullable();
            });
        }
    }

    protected function enhancePhysicalSecurity(): void
    {
        if (Schema::hasTable('visitor_logs') && ! Schema::hasColumn('visitor_logs', 'visitor_id')) {
            Schema::table('visitor_logs', function (Blueprint $table) {
                $table->foreignId('visitor_id')->nullable()->constrained('visitors')->cascadeOnDelete();
                $table->timestamp('checked_in_at')->nullable();
                $table->timestamp('checked_out_at')->nullable();
            });
        }
    }

    protected function enhanceCafeteria(): void
    {
        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                if (! Schema::hasColumn('menus', 'calories')) {
                    $table->unsignedSmallInteger('calories')->nullable();
                }
                if (! Schema::hasColumn('menus', 'protein_grams')) {
                    $table->decimal('protein_grams', 8, 2)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Irreversible enhancement migration for roadmap v0.5 rollout.
    }
};
