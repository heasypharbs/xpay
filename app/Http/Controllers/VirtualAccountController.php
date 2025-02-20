<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
  
class VirtualAccountController extends Controller
{
    

    // //   // Create Virtual Account via SafeHaven API
    //   public function createVirtualAccount()
    //   {
    //       $user = Auth::user();
  
    //       if ($user->virtual_account_number) {
    //           return response()->json(['message' => 'User already has a virtual account'], 409);
    //       }
  
    //       try {
    //           $response = Http::post('https://api.sandbox.safehavenmfb.com/accounts', [
    //               'user_id' => $user->id,
    //               'name' => $user->first_name . ' ' . $user->last_name,
    //               'email' => $user->email
    //           ]);
  
    //           if ($response->successful()) {
    //               $data = $response->json();
    //               $user->update(['virtual_account_number' => $data['accountNumber']]);
  
    //               return response()->json([
    //                   'message' => 'Virtual account created successfully',
    //                   'virtualAccountNumber' => $data['accountNumber']
    //               ]);
    //           }
    //           else {
    //             Log::error('SafeHaven API Response: ' . $response->body());
    //             return response()->json([
    //                 'message' => 'Failed to create virtual account',
    //                 'error' => $response->body()
    //             ], $response->status());
    //         }
            
    //           return response()->json(['message' => 'Failed to create virtual account'], 500);
    //       } catch (\Exception $e) {
    //           Log::error("SafeHaven API Error: " . $e->getMessage());
    //           return response()->json(['message' => 'Error connecting to SafeHaven API'], 500);
    //       }
    //   }
  
    
  // Create Virtual Account via SafeHaven API
public function createVirtualAccount()
{
    $user = Auth::user();

    if ($user->virtual_account_number) {
        return response()->json(['message' => 'User already has a virtual account'], 409);
    }

    try {
        // SafeHaven API credentials
        $apiUrl = 'https://api.sandbox.safehavenmfb.com/accounts';
        $authorizationToken = env('SAFEHAVEN_API_KEY'); // Store in .env
        $clientId = env('SAFEHAVEN_CLIENT_ID'); // Store in .env

        // Prepare the request payload
        $requestData = [
            'accountType' => 'Savings',
            'suffix' => 'House Rent',
            'metadata' => new \stdClass() // Equivalent to {}
        ];

        // Send API request
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authorizationToken,
            'ClientID' => $clientId,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, $requestData);

        // Check response
        if ($response->successful()) {
            $data = $response->json();
            $user->update(['virtual_account_number' => $data['accountNumber']]);

            return response()->json([
                'message' => 'Virtual account created successfully',
                'virtualAccountNumber' => $data['accountNumber']
            ]);
        } else {
            Log::error('SafeHaven API Response: ' . $response->body());
            return response()->json([
                'message' => 'Failed to create virtual account',
                'error' => $response->body()
            ], $response->status());
        }
    } catch (\Exception $e) {
        Log::error("SafeHaven API Error: " . $e->getMessage());
        return response()->json([
            'message' => 'Error connecting to SafeHaven API',
            'error' => $e->getMessage()
        ], 500);
    }
}

    

      // Handle SafeHaven Webhook
    //   public function handleWebhook(Request $request)
    //   {
    //       $data = $request->all();
  
    //       if (!isset($data['event']) || !isset($data['data'])) {
    //           return response()->json(['message' => 'Invalid webhook data'], 400);
    //       }
  
    //       if ($data['event'] === 'transaction_successful') {
    //           try {
    //               $transactionExists = Transaction::where('transaction_id', $data['data']['transactionId'])->exists();
  
    //               if ($transactionExists) {
    //                   return response()->json(['message' => 'Transaction already processed'], 409);
    //               }
  
    //               Transaction::create([
    //                   'account_number' => $data['data']['accountNumber'],
    //                   'amount' => $data['data']['amount'],
    //                   'transaction_id' => $data['data']['transactionId'],
    //               ]);
  
    //               return response()->json(['message' => 'Webhook processed successfully']);
    //           } catch (\Exception $e) {
    //               Log::error("Webhook Handling Failed: " . $e->getMessage());
    //               return response()->json(['message' => 'Failed to process webhook'], 500);
    //           }
    //       }
  
    //       return response()->json(['message' => 'Invalid webhook event'], 400);
    //   }


      public function handleWebhook(Request $request)
      {
          // Log the incoming request for debugging
          Log::info('SafeHaven Webhook Received:', $request->all());
  
          // Validate the request
          $data = $request->validate([
              'event' => 'required|string',
              'data.accountNumber' => 'required|string',
              'data.amount' => 'required|numeric',
              'data.transactionId' => 'required|string'
          ]);
  
          // Process webhook event
          if ($data['event'] === 'transaction_successful') {
              Log::info("Transaction successful for Account: {$data['data']['accountNumber']}, Amount: {$data['data']['amount']}");
  
              return response()->json(['message' => 'Webhook processed successfully']);
          }
  
          return response()->json(['message' => 'Event not supported'], 400);
      }
  
}
