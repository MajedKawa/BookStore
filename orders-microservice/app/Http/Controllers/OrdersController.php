<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrdersController extends Controller
{
    public function purchase($id)
    {
        // Validate and process the purchase
        $purchaseResult = $this->processPurchase($id);

        if ($purchaseResult['success']) {
            return response()->json(['message' => 'Purchase successful']);
        } else {
            return response()->json(['error' => $purchaseResult['message']], 400);
        }
    }

    private function processPurchase($id)
    {
        // Query the catalog microservice to check item availability
        $catalogResponse = Http::get('http://127.0.0.1:8000/catalog/' . $id);

        if ($catalogResponse->successful()) {
            $catalogData = $catalogResponse->json();

            // Check if the item is in stock
            if ($catalogData['quantity'] > 0) {
                // Perform the purchase
                // decrement the in-stock count in the catalog microservice
                $this->updateCatalog($id, $catalogData['quantity'] - 1);

                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Item out of stock'];
            }
        } else {
            return ['success' => false, 'message' => 'Failed to check item availability'];
        }
    }

    private function updateCatalog($id, $quantity)
    {
        // Update the catalog microservice with the new quantity
        $updateResponse = Http::put('http://127.0.0.1:8000/catalog/' . $id, [
            'quantity' => $quantity,
        ]);

        // Handle the response as needed
        if (!$updateResponse->successful()) {
            return ['success' => false, 'message' => 'Failed to update the item quantity'];
        }
    }
}
