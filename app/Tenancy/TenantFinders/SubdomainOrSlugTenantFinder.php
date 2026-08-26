<?php

namespace App\Tenancy\TenantFinders;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class SubdomainOrSlugTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        $host = $request->getHost();
        
        // Extract subdomain (assuming primary domain is something like domain.com)
        $domainParts = explode('.', $host);
        
        // If host has at least 3 parts (e.g. shop.domain.com) and is not www
        if (count($domainParts) >= 3 && $domainParts[0] !== 'www') {
            $subdomain = $domainParts[0];
            $tenant = Tenant::where('subdomain', $subdomain)->first();
            
            if ($tenant) {
                return $tenant;
            }
        }
        
        // Fallback: check path prefix for /@shop_name
        $path = $request->path();
        if (preg_match('/^@([a-zA-Z0-9\-]+)/', $path, $matches)) {
            $slug = $matches[1];
            return Tenant::where('subdomain', $slug)->first();
        }

        return null;
    }
}
