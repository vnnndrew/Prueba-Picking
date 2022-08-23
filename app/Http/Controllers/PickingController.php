<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prestashop, DateTime, DateTimeZone, Response, DB;
use Illuminate\Support\Facades\Auth;
use Protechstudio\PrestashopWebService\PrestashopWebService;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Picking;


class PickingController extends Controller
{
    

    public function save_picking(Request $data){

        $pickingData = $data->all();

        $picking = new Picking;

        try {
            

            $picking->order_id = $pickingData['order_id'];
            $picking->product_id = $pickingData['product_id'];
            $picking->quantity = $pickingData['quantity'];
            $picking->status = 1;

            if(Auth::id()){

                $picking->picker_id = Auth::id();

            }else{

                $picking->picker_id = 0;

            }

            $product = Product::find($pickingData['product_id']);


            if($product->quantity - $pickingData['quantity'] == 0){

               $product->status = 0;
               $product->quantity = 0;

            }else{

                if($product->quantity - $pickingData['quantity'] < 0){

                    $product->quantity = 0;
                    $product->status = 0;
                    
    
                 }else{
    
                    $product->quantity = $product->quantity - $pickingData['quantity'];
                 }

            }
            
            $product->save();
            $response = $picking->save();
           
            $this->validate_order($pickingData['order_id']);

            return response()->json($response);


        } catch (\Throwable $th) {
            throw $th;
            return response()->json("Error");

        }

    }

    public function get_picking($reference, $sku){


        $product = Product::where('order_code', $reference)
                          ->where('code', $sku)
                          ->first();
        $picking = DB::table('picking')
            ->where('order_id', $reference)
            ->where('product_id', $product->id)
            ->leftJoin('products as product_data', 'picking.product_id', '=', 'product_data.id')
            ->select('picking.*', 'product_data.title as product_title', 'product_data.code as product_code')
            ->get();

        return response()->json($picking);

    }


    public function validate_order($reference){

        $order = Order::where('code', $reference)->first();

        $products = Product::where('order_code', $reference)
                            ->where('status', 1)
                            ->where('quantity', '>', '0')
                            ->get();

        if(!sizeof($products)){

            $order->status = 0;
            $order->save();

        }
    }

}
