<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prestashop, DateTime, DateTimeZone, Response, DB;
use Protechstudio\PrestashopWebService\PrestashopWebService;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Carrier;




class PrestashopController extends Controller
{
    //

    public function __invoke(Request $request)
    {

    }

    public function get_products(){

        set_time_limit(0);
        
        try {
            $web_service = new PrestaShopWebservice(
                "https://dlds.cl",
                "2KUN9AE821WGLVL81YT9K1YWC613Y19P",
                false
            );

            $opt = [
                'resource' => 'products',
                'display' => 'full',
                'sort' => '[id_DESC]',
                'limit' => '0, 1000'
            ];

            $xml = $web_service->get($opt);
            $resources = $xml->products->children(); 
            
            $products = [];
            
            foreach($resources->product as $product){

                echo "<br><br>-------------------------------<br><br>";
                echo "Product ID: ".$product->id."<br>";
                // echo "Customer name: ".$customer->firstname."<br>";
                // echo "Current lastname: ".$customer->lastname."<br>";
                // echo "Current email: ".$customer->email."<br>";

                $p = array(
                    'title' => $product->name->language[0],
                    'code' => $product->reference[0],
                    'description_short' => $product->description_short->language[0],
                    'description_large' => $product->description->language[0],
                    'price' => $product->price,
                    'images' => 'https://dlds.cl/api/'.$product->id."/".$product->images->id,
                    'status' => $product->state,
                    'mpn' => null,
                    'prestashop_id' => $product->id

                );

                array_push($products, $p);
            }

            echo "<br><br>------------------------------------------------<br><br>";
            echo "Insertando en base de datos<br>";
          
             echo "Insertados<br><br>";
             Product::insert($products);
            
            
            //  echo "<br><br>Lista------------------------------------------------<br><br>";
            
            //  dd(Product::all());



        } catch (\Throwable $th) {
            Log::debug($th);
            throw $th;

        }

    }



