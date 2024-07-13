<?php

namespace App\Http\Controllers;

use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorWookbookExport;
use App\Exports\rtcmarket\SchoolConsumptionExport\SrcExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TestingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($name)
    {
        //

        switch ($name) {
            case 'rpmf':
                $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

                return Excel::download(new RtcProductionFarmerWorkbookExport(true), 'rpmf' . $time . '.xlsx');
                break;

            case 'hrc':
                $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

                return Excel::download(new HrcExport(true), 'hrc' . $time . '.xlsx');
                break;

            case 'rpmp':
                $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

                return Excel::download(new RtcProductionProcessorWookbookExport(true), 'rpmp' . $time . '.xlsx');
                break;

            case 'src':
                $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

                return Excel::download(new SrcExport(true), 'src' . $time . '.xlsx');
                break;

            default:
                # code...
                break;
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
