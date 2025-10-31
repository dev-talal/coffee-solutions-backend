<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    protected function bumpHomeVersion()
    {
        $version = (int) Cache::get('home_data_version', 1);
        Cache::forever('home_data_version', $version + 1);
    }

    public function saved(Product $product)
    {
        $this->bumpHomeVersion();
        // Optional: remove guest cache immediately
        Cache::forget('home_data_guest');

        Log::info('ProductObserver: saved product ' . $product->id);
    }

    public function deleted(Product $product)
    {
        $this->bumpHomeVersion();
        Cache::forget('home_data_guest');

        Log::info('ProductObserver: deleted product ' . $product->id);
    }
}
