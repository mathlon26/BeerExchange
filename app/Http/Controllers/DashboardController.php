<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Market;
use App\Models\User;
use App\Console\Commands\UpdateDrinkPrices;

class DashboardController extends Controller
{
    public function checkout(Request $request)
    {
        // Get the quantities from the form submission
        $quantities = $request->input('quantity'); 
        
        /* $quantities eg. {
            "_token": "Y4o7FrBBH5a6PnWjhLnITGl1nlocIE1ROQmu7FCH",
            "quantity": {
                "1": "2",
                "2": "1",
                "3": "0"
            }
        }
        */
        $user = Auth::user();
        $cmd = new UpdateDrinkPrices();
        $market = $user->markets->first();
        $drinks = $market->drinks;

        foreach ($drinks as $drink) {
            $drinkId = $drink->id;
            $quantity = isset($quantities[$drinkId]) ? (int) $quantities[$drinkId] : 0;

            $profit = $quantity * $drink->market_price - $quantity * $drink->cost_price;
            
            $drink->amount_sold += $quantity;
            $transactions = json_decode($drink->transactions, true);
            $transactionNumber = count($transactions);
            $transactions[$transactionNumber] = ["time" => now(), "amount" => $quantity];
            $drink->transactions = json_encode($transactions);
            $drink->save();

            $market->profit += $profit;
            $market->save();

        }

        return redirect()->route('cashier')->with('success', 'Checkout successful!');
    }

    public function cashier(Request $request)
    {
        $user = Auth::user();
        $drinks = $user->marketOpen() ? $user->markets()->first()->drinks : null;
        return view('cashier', ['drinks' => $drinks]);
    }


    public function createMarket(Request $request)
    {
        $request['profit'] = 0;
        $request['user_id'] = Auth::user()->id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string',
            'profit' => 'required|numeric',
            'user_id' => 'required|numeric'
        ]);
        $validated['market_session_id'] = ' ';
        $market = Market::create($validated);
        $market->market_session_id = 'BE-' . strval(100000 + $market->id);
        $market->save();

        return redirect()->route('dashboard')->with('success', 'Market created successfully!');
    }

    public function deleteMarket(Request $request)
    {
        $request->validate([
           'market_id' =>'required|numeric'
        ]);

        $market = Market::findOrFail($request->market_id);
        $market->delete();

        return redirect()->route('dashboard')->with('success', 'Market deleted successfully!');
    }

    public function index(Request $request)
    {
        $data = [
            'user' => null,
            'market' => null,
            'drinks' => [],
        ];

        if (Auth::user()->marketOpen())
        {
            $user = Auth::user();
            $market = $user->markets()->first();
            $drinks = $market->drinks->sortByDesc('amount_sold');

            $data['user'] = $user;
            $data['market'] = $market;
            $data['drinks'] = $drinks;
        }

        

        return view('dashboard', $data);
    }
}


/**
 * 
 * 
 * public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|string',
            'market_price' => 'required|numeric',
            // Add other fields and validation rules as necessary
        ]);

        $market = auth()->user()->markets->first(); // Adjust as needed

        $drink = new Drink($validated);
        $drink->market_id = $market->id;
        $drink->save();

        return redirect()->route('drinks.index')->with('success', 'Drink added successfully!');
    }
 * 
 * 
 * 
 */