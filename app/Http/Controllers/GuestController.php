<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Market;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class GuestController extends Controller
{
    public function getMarketSession($market_session_id, $drink_id = null)
    {
        $market = Market::where('market_session_id', $market_session_id)->first();

        if ($market == null)
        {
            abort(404);
        }

        $drinks = $market->drinks;

        return view('chartsview', ['market' => $market, 'drinks' => $drinks, 'selected_drink_id' => $drink_id ? $drink_id : false]);
    }

    public function getAllMarketSession($market_session_id)
    {
        $market = Market::where('market_session_id', $market_session_id)->first();

        if ($market == null)
        {
            abort(404);
        }

        $drinks = $market->drinks;
        return view('allchartsview', ['drinks' => $drinks, 'market' => $market]);
    }

    public function showQrCode()
    {
        $user = Auth::user();
        $market = $user->markets()->first();

        if ($market && $user->marketOpen()) {
            $marketSessionId = $market->id;
            $url = "http://127.0.0.1:8000/session/id/{$marketSessionId}";

            $qrCode = new QrCode($url);
            $writer = new PngWriter();
            $qrCodeImage = $writer->write($qrCode)->getString();

            return view('qrcode', ['qrCodeImage' => $qrCodeImage, 'market_session_id' => $market->market_session_id]);
        } else {
            return view('qrcode', ['marketOpen' => false]);
        }
    }

    public function enterCode(Request $request)
    {
        return view('entercode');
    }

    public function codeSubmit(Request $request)
    {
        $validated = $request->validate([
            'i1' => 'required|numeric',
            'i2' => 'required|numeric',
            'i3' => 'required|numeric',
            'i4' => 'required|numeric',
            'i5' => 'required|numeric',
            'i6' => 'required|numeric'
        ]);
        $market_session_id = "BE-" . $validated['i1']. $validated['i2']. $validated['i3']. $validated['i4']. $validated['i5']. $validated['i6'];
        $market = Market::where('market_session_id', $market_session_id)->first();
        if ($market) {
            header('Location: /session/id/'. $market_session_id);
            exit();
        } else {
            return redirect()->route('entercode')->with('error', 'Invalid code!');
        }
    }


    public function about(Request $request) {
        return view('about');
    }
}
