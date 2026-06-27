<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Services\GoodsReceiptDocumentService;
use Symfony\Component\HttpFoundation\Response;

class GoodsReceiptPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(GoodsReceipt $goodsReceipt, GoodsReceiptDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $goodsReceipt,
            'procurement::pdf.goods-receipt',
            $service->assemble($goodsReceipt),
            $service->filename($goodsReceipt),
        );
    }
}
