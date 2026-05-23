<?php

namespace Modules\Cms\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\Site;
use Modules\Enrollment\Services\LeadInquiryService;

class PublicSiteController extends Controller
{
    public function __construct(
        private readonly LeadInquiryService $leadInquiryService,
    ) {}

    public function page(string $siteCode, string $slug): View
    {
        $site = Site::query()->where('code', $siteCode)->where('is_active', true)->firstOrFail();

        $page = Page::query()
            ->where('site_id', $site->getKey())
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with('blocks')
            ->firstOrFail();

        return view('cms::public.page', [
            'site' => $site,
            'page' => $page,
        ]);
    }

    public function sitemap(string $siteCode): Response
    {
        $site = Site::query()->where('code', $siteCode)->where('is_active', true)->firstOrFail();

        $pages = Page::query()
            ->where('site_id', $site->getKey())
            ->where('status', 'published')
            ->pluck('slug');

        $xml = view('cms::public.sitemap', ['pages' => $pages, 'site' => $site])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function contact(Request $request, string $siteCode): JsonResponse
    {
        $site = Site::query()->where('code', $siteCode)->where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string'],
        ]);

        $lead = $this->leadInquiryService->createFromInquiry(
            tenantId: (int) $site->tenant_id,
            fullName: $validated['full_name'],
            email: $validated['email'] ?? null,
            phone: $validated['phone'] ?? null,
            sourceCode: 'cms_contact',
            sourceDetail: 'site:'.$siteCode,
        );

        return response()->json(['lead_id' => $lead->getKey()], 201);
    }
}
