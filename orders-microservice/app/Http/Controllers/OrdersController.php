<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrdersController extends Controller
{
    public function purchase($itemNumber)
    {
        // Validate and process the purchase
        $purchaseResult = $this->processPurchase($itemNumber);

        if ($purchaseResult['success']) {
            return response()->json(['message' => 'Purchase successful']);
        } else {
            return response()->json(['error' => $purchaseResult['message']], 400);
        }
    }

    private function processPurchase($itemNumber)
    {
        // Query the catalog microservice to check item availability
        $catalogResponse = Http::get('http://catalog-microservice:8000/catalog/' . $itemNumber);

        if ($catalogResponse->successful()) {
            $catalogData = $catalogResponse->json();

            // Check if the item is in stock
            if ($catalogData['quantity'] > 0) {
                // Perform the purchase
                // decrement the in-stock count in the catalog microservice
                $this->updateCatalog($itemNumber, $catalogData['quantity'] - 1);

                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Item out of stock'];
            }
        } else {
            return ['success' => false, 'message' => 'Failed to check item availability'];
        }
    }

    private function updateCatalog($itemNumber, $newQuantity)
    {
        // Update the catalog microservice with the new quantity
        $updateResponse = Http::put('http://catalog-microservice:8000/catalog/' . $itemNumber, [
            'quantity' => $newQuantity,
        ]);

        // Handle the response as needed
        if (!$updateResponse->successful()) {
            return ['success' => false, 'message' => 'Failed to update the item quantity'];
        }
    }
}
