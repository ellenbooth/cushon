
// construct etc. above

    /**
     * @param int $accountId
     * @param int $fundId
     * @param int $amount
     * @param DateTimeImmutable $created
     * @return Investment
     * @throws CustomersException
     * @throws AccountsException
     * @throws FundsException
     * @throws InvestmentsException
     * @return Investment
     */
    public function create(int $accountId, int $fundId, int $amount, DateTimeImmutable $created): Investment
    {
        $customer = $this->customerRepository->getByAccountId($accountId);

        if (!$customer || !$customer->active) {
            throw CustomersException::notFoundByAccountId($accountId);
        }

        $account = $this->accountRepository->getById($accountId);

        if (!$account || !$account->active) {
            throw AccountUsException::notFoundById($accountId);
        }

        $fund = $this->fundRepository->getById($accountId);

        if (!$fund || !$fund->active) {
            throw FundsException::notFoundById($fundId);
        }

        if ($amount > $account->getRemainingAnnualAllowance()  {
           throw InvestmentsException::overAllowance($account->getById());
        }

        $purchase = $this->createFundClient($fund)->purchase($fund, $account, $amount);   
        $purchase = json_decode($purchase->getBody());

        $investment = Investment::create([
            'account_id' => $accountId,
            'fund_id' => $fundId,
            'created' => $created,
        ]);

        $transaction = Transaction::create([
            'investment_id' => $investment->getId(),
            'account_id' => $accountId,
            'fund_id' => $fundId,
            'unit_cost_snapshot' => $purchase->unit_cost,
            'units_bought' => $purchase->total_units,
            'units_sold' => null
        ]);
  
        return $investment;
    }

    /**
     * @param Fund $fund
     * @return AbstractTransactions
     */
    public function createfundClient(Fund $fund): AbstractTransactions
    {
        return $this->processTransactionFactory->create($fund);
    }
}
 
