<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $hasError = false;
    protected $responseCode = 200;

    public function setHasError($hasError)
    {
        $this->hasError = $hasError;

        return $this;
    }

    public function setResponseCode($code)
    {
        $this->responseCode = $code;

        return $this;
    }

    public function response($data, $code)
    {
        return response()->json([
            'error' => $this->hasError,
            'code' => $code == 200 ? $code : "03e" . $code,
            'message' => Status::getMessage($code),
            'data' => $data
        ], $this->responseCode);
    }

    public function success($data = null, $code = 200)
    {
        return $this->setHasError(false)
            ->setResponseCode(Response::HTTP_OK)
            ->response($data, $code);
    }

    public function error($error = null,$data = null)
    {
        return $this->setHasError(true)
            ->setResponseCode(Response::HTTP_BAD_REQUEST)
            ->response($data, $error);
    }

}
