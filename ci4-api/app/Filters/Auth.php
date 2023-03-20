<?php

namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth implements FilterInterface
{
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('JWT_SECRET_KEY');
        $header = $request->getHeader('authorization');
        if(!$header) return Services::response()
                            ->setJSON(['msg' => 'Token Required'])
                            ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
       
       
       $jwt = $header->getValue();
      
       
        try {
           $decoded = JWT::decode($jwt, new Key($key, 'HS256'));


        } catch (Throwable $th) {
            return Services::response()
                            ->setJSON(['msg' => 'Invalid Token'])
                            ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

     public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
