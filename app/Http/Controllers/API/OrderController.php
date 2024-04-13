<?php

namespace App\Http\Controllers\API;

use App\Traits\APIResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use APIResponse;

    public function addOrder(Request $request)
    {
        $rules = [
            'total' => 'required',
            'credit_card' => 'required',
        ];

        $messages = [
            'total.required' => 'Total is required',
            'credit_card.required' => 'Credit card number is required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->createAPIResponse(false, null, null, $validator->errors());
        }

        $invoiceId = Invoice::create([
            'user_id' => $request->user()->id,
            'date' => date('Y-m-d'),
            'total' => $request->total,
            'credit_card' => $request->credit_card
        ])->id;

        foreach($request->songs as $song)
        {
            Order::create([
                'song_id' => $song,
                'invoice_id' => $invoiceId
            ]);
        }

        return $this->createAPIResponse(true, null, 'Your order was done successfully');
    }
}
