<?php

namespace App\Controllers;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Product;

class ProductController extends BaseController
{

    protected $product;

    public function __construct()
    {
        $this->product = new Product();
        // helper('form', 'url', 'text', 'cookie', 'response');
    }


    public function index()
    {
         $data = $this->product->findAll();
         

        return $this->response->setJson($data, 200);

    }

    public function show($id) { 

        
           $data = $this->product->find($id);   

         if($data) {
            return $this->response->setJson([$data]);
          }
        else{
             return $this->response->setJson(["message"=>"data not found"]);
        }


    }

    public function create() {

 
    $product_name = $this->request->getPost('product_name');
    $product_description = $this->request->getPost('product_description');
    $product_price = $this->request->getPost('product_price');
    $product_quantity = $this->request->getPost('product_quantity');
    $product_image = $this->request->getFile('product_image');  
    
     // Validate the input values
    $validationRules = [
        'product_name' => 'required',
        'product_description' => 'required',
        'product_price' => 'required|numeric',
        'product_quantity' => 'required|numeric',
        'product_image' => 'uploaded[product_image]|max_size[product_image,2024]|ext_in[product_image,jpg,jpeg,png]',
    ];
    if (!$this->validate($validationRules)) {
        
        
       return $this->response
         ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
        ->setJson([

         'message'=>false,
         'errors' => $this->validator->getErrors()
        ]);
    }

    $newName = $product_image->getRandomName();
    $product_image->move(WRITEPATH . 'uploads', $newName);
    
    
     // Insert the data into the database
    $data = [
        'product_name' => $product_name,
        'product_description' => $product_description,
        'product_price' => $product_price,
        'product_quantity' => $product_quantity,
        'product_image' => $newName,
    ];
    $this->product->insert($data);
    
    return $this->response
         
        ->setJson([
         'message'=>true,
         'success' => 'Product add successfully'
        ]);

}



 public function update($id)
{
    $product = $this->product->find($id);

    if (!$product) {
        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => 'Product not found.']);
    }
     

    $data = [
        'product_name' => $this->request->getVar('product_name'),
        'product_description' => $this->request->getVar('product_description'),
        'product_price' => $this->request->getVar('product_price'),
        'product_quantity' => $this->request->getVar('product_quantity'),
        'product_image' => $this->request->getFile('product_image'),
    ];

    // Validate the input values
    $validationRules = [
        'product_name' => 'required',
        'product_description' => 'required',
        'product_price' => 'required|numeric',
        'product_quantity' => 'required|numeric',
        'product_image' => 'uploaded[product_image]|max_size[product_image,2024]|ext_in[product_image,jpg,jpeg,png]',
    ];
    if (!$this->validate($validationRules)) {
        return $this->response
            ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
            ->setJSON([
                'message' => false,
                'errors' => $this->validator->getErrors(),
            ]);
    }

    $productData = [
        'product_name' => $data['product_name'],
        'product_description' => $data['product_description'],
        'product_price' => $data['product_price'],
        'product_quantity' => $data['product_quantity'],
    ];

    if ($data['product_image'] && $data['product_image']->isValid()) {
        // Delete the old image if it exists
        if ($product['product_image']) {
            unlink(WRITEPATH . 'uploads/' . $product['product_image']);
        }

        // Upload the new image and update the product data
        $newFileName = $data['product_image']->getRandomName();
        $data['product_image']->move(WRITEPATH . 'uploads/', $newFileName);
        $productData['product_image'] = $newFileName;
    }

    // Update the product data in the database
    $this->product->update($id, $productData);

    return $this->response->setJSON(['message' => 'Product updated successfully.']);
}


    public function delete($id){
        
       
        $data = $this->product->find($id);

        if($data){

          
            $oldImage = WRITEPATH . 'uploads/' . $data['product_image'];
           if(file_exists($oldImage)) {
            unlink($oldImage);
        }

         $data = $this->product->delete($id);
           
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Product successfully deleted'
                ]
            ];
            return $this->response->setJson($response);
        }else{
             return $this->response->setJson(["message"=>"data not found"]);
        }


}
}
