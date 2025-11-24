<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class ItemObserver
{
    public function created(Item $item)
    {
        $reason = request()->input('history_reason', 'Input Barang Baru');

        History::create([
            'item_id' => $item->id,
            'user_id' => Auth::id() ?? 1,
            
            // Saat baru dibuat, harga lama = 0/null
            'old_buy_price' => 0,
            'new_buy_price' => $item->buy_price,
            
            'old_sell_price' => 0,
            'new_sell_price' => $item->sell_price,
            
            'old_stock' => 0, 
            'new_stock' => $item->stock,

            'old_market' => null,
            'new_market' => $item->market,

            'reason' => $reason,
        ]);
    }

    public function updated(Item $item)
    {
        // Cek jika ada perubahan penting
        if ($item->isDirty(['stock', 'buy_price', 'sell_price', 'market'])) {
            
            $reason = request()->input('history_reason', 'Edit Manual / Koreksi Data');

            History::create([
                'item_id' => $item->id,
                'user_id' => Auth::id() ?? 1,
                
                // Catat Harga Lama vs Baru
                'old_buy_price' => $item->getOriginal('buy_price'),
                'new_buy_price' => $item->buy_price,
                
                'old_sell_price' => $item->getOriginal('sell_price'),
                'new_sell_price' => $item->sell_price,
                
                // Catat Stok Lama vs Baru
                'old_stock' => $item->getOriginal('stock'),
                'new_stock' => $item->stock,
                
                // Catat Market Lama vs Baru
                'old_market' => $item->getOriginal('market'),
                'new_market' => $item->market,
                
                'reason' => $reason,
            ]);
        }
    }
}