    public function get_orders(){


        DB::table('orders')->truncate();
        DB::table('products')->truncate();


        try {
            $web_service = new PrestaShopWebservice(
                "https://dlds.cl",
                "2KUN9AE821WGLVL81YT9K1YWC613Y19P",
                false
            );

            $opt = [
                'resource' => 'orders',
                'display' => 'full',
                'sort' => '[id_DESC]',
                'filter[current_state]' => '2,3'
            ];

            $xml = $web_service->get($opt);
            $resources = $xml->orders->children(); 
            
            $orders = [];
            $products = [];

            foreach($resources->order as $order){

                echo "<br><br>-------------------------------<br><br>";
                echo "Order ID: ".$order->id."<br>";
                echo "Order reference: ".$order->reference."<br>";
                echo "Current state: ".$order->current_state."<br>";
                echo "Date add: ".$order->date_add."<br>";
                echo "Customer ID: ".$order->id_customer."<br>";
                echo "Carrier ID: ".$order->id_carrier."<br>";
                echo "Date Add: ".$order->date_add."<br>";
                echo "Total: ".$order->total_paid."<br>";
                echo "Productos: <br><br>";

                $o = array(
                    'code' => $order->reference,
                    'prestashop_order_id' => $order->id,
                    'total' => $order->total_paid,
                    'status' => $order->current_state,
                    'user_id' => 1,
                    'customer_id'=> $order->id_customer,
                    'date_add'=> $order->date_add,
                    'carrier_id'=> $order->id_carrier
                );

                array_push($orders, $o);


                foreach($order->associations->order_rows->order_row as $product){

                    //secho "Product name: ".$product->product_name."  -  SKU: ".$product->product_reference."<br>";
                   
                    $p = array(
                        'title' => $product->product_name,
                        'code' => $product->product_reference,
                        'order_code' => $order->reference,
                        'quantity' => $product->product_quantity,
                        'price' => $product->product_price,
                        'price_with_taxes' => $product->unit_price_tax_incl,
                        'images' => null,
                        'status' => 1, 
                        'mpn' => null,
                        'prestashop_id' => $product->id
    
                    );
    
                    array_push($products, $p);
                    
                }
            }

            $opt = [
                'resource' => 'orders',
                'display' => 'full',
                'sort' => '[id_DESC]',
                'filter[current_state]' => '16,17'
            ];

            $xml = $web_service->get($opt);
            $resources = $xml->orders->children(); 
            
            foreach($resources->order as $order){

                echo "<br><br>-------------------------------<br><br>";
                echo "Order ID: ".$order->id."<br>";
                echo "Order reference: ".$order->reference."<br>";
                echo "Current state: ".$order->current_state."<br>";
                echo "Date add: ".$order->date_add."<br>";
                echo "Customer ID: ".$order->id_customer."<br>";
                echo "Carrier ID: ".$order->id_carrier."<br>";
                echo "Date Add: ".$order->date_add."<br>";
                echo "Total: ".$order->total_paid."<br>";
                echo "Productos: <br><br>";

                $o = array(
                    'code' => $order->reference,
                    'prestashop_order_id' => $order->id,
                    'total' => $order->total_paid,
                    'status' => $order->current_state,
                    'user_id' => 1,
                    'customer_id'=> $order->id_customer,
                    'date_add'=> $order->date_add,
                    'carrier_id'=> $order->id_carrier
                );

                array_push($orders, $o);


                foreach($order->associations->order_rows->order_row as $product){

                    //secho "Product name: ".$product->product_name."  -  SKU: ".$product->product_reference."<br>";

                   
                    $p = array(
                        'title' => $product->product_name,
                        'code' => $product->product_reference,
                        'order_code' => $order->reference,
                        'quantity' => $product->product_quantity,
                        'price' => $product->product_price,
                        'price_with_taxes' => $product->unit_price_tax_incl,
                        'images' => null,
                        'status' => 1, 
                        'mpn' => null,
                        'prestashop_id' => $product->product_id
    
                    );

                    array_push($products, $p);
                    
                }
            }

            Order::insert($orders);
            Product::insert($products);


        } catch (\Throwable $th) {
            Log::debug($th);
            throw $th;

        }

    }

    public function get_products_images(){

        set_time_limit(0);
        $products = Product::all();

        
        try {
            $web_service = new PrestaShopWebservice(
                "https://dlds.cl",
                "2KUN9AE821WGLVL81YT9K1YWC613Y19P",
                false
            );


            foreach($products as $product){
                    
                $opt = [
                    'resource' => 'products',
                    'display' => 'full',
                    'limit' => 1,
                    'filter[id]' => $product->prestashop_id
                ];
                

                $xml = $web_service->get($opt);
                $resources = $xml->products->children();
                

                $product->images = 'https://2KUN9AE821WGLVL81YT9K1YWC613Y19P:2KUN9AE821WGLVL81YT9K1YWC613Y19P@dlds.cl/api/images/products/'.$product->prestashop_id."/".$resources->product->id_default_image;

                echo  'https://2KUN9AE821WGLVL81YT9K1YWC613Y19P:2KUN9AE821WGLVL81YT9K1YWC613Y19P@dlds.cl/api/images/products/'.$product->id."/".$resources->product->id_default_image.'<br><br>';

                $product->save();
            }


        } catch (\Throwable $th) {
            Log::debug($th);
            throw $th;

        }

    }

