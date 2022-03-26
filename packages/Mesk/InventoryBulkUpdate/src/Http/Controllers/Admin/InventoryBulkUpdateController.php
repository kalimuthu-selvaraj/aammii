<?php

namespace Mesk\InventoryBulkUpdate\Http\Controllers\Admin;

use Webkul\Admin\Imports\DataGridImport;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Attribute\Repositories\AttributeRepository;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InventoryBulkUpdateController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * ProductAttributeValueRepository object
     *
     * @var \Webkul\Attribute\Repositories\ProductAttributeValueRepository
     *
     */
    protected $ProductAttributeValueRepository;

     /**
     * Product inventory repository instance.
     *
     * @var \Webkul\Product\Repositories\ProductInventoryRepository
     */
    protected $productInventoryRepository;

    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProductAttributeValueRepository $productAttributeValueRepository,ProductInventoryRepository $productInventoryRepository,AttributeRepository $attributeRepository)
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->productAttributeValueRepository = $productAttributeValueRepository;

        $this->productInventoryRepository = $productInventoryRepository;

        $this->attributeRepository = $attributeRepository;


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $attrList=$this->attributeRepository->select(array("id","code","admin_name"))->where("type","text")->where("is_unique",1)->get();
        return view($this->_config['view'],compact('attrList'));
    }

    /**
     * import the specified resource to storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function importProductsInventory()
    {
        request()->validate ([
            'attr_name' => 'required',
            'file_path' => 'required',
        ]);

        $valid_extension = ['csv', 'xls', 'xlsx'];

        $fileDir = 'imported-inventory-update/admin/files';

        $attrName = request()->attr_name;

        $file = request()->file('file_path');
        
        $attrResult=$this->attributeRepository->select(array("id","code","type"))->where("type","text")->where("is_unique",1)->where("code",$attrName)->first();
        if($attrResult){
            $attrId=$attrResult->id;

            if (in_array($file->getClientOriginalExtension(), $valid_extension)) {

                $uploadedFile = $file->storeAs($fileDir, uniqid().'.'.$file->getClientOriginalExtension());

                if ($uploadedFile) {
                    $csvData = (new DataGridImport)->toArray($uploadedFile)[0];
                    
                    $attrVal=[]; $errData=[]; $csvDataCount=count($csvData); $updateValue=[];
                    for ($i = 0; $i < $csvDataCount; $i++) {
                        if(isset($csvData[$i][$attrName]) && $csvData[$i][$attrName] && isset($csvData[$i]["inventories"]) && $csvData[$i]["inventories"]){
                            $attrVal[]=$csvData[$i][$attrName];
                            $updateValue[$csvData[$i][$attrName]]=$csvData[$i]["inventories"];
                        }else{
                            $csvData[$i]["error"]="Empty Value at Row Number ".($i+1);
                            $errData[]=$csvData[$i];
                        }
                    }
                    if(count($errData)==0){
                        $DBdata=$this->productAttributeValueRepository->where("attribute_id",$attrId)->whereIn("text_value", $attrVal)->get();
                        if(count($attrVal) === count($DBdata)){
                            foreach($DBdata as $va=>$key) {
                                $this->productInventoryRepository->where('product_id', $key->product_id) ->limit(1) ->update(["qty"=>$updateValue[$key->text_value]]);
                            } 
                            session()->flash('success', trans('inventorybulkupdate::app.admin.bulk-upload.messages.imported'));
                            return redirect()->route('admin.inventorybulkupdate.index');

                        }else{
                            $dbAttrVal=[];
                            foreach($DBdata as $va=>$key) {
                                $dbAttrVal[]=$key->$attrName;
                            }
                            for ($i = 0; $i < $csvDataCount; $i++) {
                                if(!in_array($csvData[$i][$attrName],$dbAttrVal)){
                                    $csvData[$i]["error"]="Not Found in DB at Row Number ".($i+1);
                                    $errData[]=$csvData[$i];
                                }
                            }
                        }
                    }
                    session()->flash('error', trans('inventorybulkupdate::app.admin.bulk-upload.messages.cant-able-to-import'));
                    return view($this->_config['view'], compact('errData'));    
                }

            } else {
                session()->flash('error', trans('inventorybulkupdate::app.admin.bulk-upload.messages.file-format-error'));
                return redirect()->route('admin.inventorybulkupdate.index');
            }
        } else {
            session()->flash('error', trans('inventorybulkupdate::app.admin.bulk-upload.messages.attr-error'));
            return redirect()->route('admin.inventorybulkupdate.index');
        }
    

        
    }
    
    /**
     * download the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadFile()
    {
        return response()->download(public_path('storage/inventory-update/bulkInventoryUpdate.xlsx'));
    }
}
