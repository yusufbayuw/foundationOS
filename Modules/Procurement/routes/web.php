<?php

use Illuminate\Support\Facades\Route;
use Modules\Procurement\Http\Controllers\GoodsReceiptPdfController;
use Modules\Procurement\Http\Controllers\ProcurementController;
use Modules\Procurement\Http\Controllers\PurchaseOrderPdfController;
use Modules\Procurement\Http\Controllers\PurchaseRequisitionPdfController;
use Modules\Procurement\Http\Controllers\RequestForQuotationPdfController;
use Modules\Procurement\Http\Controllers\VendorBillPdfController;

Route::middleware(['auth', 'verified', 'throttle:documents'])->group(function () {
    Route::resource('procurements', ProcurementController::class)->names('procurement');

    Route::get('/procurement/purchase-requisitions/{purchaseRequisition}/pdf', PurchaseRequisitionPdfController::class)
        ->name('procurement.purchase-requisitions.pdf');
    Route::get('/procurement/purchase-orders/{purchaseOrder}/pdf', PurchaseOrderPdfController::class)
        ->name('procurement.purchase-orders.pdf');
    Route::get('/procurement/goods-receipts/{goodsReceipt}/pdf', GoodsReceiptPdfController::class)
        ->name('procurement.goods-receipts.pdf');
    Route::get('/procurement/vendor-bills/{vendorBill}/pdf', VendorBillPdfController::class)
        ->name('procurement.vendor-bills.pdf');
    Route::get('/procurement/rfqs/{requestForQuotation}/pdf', RequestForQuotationPdfController::class)
        ->name('procurement.rfqs.pdf');
});
