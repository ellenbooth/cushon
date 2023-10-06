//// rest of the file above

/**
 * @param CreateInvestmentRequest $request
 * @param CreateInvestmentService $service
 *
 * @return Response
 */
public function createInvestment(CreateInvestmentRequest $request, CreateInvestmentService $service): Response
{
    try {
        $service->create(
            $request->post('accountId'),
            $request->post('fundId'),
            $request->post('amount'),
            new DateTimeImmutable($request->post('created')),
        );
        return new Response(null, 204);
    } catch (Exception $e) {
        return new Response(['status' => 'error', 'message' => $exception->getMessage()], 500);
    }
}