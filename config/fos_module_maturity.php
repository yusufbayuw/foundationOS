<?php

/**
 * Module maturity tiers for FoundationOS.
 *
 * GA modules are production-ready (domain services, tests, importers).
 * Experimental modules are scaffold CRUD — not covered by the full test matrix.
 *
 * @see ANALISIS_REKOMENDASI.md §2 and §10 Gelombang 4
 */
return [

    'ga' => [
        'Core',
        'Global',
        'School',
        'Campus',
        'Finance',
        'Enrollment',
        'Employee',
        'Library',
        'Procurement',
        'Monitoring',
        'Workflow',
        'Inventory',
        'Risk',
        'Donation',
        'Sales',
        'Transport',
        'Property',
        'Training',
        'Marketplace',
        'Cms',
        'Legal',
        'Asset',
        'Helpdesk',
        'Facility',
        'EOffice',
        'Dms',
        'ItOps',
        'Boarding',
        'Cafeteria',
    ],

    /**
     * Modules approaching GA (domain services + factories + importers in progress).
     */
    'maturing' => [],

    'experimental' => [
        'PhysicalSecurity',
        'Counseling',
        'Clinic',
        'Event',
        'MerchOrder',
        'Alumni',
        'InternalAudit',
        'IsoCompliance',
        'EducationQa',
        'Exam',
        'KpiEnterprise',
        'Capacity',
        'Ai',
        'Messaging',
        'Printing',
        'Consulting',
    ],

    /**
     * Definition of Done for promoting a module from experimental → GA.
     */
    'definition_of_done' => [
        'BelongsToTenant on all operational models',
        'Factory + feature tests for primary flows',
        'Filament forms use TenantField / organizationSelect (no numeric organization_id)',
        'Domain service for non-trivial business rules',
        'Importer extends BaseModelImporter',
        'Shield permissions regenerated for new resources',
    ],

];
