<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log; // Ensure this import is present
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class UpdateDrinkPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-drink-prices';

    private $console;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update market prices in the database';

    /**
     * Execute the console command.
     */
    public function handleAll(mixed $con)
    {
        $this->console = $con;
        $users = User::all();
        foreach ($users as $user) {
            if (!$user->marketOpen()) {
                continue;
            }
            $market = $user->markets->first();
            $drinks = $market->drinks;
            foreach ($drinks as $drink) {
                $this->handle($drink);
            }
        }
    }

    

    public function handle($drink)
    {
        $bottomPrice = $drink->bottom_price; // minimum price
        $upperPrice = $drink->upper_price; // maximum price
        $marketprice = $drink->market_price; // current market price
        $price_history = json_decode($drink->price_history, true);
        $transactions = json_decode($drink->transactions, true); // transactions
        $newMarketPrice = $this->calculateOrganicMarketPrice($transactions, $bottomPrice, $upperPrice, $marketprice);

        if (isset($this->console)) $this->console->comment($newMarketPrice);

        $price_history["labels"] = ["time", "price"];
        if (!isset($price_history["prices"])) {
            $price_history["prices"] = [];
            $price_history["times"] = [];
        }
        $price_history["borders"] = ['min' => $bottomPrice,'max' => $upperPrice];
        array_push($price_history["prices"], $newMarketPrice);
        array_push($price_history["times"], Carbon::now());
        $drink->market_price = $newMarketPrice;
        
        $drink->price_history = json_encode($price_history);
        $drink->save();

    }

    function calculateOrganicMarketPrice($transactions, $bottomPrice, $upperPrice, $currentPrice) {
        // Initial parameters
        $S0 = $currentPrice;  // Initial price
        $mu = 0.05;  // Drift coefficient
        $sigma = 0.2;  // Volatility coefficient
        $dt = 10 / 120;  // Time increment in seconds
        $lower = $bottomPrice;  // Lower price bound
        $upper = $upperPrice;  // Upper price bound
        $Z = mt_rand() / mt_getrandmax();
        $Z = (2 * $Z) - 1;

        // Calculate the new price using the GBM formula
        $nextPrice = $currentPrice * exp(($mu - 0.5 * $sigma ** 2) * $dt + $sigma * sqrt($dt) * $Z);

        // Ensure the price stays within the given bounds
        if ($nextPrice < $lower) {
            $nextPrice = $lower;
        } elseif ($nextPrice > $upper) {
            $nextPrice = $upper;
        }

        return round($nextPrice, 2);
    }

    function calculateMarketPrice($transactions, $bottomPrice, $upperPrice, $currentPrice) {
        $currentTime = Carbon::now();
        $oneTimeAgo = $currentTime->copy()->subSeconds(100);
        
        // Calculate the total amount of drinks bought in the last minute
        $totalAmount = 1;
        foreach ($transactions as $transaction) {
            $transactionTime = Carbon::parse($transaction['time']);
            if ($transactionTime >= $oneTimeAgo && $transactionTime <= $currentTime) {
                $totalAmount += $transaction['amount'];
            }
        }
        
        // Calculate the average price change per transaction
        $totalTransactions = count($transactions);
        $averageTransactionAmount = $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0;
    
        // Avoid division by zero and negative infinity
        if ($averageTransactionAmount <= 0) {
            $averageTransactionAmount = 1; // Default to 1 if no transactions or invalid data
        }
    
        // Adjust the sensitivity to changes in market activity
        $tick = 0.2 * ($upperPrice - $bottomPrice); // Increase sensitivity for more volatility
        $multiplier = log($averageTransactionAmount);
        $tick = $multiplier == 0 ? $tick * 2 : $tick * $multiplier;
    
        // Determine the direction of price change based on transaction volume
        if ($totalTransactions > 0) {
            $percentTransactions = $totalTransactions / $totalAmount;
    
            // Adjust price based on transaction volume percentage
            if ($percentTransactions <= 0.6) {
                // If 60% or more of transactions are buys, tendency to increase price
                $priceChange = $tick * (0.8 + 0.2 * rand(0, 10) / 10) / $percentTransactions; // Increase price more aggressively
            } elseif ($percentTransactions <= 0.4) {
                // If 40% or less of transactions are buys, tendency to decrease price
                $priceChange = - ($tick * (0.8 + 0.2 * rand(0, 10) / 10))/4 * 1 + $percentTransactions; // Decrease price less aggressively -> profits increase
            } else {
                // Neutral or balanced transaction volume, moderate price change
                $priceChange = $tick * (rand(0, 1) ? 1 : -1) * (0.5 + 0.5 * rand(0, 10) / 10); // Random small change
            }
        } else {
            // No transactions, price fluctuates randomly around current price
            $priceChange = rand(-1, 1) * $tick * (0.5 + 0.5 * rand(0, 10) / 10); // Random fluctuation around current price
        }
    
        // Apply the price change to the current price
        $newPrice = $currentPrice + $priceChange;
        
        // Ensure the new price stays within the allowed range
        $newPrice = max($bottomPrice, min($newPrice, $upperPrice));
        
        return round($newPrice, 2);

    }
    
    
}
