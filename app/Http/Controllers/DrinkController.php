<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Drink;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DrinkController extends Controller
{
    public function index()
    {

        if (Auth::user()->marketOpen())
        {
            $market = auth()->user()->markets->first();
            $drinks = $market->drinks; // Adjust as needed
        } else {
            $market = null;
            $drinks = [];
        }

        
        return view('drinks.index', ['drinks' => $drinks,'market' => $market]);
    }

    public function create()
    {
        return view('drinks.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
    
        $rules = [
            'drink_name' => 'required|max:20',
            'start_price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'bottom_price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'upper_price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'retail_price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'manual_pump_dump' => 'nullable',
            'auto_pump_dump' => 'nullable',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ];
    
        $messages = [
            'bottom_price.lt_or_equal' => 'The bottom price must be less than or equal to the upper price.',
            'start_price.between' => 'The start price must be between the bottom price and the upper price.',
        ];
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        // Add custom validation to check bottom_price <= upper_price
        $validator->after(function ($validator) use ($request) {
            $bottomPrice = $request->input('bottom_price');
            $upperPrice = $request->input('upper_price');
            $startPrice = $request->input('start_price');
    
            if ($bottomPrice > $upperPrice) {
                $validator->errors()->add('bottom_price', 'The bottom price must be less than or equal to the upper price.');
            }
    
            if ($startPrice < $bottomPrice || $startPrice > $upperPrice) {
                $validator->errors()->add('start_price', 'The start price must be between the bottom price and the upper price.');
            }
        });
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Create and save the drink
        $drink = new Drink();
        $drink->name = $request->input('drink_name');
        $drink->in_discount = false;
        $drink->pumping = false;
        $drink->dumping = false;
        $drink->allow_discount = false;
        $drink->allow_autocrash = $request->input('auto_pump_dump', 'off') == 'off' ? false : true;
        $drink->allow_manualcrash = $request->input('manual_pump_dump', 'off') == 'off' ? false : true;
        $drink->amount_sold = 0;
        $drink->market_price = $request->input('start_price');
        $drink->bottom_price = $request->input('bottom_price');
        $drink->upper_price = $request->input('upper_price');
        $drink->cost_price = $request->input('retail_price');
        $drink->market_id = $user->markets()->first()->id;
    
        if ($request->hasFile('image')) {
            // Store the uploaded image
            $image = $request->file('image');
            $imagePath = $image->store('public/images/' . str_replace(' ', '_', $user->name) . $user->id);
            $imagePath = str_replace('public', 'storage', $imagePath);
            $drink->logo = $imagePath;
        } else {
            // Use default image if no image is uploaded
            $drink->logo = 'public/images/BeerLogo.webp';
        }
    
        $drink->save();
    
        return redirect()->route('drinks')->with('success', 'Drink added successfully!');
    }
    


    public function edit(Drink $drink)
    {
        return view('drinks.edit', compact('drink'));
    }

    public function delete(Request $request)
    {
        $request->validate([
            'drink_id' =>'required|numeric'
         ]);
 
         $drink = Drink::findOrFail($request->drink_id);
         $path = str_replace('storage', 'public', $drink->logo);
         if (Storage::exists($path))
         {
             Storage::delete($path);
         }
         $drink->delete();
 
         return redirect()->route('drinks')->with('success', 'Market deleted successfully!');
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
