<?php

namespace App\Controllers;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{

    protected $user;

    public function __construct()
    {
        $this->user = new User();
      
    }


    public function index()
    {
        $data = $this->user->findAll();   

         if($data) {
            return $this->response->setJson([$data]);
          }
        else{
             return $this->response->setJson(["message"=>"data not found"]);
        } 

    }

    public function show($id) {

        
           $data = $this->user->find($id);   

         if($data) {
            return $this->response->setJson([$data]);
          }
        else{
             return $this->response->setJson(["message"=>"data not found"]);
        }


    }

    public function register() {

 
    $username = $this->request->getPost('username');
    $email = $this->request->getPost('email');
    $image = $this->request->getFile('image'); 
    $password = $this->request->getPost('password');

    
     // Validate the input values
    $validationRules = [
        'username' => 'required|min_length[3]|max_length[20]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[4]|max_length[20]',
        'confirmpassword'  => 'matches[password]',
        'image' => 'uploaded[image]|max_size[image,2024]|ext_in[image,jpg,jpeg,png]',
    ];
    if (!$this->validate($validationRules)) {
        
        
       return $this->response
         ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
        ->setJson([

         'message'=>false,
         'errors' => $this->validator->getErrors()
        ]);
    }

    $newName = $image->getRandomName();
    $image->move(WRITEPATH . 'userimage', $newName);
    
    
     // Insert the data into the database
    $data = [
        'username' => $username,
        'email' => $email,
        'image' => $newName,
         'password' => $password,
    ];

     $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $this->user->insert($data);

    $userId = $this->user->getInsertID();
    $user = $this->user->find($userId);
    
     $token = $this->generateJWT($user);
       return $this->response
         
        ->setJson([
            'token' => $token,
         'message'=>true,
         'success' => 'User add successfully'
        ]);

}

  public function login(){

    $useremail = $this->request->getPost('email');
    $userpassword = $this->request->getPost('password');


    $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response
         ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
        ->setJson([

         'message'=>false,
         'errors' => $this->validator->getErrors()
        ]);
        }


        $user = $this->user->where("email", $useremail)->first();


      if(!$user || !password_verify($userpassword, $user['password'])) {
         
             return $this->response
         ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
        ->setJson([

         'message'=>false,
         'errors' => "Invalid Credentials"
        ]);
      }
// Generate JWT token
        $token = $this->generateJWT($user);
      
       return $this->response
         
        ->setJson([
            'token' => $token,
            'uername'=> $user['username'],
         'message'=>true,
         'success' => 'Login successfully'
        ]);

       
  }


  private function generateJWT($user)
    {
        $key = getenv('JWT_SECRET_KEY');
        $payload = [
            
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24,
            "uid" => $user['id'],
            "email" => $user['email']
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

}
