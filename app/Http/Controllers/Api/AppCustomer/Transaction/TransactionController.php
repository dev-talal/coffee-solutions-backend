<?php

namespace App\Http\Controllers\Api\AppCustomer\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    use ApiResponseTrait;
    protected $transactionService;
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }
    public function index()
    {
        $transactions = $this->transactionService->getCustomerTransactions(auth()->id());
        return $this->successCollection($transactions, TransactionResource::class, 'Transaction retrieved successfully');
    }
}
