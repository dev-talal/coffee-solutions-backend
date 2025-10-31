<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClickPayService
{
    protected $baseUrl;
    protected $serverKey;
    protected $profileId;

    public function __construct()
    {
        $this->baseUrl = 'https://secure.clickpay.com.sa/payment';
        $this->serverKey = 'S2JNMKHWW9-JLW6G6KRN2-6HWTBZTKBD';
        $this->profileId = '47228';
    }

    public function createPayment(array $data)
    {
        $payload = [
            "profile_id" => $this->profileId,
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => uniqid('ORDER_'),
            "cart_currency" => "SAR",
            "cart_amount" => $data['amount'],
            "cart_description" => "Order payment",
            "customer_details" => [
                "name" => $data['name'],
                "email" => $data['email'],
                // "phone" => $data['phone'],
                // "street1" => $data['address'],
                // "city" => $data['city'],
                "country" => "SA",
            ],
            "callback" => route('clickpay.callback'),
            // "Callback" => env('FRONTEND_URL') . '/payment-status',
            "return" => 'https://coffeesolutions-customer.vercel.app/en/profile?tab=orders',
        ];

        $response = Http::withHeaders([
            'authorization' => $this->serverKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/request", $payload);

        return $response->json();
    }

    public function verifyPayment($tranRef)
    {
        $response = Http::withHeaders([
            'authorization' => $this->serverKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/query", [
            "profile_id" => $this->profileId,
            "tran_ref" => $tranRef
        ]);

        return $response->json();
    }

    public function refund(array $data)
    {
        $payload = [
            "profile_id"     => $this->profileId,
            "tran_type"      => "refund",
            "tran_class"     => "ecom",
            "cart_currency"  => 'SAR',
            "cart_amount"    => $data['amount'],
            "cart_description" => 'Refund',
            "tran_ref"        => $data['tran_ref'],
        ];

        $response = Http::withHeaders([
            'authorization' => $this->serverKey,
            'Content-Type'  => 'application/json',
        ])->post("{$this->baseUrl}/payment/request", $payload);

        return $response->json();
    }

}
