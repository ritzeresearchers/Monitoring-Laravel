<?php

namespace App\Http\Controllers\Api;
use \Stripe\Stripe;
use App\Models\User;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{


    public function __construct()
    {
        // $this->middleware('auth');
    }
    public function index()
    {
        $plans = $this->retrievePlans();

        return view('subscriptions.index', compact('plans'));
    }
    public function retrievePlans()
    {
        $key = env('STRIPE_API_KEY');
        $stripe = new StripeClient($key);
        $plansraw = $stripe->plans->all();
        $plans = $plansraw->data;

        foreach ($plans as $plan) {
            $prod = $stripe->products->retrieve(
                $plan->product,
                []
            );
            $plan->product = $prod;
        }
        // dd($plans[0]->id);
        return $plans;
    }

    // public function processSubscription(Request $request)
    // {
    //     Stripe::setApiKey(config('services.stripe.secret'));
    //     $user = Auth::user();
    //     $paymentMethod ='pm_card_visa';
    //     $customer=$user->createOrGetStripeCustomer();
    //     $payment_method=$user->addPaymentMethod($paymentMethod);
    //     \Stripe\Customer::update(
    //         $customer->id,  // Replace with the actual customer ID
    //         [
    //           'invoice_settings' => [
    //             'default_payment_method' => $payment_method->id,  // Replace with the actual payment method ID
    //           ],
    //         ]
    //       );

    //     $plans = $this->retrievePlans();
    //     $plan =$plans[0]->id;

    //     try {
    //         $user->newSubscription('account_subscription',$plan)
    //                 ->create($payment_method->id, [
    //                 'email' => $user->email,
    //             ]);
    //         return response()->json([
    //             'success'      => true,
    //             'message' => "Subscription successful",
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success'      => false,
    //             'message' => 'Error proccessing subscription. ' . $e->getMessage()
    //         ]);
    //     }
    // }
    public function processSubscription(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $user_id = $request->user;

        $user = User::findorfail($user_id['id']);
        $user->createOrGetStripeCustomer();
        $paymentMethod = \Stripe\PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'token' => $request->token,
            ],
        ]);
        $payment_method =$user->addPaymentMethod($paymentMethod->id);
        $plan =$request->plan;
        try {
            $user->newSubscription('account_subscription',$plan)
                    ->create($payment_method->id, [
                    'email' => $user->email,
                ]);
            return response()->json([
                'success'      => true,
                'message' => "Subscription successful",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'      => false,
                'message' => 'Error proccessing subscription. ' . $e->getMessage()
            ]);
        }
    }
    // public function processSubscription(Request $request)
    // {
    //     Stripe::setApiKey(config('services.stripe.secret'));
    //     $user_id = $request->user;

    //     $user = User::findOrFail($user_id['id']);
    //     $customer = $user->createOrGetStripeCustomer();
    //     // Attach the payment method to the customer
    //     $payment_method = $customer->sources->create(['source' => $request->payment_method_id]);

    //     $plan = $request->plan;

    //     try {
    //         $subscription = $user->newSubscription('account_subscription', $plan);

    //         if ($subscription) {
    //             $subscription->create($payment_method->id, [
    //                 'email' => $user->email,
    //             ]);

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => "Subscription successful",
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Error creating subscription. Subscription instance is null.'
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error processing subscription. ' . $e->getMessage()
    //         ]);
    //     }
    // }
}
