<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\ItemPriceHistory;
use Illuminate\Support\Facades\Auth;

class ItemObserver
{
    public function updated(Item $item)
    {
        // Cek perubahan pada: Stok, Harga Beli, Harga Jual, ATAU Market
        if ($item->isDirty(['stock', 'buy_price', 'sell_price', 'market'])) {
            
            $reason = request()->input('history_reason', 'Edit Manual / Koreksi Data');

            ItemPriceHistory::create([
                'item_id' => $item->id,
                'user_id' => Auth::id() ?? 1,
                
                // Rekam Harga
                'buy_price' => $item->buy_price,
                'sell_price' => $item->sell_price,
                
                // Rekam Stok
                'old_stock' => $item->getOriginal('stock'),
                'new_stock' => $item->stock,

                // Rekam Market (Supplier) - TAMBAHAN BARU
                'old_market' => $item->getOriginal('market'),
                'new_market' => $item->market,

                'reason' => $reason,
            ]);
        }
    }
}