/// stuff up here 

class FundClientFactory
{
    /**
     * @param Fund $fund
     * @return FundClientInterface
     * @throws FundsException
     */
    public function create(Fund $fund): FundClientInterface
    {
        switch ($fund->getBroker()) {
            case 'XTB':
                return app(XTBClient::class);
          
            case 'eToro':
                return app(eToroClient::class);
          
            case 'FinecoBank':
                return app(FinecoBankClient::class);  
        }

        throw FundsException::brokerClientNotFound($fund->getbroker());
    }
}
