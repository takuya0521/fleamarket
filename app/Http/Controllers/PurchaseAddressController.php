<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use Illuminate\Http\Request;

class PurchaseAddressController extends Controller
{
    public function edit(Request $request, Item $item)
    {
        $key = "purchase_address.{$item->id}";
        $addr = session($key, []);

        $shipping_postal_code = $addr['shipping_postal_code'] ?? $request->user()->postal_code;
        $shipping_address     = $addr['shipping_address'] ?? $request->user()->address;
        $shipping_building    = $addr['shipping_building'] ?? $request->user()->building;

        return view('purchase.address', compact(
            'item',
            'shipping_postal_code',
            'shipping_address',
            'shipping_building'
        ));
    }

    public function update(AddressRequest $request, Item $item)
    {
        $key = "purchase_address.{$item->id}";

        // AddressRequest 側で shipping_* に寄せ済み
        $postal   = $request->input('shipping_postal_code');
        $address  = $request->input('shipping_address');
        $building = $request->input('shipping_building');

        session([
            $key => [
                'shipping_postal_code' => $postal,
                'shipping_address'     => $address,
                'shipping_building'    => $building,
            ],
        ]);

        // ✅ usersテーブルも更新（PurchaseTest がここを見てる）
        $user = $request->user();
        $user->postal_code = $postal;
        $user->address     = $address;
        $user->building    = $building;
        $user->save();

        return redirect()->route('purchase.show', $item);
    }
}
