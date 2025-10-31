<?php

namespace App\Services;
use App\Models\Transaction;

class TransactionService
{
    public function getCustomerTransactions($userId)
    {
        return Transaction::where('user_id', $userId)->paginate(10);
    }
}