<?php

namespace Mesk\OrderBulkUpdate\Http\Controllers\Admin;

use Webkul\Admin\Imports\DataGridImport;
use Webkul\Core\Eloquent\Repository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderItemRepository;
use Webkul\Sales\Repositories\ShipmentItemRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Sales\Contracts\Shipment;
use Mesk\OrderBulkUpdate\Mail\ShipmentUpdateNotification;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Webkul\Admin\Traits\Mails;

class OrderBulkUpdateController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,Mails;
    
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * OrderRepository object
     *
     * @var use Webkul\Sales\Repositories\OrderRepository;
     *
     */
    protected $orderRepository;
	
	protected $customerRepository;
	
	 /**
     * OrderItemRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * ShipmentItemRepository object
     *
     * @var \Webkul\Sales\Repositories\ShipmentItemRepository
     */
    protected $shipmentItemRepository;

     
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   // ProductAttributeValueRepository $productAttributeValueRepository,ProductInventoryRepository $productInventoryRepository,AttributeRepository $attributeRepository
    public function __construct(OrderRepository $orderRepository,orderItemRepository $orderItemRepository,shipmentItemRepository $shipmentItemRepository, CustomerRepository $customerRepository,Shipment $shipment)
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;
		
        $this->orderItemRepository = $orderItemRepository;
		
        $this->customerRepository = $customerRepository;
		
        $this->shipmentItemRepository = $shipmentItemRepository;

        $this->shipment = $shipment;

    }
	function model()
    {
        return Shipment::class;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
		$this->setStartEndDate();
        return view($this->_config['view']);
    }
	
	private function getOrdersBetweenDate($start, $end)
    {
        return $this->orderRepository->scopeQuery(function ($query) use ($start, $end) {
			return $query->where('orders.created_at', '>=', $start)->where('orders.created_at', '<=', $end);
        })->get();
    }
	
	 public function setStartEndDate()
    {
        $this->startDate = request()->get('start')
            ? Carbon::createFromTimeString(request()->get('start') . " 00:00:01")
            : Carbon::createFromTimeString(Carbon::now()->subDays(30)->format('Y-m-d') . " 00:00:01");

        $this->endDate = request()->get('end')
            ? Carbon::createFromTimeString(request()->get('end') . " 23:59:59")
            : Carbon::now();

        if ($this->endDate > Carbon::now()) {
            $this->endDate = Carbon::now();
        }

        $this->lastStartDate = clone $this->startDate;
        $this->lastEndDate = clone $this->startDate;

        $this->lastStartDate->subDays($this->startDate->diffInDays($this->endDate));
    }
	
    /**
     * import the specified resource to storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function importOrders()
    {
        request()->validate ([
            'file_path' => 'required',
        ]);

        $valid_extension = ['csv', 'xls', 'xlsx'];

        $fileDir = 'imported-order-update/admin/files';
        $file = request()->file('file_path');
		
        if (in_array($file->getClientOriginalExtension(), $valid_extension)) {

            $uploadedFile = $file->storeAs($fileDir, uniqid().'.'.$file->getClientOriginalExtension());
			
            if ($uploadedFile) {
                $csvData = (new DataGridImport)->toArray($uploadedFile)[0];
                $errData=[]; $csvDataCount=count($csvData); $updateValue=[];$orderIdArr=[];
                for ($i = 0; $i < $csvDataCount; $i++) {
                    if(isset($csvData[$i]["order_id"]) && $csvData[$i]["order_id"]!='' && isset($csvData[$i]["shipping_method"]) && $csvData[$i]["shipping_method"] && isset($csvData[$i]["shipping_title"]) && $csvData[$i]["shipping_title"] && isset($csvData[$i]["source"]) && $csvData[$i]["source"]){
						$order = $this->orderRepository->find($csvData[$i]["order_id"]);
	
						if(!empty($order)){
						$shipment_exits=$this->shipment->where("order_id","=",$order->id)->count();	
 						if($shipment_exits == 0){
						\DB::statement('SET FOREIGN_KEY_CHECKS=0');
 						$shipment = $this->shipment->create([
							'order_id'            => isset($order->id) ? $order->id : '',
							'total_qty'           => 0,
							'carrier_title'       => isset($csvData[$i]["carrier_title"]) ? $csvData[$i]["carrier_title"] : NULL,
							'track_number'        => isset($csvData[$i]["track_number"]) ? $csvData[$i]["track_number"] : NULL,
							'customer_id'         => isset($order->customer_id) ? $order->customer_id : '',
							'customer_type'       => isset($order->customer_type) ? $order->customer_type : '',
							'order_address_id'    => isset($order->shipping_address) ? $order->shipping_address->id : '',
							'inventory_source_id' => isset($csvData[$i]["source"]) ? $csvData[$i]["source"] : NULL,
						]);
						$totalQty = 0;
						
						if(!empty($order->items)){
						$order_items=$order->items->toArray();
						
						foreach ($order_items as $item => $inventorySource) {
							$qty = $inventorySource["qty_ordered"];
							$itemId = $inventorySource["id"];
							$orderItem = $this->orderItemRepository->find($itemId);
 
							if(!empty($orderItem)){
							if (isset($orderItem->qty_to_ship) && $qty > $orderItem->qty_to_ship) {
								$qty = $orderItem->qty_to_ship;
							}

							$totalQty += $qty;

							$shipmentItem = $this->shipmentItemRepository->create([
							'shipment_id'   => $shipment->id,
							'order_item_id' => $orderItem->id,
							'name'          => $orderItem->name,
							'sku'           => $orderItem->getTypeInstance()->getOrderedItem($orderItem)->sku,
							'qty'           => $qty,
							'weight'        => $orderItem->weight * $qty,
							'price'         => $orderItem->price,
							'base_price'    => $orderItem->base_price,
							'total'         => $orderItem->price * $qty,
							'base_total'    => $orderItem->base_price * $qty,
							'product_id'    => $orderItem->product_id,
							'product_type'  => $orderItem->product_type,
							'additional'    => $orderItem->additional,
							]);

							if ($orderItem->getTypeInstance()->isComposite()) {
								foreach ($orderItem->children as $child){
									if (! $child->qty_ordered) {
										$finalQty = $qty;
									} else {
										$finalQty = ($child->qty_ordered / $orderItem->qty_ordered) * $qty;
									}
									
									$this->shipmentItemRepository->updateProductInventory([
									'shipment'  => $shipment,
									'product'   => $child->product,
									'qty'       => $finalQty,
									'vendor_id' => isset($data['vendor_id']) ? $data['vendor_id'] : 0,
									]);

									$this->orderItemRepository->update(['qty_shipped' => $child->qty_shipped + $finalQty], $child->id);
								}
							} else {
								$this->shipmentItemRepository->updateProductInventory([
									'shipment'  => $shipment,
									'product'   => $orderItem->product,
									'qty'       => $qty,
									'vendor_id' => isset($data['vendor_id']) ? $data['vendor_id'] : 0,
								]);
							}

							$this->orderItemRepository->update(['qty_shipped' => $orderItem->qty_shipped + $qty], $orderItem->id);
							}
						}
						}
						$shipment->update([
							'total_qty'             => $totalQty,
							'inventory_source_name' => isset($shipment->inventory_source) ? $shipment->inventory_source->name : 'Default',
						]);
						
						if (isset($orderState)) {
							$this->orderRepository->updateOrderStatus($order, $orderState);
						} else {
							$this->orderRepository->updateOrderStatus($order);
						}
						}
						}						
						
                        $orderIdArr[]=$csvData[$i]["order_id"];
                        $updateValue[$csvData[$i]["order_id"]]["shipping_method"]=$csvData[$i]["shipping_method"];
                        $updateValue[$csvData[$i]["order_id"]]["shipping_title"]=$csvData[$i]["shipping_title"];
                        $updateValue[$csvData[$i]["order_id"]]["shipping_description"]=$csvData[$i]["shipping_description"];
						
                    }else if(isset($csvData[$i]["order_id"]) && $csvData[$i]["order_id"]!=''){
                        $csvData[$i]["error"]="Empty Value at Row Number ".($i+1);
                        $errData[]=$csvData[$i];
                    }
                }
				
                if(count($errData)==0){
					if(isset($shipment_exits) && $shipment_exits == 0){
                    $customerData=$this->orderRepository->select(array("orders.id","orders.customer_id","orders.customer_type","customers.first_name","customers.last_name","customers.email","customers.phone"))
                    ->join('customers', 'customers.id', '=', 'orders.customer_id')
                    ->whereIn("orders.id",$orderIdArr)
                    ->get();
                     foreach($customerData as $va=>$key){
                        $this->orderRepository->where('id', $key->id) ->limit(1)->update($updateValue[$key->id]);
                        if($key->phone)
                            $this->sendSms($key->phone,"Your order shippment has been updated");
                        if($key->email){
						   $customer = $this->customerRepository->findOrFail($key->customer_id);
						   Mail::queue(new ShipmentUpdateNotification($customer));
                        }
                    }
					}
                    session()->flash('success', trans('orderbulkupdate::app.admin.bulk-upload.messages.imported'));
                    return redirect()->route('admin.orderbulkupdate.index');
					
                }
				session()->flash('error', trans('orderbulkupdate::app.admin.bulk-upload.messages.cant-able-to-import'));
                return view($this->_config['view'], compact('errData'));  
            }
        } else {
            session()->flash('error', trans('orderbulkupdate::app.admin.bulk-upload.messages.file-format-error'));
            return redirect()->route('admin.orderbulkupdate.index');
        }

    

        
    }
     
    /**
     * download the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadFile()
    {
		
		$this->setStartEndDate();
		 
		$path='';
		if(request()->get('start') && request()->get('end')){
			foreach (core()->getTimeInterval($this->startDate, $this->endDate) as $interval) {
				$orders = $this->getOrdersBetweenDate($interval['start'], $interval['end']);
			}
			$filename=time().'Order.xlsx';
			$path = public_path('storage/downloads/sample-files/bulkorderupdate.xlsx');
			$path_download = public_path('storage/downloads/sample-files/'.$filename);
 			$spreadsheet = IOFactory::load($path);
			
			$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
			$num = 2;
 			foreach ($orders as $key=>$order) { 
				$spreadsheet->getActiveSheet()->getCell('A'.$num)->setValue($order->id);
				$num++;
 			}
			$writer = new Xlsx($spreadsheet);
			$writer->save($path_download);  
 		}
 		return response()->download($path_download)->deleteFileAfterSend(true);
    }
	
    public function sendSms($phone,$message){

        $username = "aammii"; //your username
        $password = "aammii$@7888"; //your password
        $sender = "ATSWEB"; //Your senderid Aammii
        $message = 'Aammii - '.$message;
        $username = urlencode($username);
        $password = urlencode($password);
        $sender = urlencode($sender);
        $message = urlencode($message);
        $entityId = urlencode(1201160750480695643);
        $templateId=urlencode(1207161804463855735);
        $url="http://bulksmscoimbatore.co.in/sendsms?uname=$username&pwd=$password&senderid=$sender&to=$phone&msg=$message&route=T&peid=$entityId&tempid=$templateId";
        $ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch); // This is the result from the API
		curl_close($ch);
        return $response;
	} 
}
