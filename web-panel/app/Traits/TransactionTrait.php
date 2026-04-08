<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\Transection;
use Carbon\Carbon;

trait TransactionTrait
{
    /**
     * Create a payable transaction
     */
    private function createPayableTransaction(float $amount, int $customerId, int $orderId, Account $account): void
    {
        $this->createTransaction(
            'Payable',
            $account,
            $amount,
            'POS order',
            true, // isDebit
            date("Y/m/d"),
            $customerId,
            $orderId
        );
    }

    /**
     * Create a receivable transaction
     */
    private function createReceivableTransaction(float $amount, int $customerId, int $orderId, Account $account): void
    {
        $transaction = new Transection();
        $transaction->tran_type = 'Receivable';
        $transaction->account_id = $account->id;
        $transaction->amount = $amount;
        $transaction->description = 'POS order';
        $transaction->debit = 0;
        $transaction->credit = 1;
        $transaction->balance = $account->balance + $amount;
        $transaction->date = date("Y/m/d");
        $transaction->customer_id = $customerId;
        $transaction->order_id = $orderId;
        $transaction->save();

        $account->total_in += $amount;
        $account->balance += $amount;
        $account->save();
    }

    /**
     * Create an income transaction
     */
    private function createIncomeTransaction(float $amount, int $customerId, int $orderId, Account $account): void
    {
        $transaction = new Transection();
        $transaction->tran_type = 'Income';
        $transaction->account_id = $account->id;
        $transaction->amount = $amount;
        $transaction->description = 'POS order';
        $transaction->debit = 0;
        $transaction->credit = 1;
        $transaction->balance = $account->balance + $amount;
        $transaction->date = date("Y/m/d");
        $transaction->customer_id = $customerId;
        $transaction->order_id = $orderId;
        $transaction->save();

        $account->balance += $amount;
        $account->total_in += $amount;
        $account->save();
    }

    /**
     * Handle wallet payment transaction
     */
    private function handleWalletPayment(float $grandTotal, float $remainingBalance, int $customerId, int $orderId): void
    {
        $customer = $this->customer->find($customerId);

        if ($remainingBalance >= 0) {
            // Full payment from wallet
            $payableAccount = Account::find(2);
            $this->createPayableTransaction($grandTotal, $customerId, $orderId, $payableAccount);
        } else {
            // Partial payment from wallet + receivable
            if ($customer->balance > 0) {
                // Use existing wallet balance
                $payableAccount = Account::find(2);
                $this->createPayableTransaction($customer->balance, $customerId, $orderId, $payableAccount);

                // Create receivable for remaining amount
                $receivableAccount = Account::find(3);
                $this->createReceivableTransaction(-$remainingBalance, $customerId, $orderId, $receivableAccount);
            } else {
                // Full amount as receivable
                $receivableAccount = Account::find(3);
                $this->createReceivableTransaction($grandTotal, $customerId, $orderId, $receivableAccount);
            }
        }

        // Update customer balance
        $customer->balance = $remainingBalance;
        $customer->save();
    }

    /**
     * Handle non-wallet payment transaction
     */
    private function handleNonWalletPayment(float $amount, int $customerId, int $orderId, int $accountId): void
    {
        $account = Account::find($accountId);
        $this->createIncomeTransaction($amount, $customerId, $orderId, $account);
    }

    protected function createTransaction(
        $tranType,
        Account $account,
        $amount,
        $description,
        $isDebit,
        $date,
        $customerId,
        $orderId = null
    ): void
    {
        $transaction = new Transection();
        $transaction->tran_type = $tranType;
        $transaction->account_id = $account->id;
        $transaction->amount = $amount;
        $transaction->description = $description;
        $transaction->debit = $isDebit ? 1 : 0;
        $transaction->credit = $isDebit ? 0 : 1;
        $transaction->balance = $isDebit
            ? $account->balance - $amount
            : $account->balance + $amount;
        $transaction->date = $date;
        $transaction->customer_id = $customerId;
        $transaction->order_id = $orderId;
        $transaction->save();

        // Update account balance
        if ($isDebit) {
            $account->total_out += $amount;
            $account->balance -= $amount;
        } else {
            $account->total_in += $amount;
            $account->balance += $amount;
        }
        $account->save();
    }

    public function transactionQueryList(array $filters)
    {
        [$column, $direction] = getSorting($filters['sorting_type'] ?? 'latest');
        if ($column === 'name') {
            $column = 'tran_type';
        }
        return Transection::when($filters['search'], function ($query) use ($filters) {
                $key = explode(' ', $filters['search']);
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere(function ($w) use ($value) {
                            $w->whereHas('account', function ($accountQuery) use ($value) {
                                $accountQuery->where('account', 'like', "%{$value}%");
                            });
                        })
                        ->orWhere('tran_type', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($filters['start_date']) && isset($filters['end_date']), function ($query) use ($filters) {
                $start = Carbon::parse($filters['start_date'])->startOfDay();
                $end = Carbon::parse($filters['end_date'])->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            })
            ->when(isset($filters['transaction_type']) && $filters['transaction_type'] != 'all', function ($query) use ($filters) {
                $query->where('tran_type', $filters['transaction_type']);
            })
            ->when(isset($filters['account_id']) && $filters['account_id'] != 'all', function ($query) use ($filters) {
                $query->where('account_id', $filters['account_id']);
            })
            ->orderBy($column, $direction);
    }
}
