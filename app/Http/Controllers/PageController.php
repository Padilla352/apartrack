<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    /**
     * Home page – redirects logged‑in users to their dashboards,
     * otherwise shows the guest view with latest apartments.
     */
    public function home()
    {
        if (session()->has('admin_email')) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('owner')->check()) {
            return redirect()->route('owner.dashboard');
        }
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $apartments = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->select('apartments.*', 'owners.name as owner_name')
            ->where('apartments.verification_status', 'approved')
            ->where('apartments.status', 'Vacant')
            ->orderBy('apartments.created_at', 'desc')
            ->limit(6)
            ->get();

        return view('guest', compact('apartments'));
    }

    /**
     * Explore page – paginated approved apartments.
     */
    public function explore()
    {
        $apartments = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->select('apartments.*', 'owners.name as owner_name')
            ->where('apartments.verification_status', 'approved')
            ->where('apartments.status', 'Vacant')
            ->orderBy('apartments.created_at', 'desc')
            ->paginate(12);

        return view('guest', compact('apartments'));
    }

    /**
     * Barangay details page – shows apartments and business spaces for a given barangay.
     */
    public function barangayDetails(Request $request)
    {
        $name = $request->query('name', '');
        $available = $request->query('available', 0);
        $total = $request->query('total', 0);
        $logo = $request->query('logo', '');

        if (empty($name)) {
            return redirect()->route('home')->with('error', 'Barangay information not found.');
        }

        $apartments = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->select('apartments.*', 'owners.name as owner_name')
            ->where('apartments.barangay_name', $name)
            ->where('apartments.verification_status', 'approved')
            ->where('apartments.status', 'Vacant')
            ->orderBy('apartments.created_at', 'desc')
            ->get();

        $businessSpaces = collect();
        if (Schema::hasTable('business_spaces')) {
            $businessSpaces = DB::table('business_spaces')
                ->leftJoin('owners', 'business_spaces.owner_id', '=', 'owners.id')
                ->select(
                    'business_spaces.*',
                    'business_spaces.barangay_name as barangay_name',
                    'owners.name as owner_name'
                )
                ->where('business_spaces.barangay_name', $name)
                ->where('business_spaces.verification_status', 'approved')
                ->where('business_spaces.status', 'Available')
                ->orderBy('business_spaces.created_at', 'desc')
                ->get();
        }

        return view('user.barangay.barangay-apartments', [
            'barangayName' => $name,
            'availableCount' => $available,
            'totalCount' => $total,
            'barangayLogo' => $logo,
            'apartments' => $apartments,
            'businessSpaces' => $businessSpaces
        ]);
    }

    /**
     * Dashboard redirect – sends users to the correct dashboard.
     */
    public function dashboardRedirect()
    {
        if (session()->has('admin_email')) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('owner')->check()) {
            return redirect()->route('owner.dashboard');
        }
        return redirect()->route('home');
    }
}