    public function get_customers(){

        DB::table('customers')->truncate();
        
        try {
            $web_service = new PrestaShopWebservice(
                "https://dlds.cl",
                "2KUN9AE821WGLVL81YT9K1YWC613Y19P",
                false
            );

            $opt = [
                'resource' => 'customers',
                'display' => '[id, firstname, lastname, email]',
                'sort' => '[id_DESC]'
            ];

            $xml = $web_service->get($opt);
            $resources = $xml->customers->children(); 
            
            $customers = [];

            foreach($resources->customer as $customer){

                // echo "<br><br>-------------------------------<br><br>";
                // echo "Customer ID: ".$customer->id."<br>";
                // echo "Customer name: ".$customer->firstname."<br>";
                // echo "Current lastname: ".$customer->lastname."<br>";
                // echo "Current email: ".$customer->email."<br>";
                

                $c = array(
                    'name' => $customer->firstname." ".$customer->lastname,
                    'email' => $customer->email,
                    'prestashop_id' => $customer->id
                );

                array_push($customers, $c);
            }

            echo "<br><br>------------------------------------------------<br><br>";
            echo "Insertando en base de datos<br>";
            echo "Insertados<br><br>";
            Customer::insert($customers);
            echo "<br><br>Lista------------------------------------------------<br><br>";
            
            dd(Customer::all());



        } catch (\Throwable $th) {
            Log::debug($th);
            throw $th;

        }

    }

    public function get_carriers(){

        try {
            $web_service = new PrestaShopWebservice(
                "https://dlds.cl",
                "2KUN9AE821WGLVL81YT9K1YWC613Y19P",
                false
            );

            $opt = [
                'resource' => 'carriers',
                'display' => 'full',
                'sort' => '[id_DESC]',
                'filter[active]' => '1'

            ];

            $xml = $web_service->get($opt);
            $resources = $xml->carriers->children(); 
            
            $carriers = [];

            foreach($resources->carrier as $carrier){

                echo "<br><br>-------------------------------<br><br>";
                echo "carrier ID: ".$carrier->id."<br>";
                echo "carrier name: ".$carrier->name."<br>";
                echo "Delay: ".$carrier->delay."<br>";
                

                $c = array(
                    'name' => $carrier->name,
                    'delay' => $carrier->delay->language[0],
                    'prestashop_id' => $carrier->id
                );

                array_push($carriers, $c);
            }

            echo "<br><br>------------------------------------------------<br><br>";
            echo "Insertando en base de datos<br>";
            echo "Insertados<br><br>";
            
            Carrier::insert($carriers);
            echo "<br><br>Lista------------------------------------------------<br><br>";
            
            dd(Carrier::all());



        } catch (\Throwable $th) {
            Log::debug($th);
            throw $th;

        }

    }

    public function get_order_list(){


        $orders = Order::join('carriers', 'orders.carrier_id', '=', 'carriers.prestashop_id')
                        ->where('status', '>', 0)
                        ->paginate(15);

        return response()->json($orders);

    }

    public function get_orders_picked_list(){


        $orders = Order::join('carriers', 'orders.carrier_id', '=', 'carriers.prestashop_id')
                        ->where('status', 0)
                        ->select('orders.*', 'carriers.name')
                        ->paginate(15);

        return response()->json($orders);

    }


    public function pagination(){


        // $orders = Order::limit($params['limit'])->offset($params['page']*$params['limit'])->get();


    }

    public function get_products_by_order($reference){

        $order = Order::where('code', $reference)->first();
        $products = Product::where('order_code', $reference)
                            ->orderBy('code', 'asc')
                            ->get();
        $SM = [];
        $noSM = [];

        foreach($products as $product){

            if(substr($product->code, 0, 2) == 'sm' || substr($product->code, 0, 2) == 'SM'){

                array_push($SM, $product);

            }else{

                array_push($noSM, $product);


            }

        }

        

        $data = array(

            "order" => $order,
            "products" => array_merge($SM, $noSM)
        );

        return response()->json($data);

    }

    public function picking_products_by_order(Request $data){

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

        var_dump($data);


        // var_dump($products);

        // foreach($products->products as $product){

        //     echo $product;

        // }

        // return response()->json($products);

    }

    public function product_data($order_code, $product_sku){

        
        $order = Order::where('code', $order_code)->first();

        $product = Product::where('order_code', $order_code)
                            ->where('code', $product_sku)
                            ->first();

        $data = array(

            "order" => $order,
            "product_data" => $product
        );

        return response()->json($data);
    }


}
