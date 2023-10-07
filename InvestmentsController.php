//// rest of the file above

/**
 * @param CreateInvestmentRequest $request
 * @param CreateInvestmentService $service
 * @return Response
 */
public function createInvestment(
    CreateInvestmentRequest $request, 
    CreateInvestmentService $service
): Response
{
    try {
        $investment = $service->create(
            $request->post('accountId'),
            $request->post('fundId'),
            $request->post('amount'),
            new DateTimeImmutable($request->post('created'))
        );
        return new Response(InvestmentResponse::one($investment), 201)
    } catch (Throwable $exception) {
        return new Response(
            [    
                'status' => 'error', 
                'message' => $exception->getMessage()
            ],
            400
        );
    } 
}
