<p>{{ $purchase->item->seller->name ?? '出品者' }} 様</p>

<p>購入者が取引を完了しました。</p>

<ul>
  <li>商品：{{ $purchase->item->name }}</li>
  <li>購入者：{{ $purchase->buyer->name ?? $purchase->buyer->email }}</li>
</ul>

<p>取引チャット：{{ route('transactions.show', $purchase) }}</p>