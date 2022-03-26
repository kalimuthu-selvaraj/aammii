<?php

namespace Webkul\MobikulApiTransformer\Helpers;

class Authentication
{
    /**
     * Add Customer Guard and JWT
     *
     * @param data $value
     * @return mixed
    */
    public function customerAuthentication($token, $guard)
    {
        $loggedCustomer = auth($guard)->user();

        if ($token) {
            try {
                $setToken =  \JWTAuth::setToken($token)->authenticate();
                $customerFromToken = \JWTAuth::toUser($setToken);

                if (isset($setToken) && isset($customerFromToken) && $loggedCustomer != NULL) {
                    if ($customerFromToken->id == $loggedCustomer->id) {
                        return true;
                    }
                } else {
                    return response()->json([
                        'success'   => false,
                        'message'   => trans('mobikul-api::app.api.customer.address-info.error-login'),
                        'error'     => trans('mobikul-api::app.api.customer.address-info.error-login'),
                    ], 401);
                }
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json([
                    'success'   => false,
                    'message'   => $e->getMessage(),
                    'error'     => $e->getMessage(),
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success'   => false,
                    'message'   => $e->getMessage(),
                    'error'     => $e->getMessage(),
                ], 401);
            }
        }
    }
}