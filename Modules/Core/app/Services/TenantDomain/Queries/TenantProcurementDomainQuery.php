<?php

namespace Modules\Core\Services\TenantDomain\Queries;

use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\GoodsReceiptItem;
use Modules\Procurement\Models\ProcurementCategory;
use Modules\Procurement\Models\ProcurementItem;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionItem;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Models\RfqItem;
use Modules\Procurement\Models\RfqVendor;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\VendorBill;
use Modules\Procurement\Models\VendorBillItem;

final class TenantProcurementDomainQuery implements TenantDomainQueryInterface
{
    public function relations(): array
    {
        return [
            'vendors' => new TenantDomainRelationDefinition('vendors', Vendor::class),
            'procurementCategories' => new TenantDomainRelationDefinition('procurementCategories', ProcurementCategory::class),
            'procurementItems' => new TenantDomainRelationDefinition('procurementItems', ProcurementItem::class),
            'purchaseRequisitions' => new TenantDomainRelationDefinition('purchaseRequisitions', PurchaseRequisition::class),
            'purchaseRequisitionItems' => new TenantDomainRelationDefinition('purchaseRequisitionItems', PurchaseRequisitionItem::class),
            'requestForQuotations' => new TenantDomainRelationDefinition('requestForQuotations', RequestForQuotation::class),
            'rfqItems' => new TenantDomainRelationDefinition('rfqItems', RfqItem::class),
            'rfqVendors' => new TenantDomainRelationDefinition('rfqVendors', RfqVendor::class),
            'purchaseOrders' => new TenantDomainRelationDefinition('purchaseOrders', PurchaseOrder::class),
            'purchaseOrderItems' => new TenantDomainRelationDefinition('purchaseOrderItems', PurchaseOrderItem::class),
            'goodsReceipts' => new TenantDomainRelationDefinition('goodsReceipts', GoodsReceipt::class),
            'goodsReceiptItems' => new TenantDomainRelationDefinition('goodsReceiptItems', GoodsReceiptItem::class),
            'vendorBills' => new TenantDomainRelationDefinition('vendorBills', VendorBill::class),
            'vendorBillItems' => new TenantDomainRelationDefinition('vendorBillItems', VendorBillItem::class),
        ];
    }
}
