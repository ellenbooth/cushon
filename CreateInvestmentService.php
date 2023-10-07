
// construct etc. above

    /**
     * @param int $accountId
     * @param int $fundId
     * @param int $amount
     * @param DateTimeImmutable $created
     * @throws CustomersException
     * @throws AccountsException
     * @throws FundsException
     * @throws InvestmentsException
     * @throws TransactionsException
     * @return Investment
     */
    public function create(
        int $accountId, 
        int $fundId, 
        int $amount, 
        DateTimeImmutable $created
    ): Investment
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
           throw InvestmentsException::overAllowance($account->getId());
        }

        $purchase = $this->purchase($fund, $account, $amount); 

        if ($purchase->getStatusCode() !== 201) {
            throw TransactionsException::purchaseError($account->getId());
        }

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
     * @param Account $account
     * @param int $amount
     * @return Response
     */
    public function purchase(Fund $fund, Account $account, int $amonut)): Response
    {
        $client = $this->fundClientFactory->create($fund->getId());
        return new Response(
            $client->purchase(Fund $fund, Account $account, int $amonut)
        ); 
    }
}
 
