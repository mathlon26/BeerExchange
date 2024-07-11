<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Drink;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DrinkController extends Controller
{
    public function index()
    {
        if (Auth::user()->marketOpen())
        {
            $drinks = auth()->user()->markets->first()->drinks; // Adjust as needed
        } else {
            $drinks = [];
        }
        
        return view('drinks.index', compact('drinks'));
    }

    public function create()
    {
        return view('drinks.create');
    }

    public function store(Request $request)
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

    public function edit(Drink $drink)
    {
        return view('drinks.edit', compact('drink'));
    }

    public function getMarketPrice($id)
    {
        $market = Auth::user()->markets->first(); // Adjust as needed
        $drinks = $market->drinks;
        $drink = $drinks->find($id);
        if ($drink) {
            return response()->json(['market_price' => $market->unit . $drink->market_price]);
        } else {
            return response()->json(['error' => 'Drink not found'], 404);
        }
    }

    public function getMarketPriceHistory($id)
    {
        $market = Auth::user()->markets->first(); // Adjust as needed
        $drinks = $market->drinks;
        $drink = $drinks->find($id);
        if ($drink) {
            return response()->json(['price_history' => $drink->price_history]);
        } else {
            return response()->json(['error' => 'Drink not found'], 404);
        }
    }

    public function update(Request $request, Drink $drink)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|string',
            'market_price' => 'required|numeric',
            // Add other fields and validation rules as necessary
        ]);

        $drink->update($validated);

        return redirect()->route('drinks.index')->with('success', 'Drink updated successfully!');
    }
}
