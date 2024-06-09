<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Page Name</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">

                <div class="mb-1 row justify-content-center">
                    <form wire:submit='save'>

                        <div class="d-flex ">
                            <div class="card col-12 col-md-8">
                                <div class="card-header fw-bold">Location</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="" class="form-label">ENTERPRISE</label>
                                        <x-text-input wire:model='enterprise' />
                                        @error('enterprise')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="form-label">DISTRICT</label>
                                        <select class="form-select" wire:model='district'>
                                            <option>BALAKA</option>
                                            <option>BLANTYRE</option>
                                            <option>CHIKWAWA</option>
                                            <option>CHIRADZULU</option>
                                            <option>CHITIPA</option>
                                            <option>DEDZA</option>
                                            <option>DOWA</option>
                                            <option>KARONGA</option>
                                            <option>KASUNGU</option>
                                            <option>LILONGWE</option>
                                            <option>MACHINGA</option>
                                            <option>MANGOCHI</option>
                                            <option>MCHINJI</option>
                                            <option>MULANJE</option>
                                            <option>MWANZA</option>
                                            <option>MZIMBA</option>
                                            <option>NENO</option>
                                            <option>NKHATA BAY</option>
                                            <option>NKHOTAKOTA</option>
                                            <option>NSANJE</option>
                                            <option>NTCHEU</option>
                                            <option>NTCHISI</option>
                                            <option>PHALOMBE</option>
                                            <option>RUMPHI</option>
                                            <option>SALIMA</option>
                                            <option>THYOLO</option>
                                            <option>ZOMBA</option>
                                        </select>
                                        @error('district')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="" class="form-label">EPA</label>
                                        <x-text-input wire:model='epa' />
                                        @error('epa')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="" class="form-label">SECTION</label>
                                        <x-text-input wire:model='section' />
                                        @error('section')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-md-8">



                            <div class="card">

                                <div class="card-body">

                                    <!-- Date of Recruitment -->
                                    <div class="mb-3">
                                        <label for="dateOfRecruitment" class="form-label">Date of
                                            Recruitment</label>
                                        <input type="date" class="form-control" id="dateOfRecruitment"
                                            wire:model=''>
                                    </div>

                                    <!-- Name of Actor -->
                                    <div class="mb-3">
                                        <label for="nameOfActor" class="form-label">Name of Actor</label>
                                        <input type="text" class="form-control" id="nameOfActor" wire:model=''>
                                    </div>

                                    <!-- Name of Representative -->
                                    <div class="mb-3">
                                        <label for="nameOfRepresentative" class="form-label">Name of
                                            Representative</label>
                                        <input type="text" class="form-control" id="nameOfRepresentative"
                                            wire:model=''>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="mb-3">
                                        <label for="phoneNumber" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phoneNumber" wire:model=''>
                                    </div>

                                    <!-- Type -->
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type</label>
                                        <input type="text" class="form-control" id="type" wire:model=''>
                                    </div>

                                    <!-- Approach (For Producer Organizations Only) -->
                                    <div class="mb-3">
                                        <label for="approach" class="form-label">If Producer Organization, What
                                            Approach Does Your Group Follow (For Producer Organizations
                                            Only)</label>
                                        <textarea class="form-control" id="approach" rows="3" wire:model=''></textarea>
                                    </div>

                                    <!-- Sector -->
                                    <div class="mb-3">
                                        <label for="sector" class="form-label">Sector</label>
                                        <input type="text" class="form-control" id="sector" wire:model=''>
                                    </div>

                                    <!-- Number of Members (For Producer Organizations Only) -->
                                    <div class="mb-3">
                                        <label for="numberOfMembers" class="form-label">Number of Members (For
                                            Producer Organizations Only)</label>
                                        <input type="number" class="form-control" id="numberOfMembers" wire:model=''>
                                    </div>

                                    <!-- Group -->
                                    <div class="mb-3">
                                        <label for="group" class="form-label">Group</label>
                                        <input type="text" class="form-control" id="group" wire:model=''>
                                    </div>

                                    <!-- New or Old Establishment -->
                                    <div class="mb-3">
                                        <label for="establishment" class="form-label">Is this a New or Old
                                            Establishment</label>
                                        <select class="form-select" id="establishment" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="new">New</option>
                                            <option value="old">Old</option>
                                        </select>
                                    </div>

                                    <!-- Formally Registered Entity -->
                                    <div class="mb-3">
                                        <label for="registeredEntity" class="form-label">Is this a Formally
                                            Registered Entity</label>
                                        <select class="form-select" id="registeredEntity" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Registration Details -->
                                    <div class="mb-3">
                                        <label for="registrationDetails" class="form-label">Registration
                                            Details</label>
                                        <textarea class="form-control" id="registrationDetails" rows="3" wire:model=''></textarea>
                                    </div>

                                    <!-- Number of Employees on RTC Establishment -->
                                    <div class="mb-3">
                                        <label for="numberOfEmployees" class="form-label">Number of Employees on
                                            RTC Establishment</label>
                                        <input type="number" class="form-control" id="numberOfEmployees"
                                            wire:model=''>
                                    </div>


                                    <hr>
                                    <!-- Area Under Cultivation (Number of Acres) by Variety -->
                                    <div class="mb-3">
                                        <label for="areaUnderCultivation" class="form-label">Area Under Cultivation
                                            (Number of Acres) by Variety</label>
                                        <input type="number" class="form-control" id="areaUnderCultivation"
                                            wire:model=''>
                                    </div>

                                    <!-- Number of Plantlets Produced -->
                                    <div class="mb-3">
                                        <label for="numberOfPlantlets" class="form-label">Number of Plantlets
                                            Produced</label>
                                        <input type="number" class="form-control" id="numberOfPlantlets"
                                            wire:model=''>
                                    </div>

                                    <!-- Number of Screen House Vines Harvested (Sweet Potatoes) -->
                                    <div class="mb-3">
                                        <label for="numberOfScreenHouseVines" class="form-label">Number of Screen
                                            House Vines Harvested (Sweet Potatoes)</label>
                                        <input type="number" class="form-control" id="numberOfScreenHouseVines"
                                            wire:model=''>
                                    </div>

                                    <!-- Number of Screen House Mini-Tubers Harvested (Potato) -->
                                    <div class="mb-3">
                                        <label for="numberOfMiniTubers" class="form-label">Number of Screen House
                                            Mini-Tubers Harvested (Potato)</label>
                                        <input type="number" class="form-control" id="numberOfMiniTubers"
                                            wire:model=''>
                                    </div>

                                    <!-- Number of SAH Plants Produced (Cassava) -->
                                    <div class="mb-3">
                                        <label for="numberOfSAHPlants" class="form-label">Number of SAH Plants
                                            Produced (Cassava)</label>
                                        <input type="number" class="form-control" id="numberOfSAHPlants"
                                            wire:model=''>
                                    </div>

                                    <!-- Area Under Basic Seed Multiplication (Number of Acres) -->
                                    <div class="mb-3">
                                        <label for="areaUnderBasicSeed" class="form-label">Area Under Basic Seed
                                            Multiplication (Number of Acres)</label>
                                        <input type="number" class="form-control" id="areaUnderBasicSeed"
                                            wire:model=''>
                                    </div>

                                    <!-- Area Under Certified Seed Multiplication -->
                                    <div class="mb-3">
                                        <label for="areaUnderCertifiedSeed" class="form-label">Area Under Certified
                                            Seed Multiplication</label>
                                        <input type="number" class="form-control" id="areaUnderCertifiedSeed"
                                            wire:model=''>
                                    </div>

                                    <!-- Are You a Registered Seed Producer -->
                                    <div class="mb-3">
                                        <label for="registeredSeedProducer" class="form-label">Are You a Registered
                                            Seed Producer</label>
                                        <select class="form-select" id="registeredSeedProducer" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Registration Details (Seed Services Unit) -->
                                    <div class="mb-3">
                                        <label for="seedRegistrationDetails" class="form-label">Registration Details
                                            (Seed Services Unit)</label>
                                        <textarea class="form-control" id="seedRegistrationDetails" rows="3" wire:model=''></textarea>
                                    </div>

                                    <!-- Do You Use Certified Seed -->
                                    <div class="mb-3">
                                        <label for="useCertifiedSeed" class="form-label">Do You Use Certified
                                            Seed</label>
                                        <select class="form-select" id="useCertifiedSeed" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>


                                    <hr>
                                    <!-- Market Segment (Multiple Responses) -->
                                    <div class="mb-3">
                                        <label for="marketSegment" class="form-label">Market Segment (Multiple
                                            Responses)</label>
                                        <select class="form-select" id="marketSegment" wire:model='' multiple>
                                            <option value="segment1">Segment 1</option>
                                            <option value="segment2">Segment 2</option>
                                            <option value="segment3">Segment 3</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>

                                    <!-- RTC Market Contractual Agreement -->
                                    <div class="mb-3">
                                        <label for="rtcMarketContract" class="form-label">Do You Have Any RTC Market
                                            Contractual Agreement</label>
                                        <select class="form-select" id="rtcMarketContract" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Total Volume of Production in Previous Season (Metric Tonnes) -->
                                    <div class="mb-3">
                                        <label for="totalVolumeProduction" class="form-label">Total Volume of
                                            Production in Previous Season (Metric Tonnes)</label>
                                        <input type="number" class="form-control" id="totalVolumeProduction"
                                            wire:model=''>
                                    </div>

                                    <!-- Total Value Production Previous Season (Financial Value-MWK) -->
                                    <div class="mb-3">
                                        <label for="totalValueProduction" class="form-label">Total Value Production
                                            Previous Season (Financial Value-MWK)</label>
                                        <input type="number" class="form-control" id="totalValueProduction"
                                            wire:model=''>
                                    </div>

                                    <!-- Total Volume of Production in Previous Season from Irrigation Farming (Metric Tonnes) -->
                                    <div class="mb-3">
                                        <label for="totalVolumeIrrigation" class="form-label">Total Volume of
                                            Production in Previous Season from Irrigation Farming (Metric
                                            Tonnes)</label>
                                        <input type="number" class="form-control" id="totalVolumeIrrigation"
                                            wire:model=''>
                                    </div>

                                    <!-- Total Value of Irrigation Production in Previous Season (Financial Value-MWK) -->
                                    <div class="mb-3">
                                        <label for="totalValueIrrigation" class="form-label">Total Value of Irrigation
                                            Production in Previous Season (Financial Value-MWK)</label>
                                        <input type="number" class="form-control" id="totalValueIrrigation"
                                            wire:model=''>
                                    </div>

                                    <!-- Sell RTC Products to Domestic Markets -->
                                    <div class="mb-3">
                                        <label for="sellToDomesticMarkets" class="form-label">Do You Sell Your RTC
                                            Products to Domestic Markets</label>
                                        <select class="form-select" id="sellToDomesticMarkets" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Sell Products to International Markets -->
                                    <div class="mb-3">
                                        <label for="sellToInternationalMarkets" class="form-label">Do You Sell Your
                                            Products to International Markets</label>
                                        <select class="form-select" id="sellToInternationalMarkets" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Sell Products Through Market Information Systems -->
                                    <div class="mb-3">
                                        <label for="sellThroughMarketInfo" class="form-label">Do You Sell Your
                                            Products Through Market Information Systems</label>
                                        <select class="form-select" id="sellThroughMarketInfo" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Sell RTC Produce Through Aggregation Centers -->
                                    <div class="mb-3">
                                        <label for="sellThroughAggregationCenters" class="form-label">Do You Sell RTC
                                            Produce Through Aggregation Centers</label>
                                        <select class="form-select" id="sellThroughAggregationCenters"
                                            wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        <input type="text" class="mt-2 form-control"
                                            placeholder="Specify Name of Aggregation Center" wire:model=''>
                                    </div>

                                    <!-- Total Volume of RTC Sold Through Aggregation Centers in Previous Season (Metric Tonnes) -->
                                    <div class="mb-3">
                                        <label for="totalVolumeSoldThroughAggregation" class="form-label">Total Volume
                                            of RTC Sold Through Aggregation Centers in Previous Season (Metric
                                            Tonnes)</label>
                                        <input type="number" class="form-control"
                                            id="totalVolumeSoldThroughAggregation" wire:model=''>
                                    </div>

                                    <hr>

                                    <!-- Group Name -->
                                    <div class="mb-3">
                                        <label for="groupName" class="form-label">Group Name</label>
                                        <input type="text" class="form-control" id="groupName" wire:model=''>
                                    </div>

                                    <!-- District -->
                                    <div class="mb-3">
                                        <label for="district" class="form-label">District</label>
                                        <input type="text" class="form-control" id="district" wire:model=''>
                                    </div>

                                    <!-- EPA -->
                                    <div class="mb-3">
                                        <label for="epa" class="form-label">EPA</label>
                                        <input type="text" class="form-control" id="epa" wire:model=''>
                                    </div>

                                    <!-- SECTION -->
                                    <div class="mb-3">
                                        <label for="epa" class="form-label">SECTION</label>
                                        <input type="text" class="form-control" id="section" wire:model=''>
                                    </div>


                                    <!-- Enterprise -->
                                    <div class="mb-3">
                                        <label for="enterprise" class="form-label">Enterprise</label>
                                        <input type="text" class="form-control" id="enterprise" wire:model=''>
                                    </div>
                                    '
                                    <!-- Date of Follow Up -->
                                    <div class="mb-3">
                                        <label for="dateOfFollowUp" class="form-label">Date of Follow Up</label>
                                        <input type="date" class="form-control" id="dateOfFollowUp"
                                            wire:model=''>
                                    </div>

                                    <!-- Area Under Cultivation (Number of Acres) by Variety -->
                                    <div class="mb-3">
                                        <label for="areaUnderCultivation" class="form-label">Area Under Cultivation
                                            (Number of Acres) by Variety</label>
                                        <input type="number" class="form-control" id="areaUnderCultivation"
                                            wire:model=''>
                                    </div>

                                    <!-- Number of Plantlets Produced -->
                                    <div class="mb-3">
                                        <label for="numberOfPlantlets" class="form-label">Number of Plantlets
                                            Produced</label>
                                        <input type="number" class="form-control" id="numberOfPlantlets"
                                            wire:model=''>
                                    </div>

                                    <!-- Number of Screen House Vines Harvested (Sweet Potatoes) -->
                                    <div class="mb-3">
                                        <label for="numberOfScreenHouseVines" class="form-label">Number of Screen
                                            House Vines Harvested (Sweet Potatoes)</label>
                                        <input type="number" class="form-control" id="numberOfScreenHouseVines"
                                            wire:model=''>
                                    </div>

                                    <!-- Number of Screen House Mini-Tubers Harvested (Potato) -->
                                    <div class="mb-3">
                                        <label for="numberOfMiniTubers" class="form-label">Number of Screen House
                                            Mini-Tubers Harvested (Potato)</label>
                                        <input type="number" class="form-control" id="numberOfMiniTubers"
                                            wire:model=''>
                                    </div>

                                    <!-- Number of SAH Plants Produced (Cassava) -->
                                    <div class="mb-3">
                                        <label for="numberOfSAHPlants" class="form-label">Number of SAH Plants
                                            Produced (Cassava)</label>
                                        <input type="number" class="form-control" id="numberOfSAHPlants"
                                            wire:model=''>
                                    </div>

                                    <!-- Area Under Basic Seed Multiplication (Number of Acres) -->
                                    <div class="mb-3">
                                        <label for="areaUnderBasicSeed" class="form-label">Area Under Basic Seed
                                            Multiplication (Number of Acres)</label>
                                        <input type="number" class="form-control" id="areaUnderBasicSeed"
                                            wire:model=''>
                                    </div>

                                    <!-- Area Under Certified Seed Multiplication -->
                                    <div class="mb-3">
                                        <label for="areaUnderCertifiedSeed" class="form-label">Area Under Certified
                                            Seed Multiplication</label>
                                        <input type="number" class="form-control" id="areaUnderCertifiedSeed"
                                            wire:model=''>
                                    </div>

                                    <!-- Are You a Registered Seed Producer -->
                                    <div class="mb-3">
                                        <label for="registeredSeedProducer" class="form-label">Are You a Registered
                                            Seed Producer</label>
                                        <select class="form-select" id="registeredSeedProducer" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Registration Details (Seed Services Unit) -->
                                    <div class="mb-3">
                                        <label for="seedRegistrationDetails" class="form-label">Registration Details
                                            (Seed Services Unit)</label>
                                        <textarea class="form-control" id="seedRegistrationDetails" rows="3" wire:model=''></textarea>
                                    </div>

                                    <!-- Do You Use Certified Seed -->
                                    <div class="mb-3">
                                        <label for="useCertifiedSeed" class="form-label">Do You Use Certified
                                            Seed</label>
                                        <select class="form-select" id="useCertifiedSeed" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>


                                    <hr>
                                    <!-- Market Segment (Multiple Responses) -->
                                    <div class="mb-3">
                                        <label for="marketSegment" class="form-label">Market Segment (Multiple
                                            Responses)</label>
                                        <select class="form-select" id="marketSegment" wire:model='' multiple>
                                            <option value="segment1">Segment 1</option>
                                            <option value="segment2">Segment 2</option>
                                            <option value="segment3">Segment 3</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>

                                    <!-- RTC Market Contractual Agreement -->
                                    <div class="mb-3">
                                        <label for="rtcMarketContract" class="form-label">Do You Have Any RTC Market
                                            Contractual Agreement</label>
                                        <select class="form-select" id="rtcMarketContract" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Total Volume of Production in Previous Season (Metric Tonnes) -->
                                    <div class="mb-3">
                                        <label for="totalVolumeProduction" class="form-label">Total Volume of
                                            Production in Previous Season (Metric Tonnes)</label>
                                        <input type="number" class="form-control" id="totalVolumeProduction"
                                            wire:model=''>
                                    </div>

                                    <!-- Total Value Production Previous Season (Financial Value-MWK) -->
                                    <div class="mb-3">
                                        <label for="totalValueProduction" class="form-label">Total Value Production
                                            Previous Season (Financial Value-MWK)</label>
                                        <input type="number" class="form-control" id="totalValueProduction"
                                            wire:model=''>
                                    </div>

                                    <!-- Total Volume of Production in Previous Season from Irrigation Farming (Metric Tonnes) -->
                                    <div class="mb-3">
                                        <label for="totalVolumeIrrigation" class="form-label">Total Volume of
                                            Production in Previous Season from Irrigation Farming (Metric
                                            Tonnes)</label>
                                        <input type="number" class="form-control" id="totalVolumeIrrigation"
                                            wire:model=''>
                                    </div>

                                    <!-- Total Value of Irrigation Production in Previous Season (Financial Value-MWK) -->
                                    <div class="mb-3">
                                        <label for="totalValueIrrigation" class="form-label">Total Value of Irrigation
                                            Production in Previous Season (Financial Value-MWK)</label>
                                        <input type="number" class="form-control" id="totalValueIrrigation"
                                            wire:model=''>
                                    </div>

                                    <!-- Do You Sell Your RTC Products to Domestic Markets -->
                                    <div class="mb-3">
                                        <label for="sellToDomesticMarkets" class="form-label">Do You Sell Your RTC
                                            Products to Domestic Markets</label>
                                        <select class="form-select" id="sellToDomesticMarkets" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Do You Sell Your Products to International Markets -->
                                    <div class="mb-3">
                                        <label for="sellToInternationalMarkets" class="form-label">Do You Sell Your
                                            Products to International Markets</label>
                                        <select class="form-select" id="sellToInternationalMarkets" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Do You Sell Your Products Through Market Information Systems -->
                                    <div class="mb-3">
                                        <label for="sellThroughMarketInfo" class="form-label">Do You Sell Your
                                            Products Through Market Information Systems</label>
                                        <select class="form-select" id="sellThroughMarketInfo" wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <!-- Do You Sell RTC Produce Through Aggregation Centers -->
                                    <div class="mb-3">
                                        <label for="sellThroughAggregationCenters" class="form-label">Do You Sell RTC
                                            Produce Through Aggregation Centers</label>
                                        <select class="form-select" id="sellThroughAggregationCenters"
                                            wire:model=''>
                                            <option value="" selected>Select an option</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        <input type="text" class="mt-2 form-control"
                                            placeholder="Specify Name of Aggregation Center" wire:model=''>
                                    </div>

                                    <!-- Total Volume of RTC Sold Through Aggregation Centers in Previous Season (Metric Tonnes) -->
                                    <div class="mb-3">
                                        <label for="totalVolumeSoldThroughAggregation" class="form-label">Total Volume
                                            of RTC Sold Through Aggregation Centers in Previous Season (Metric
                                            Tonnes)</label>
                                        <input type="number" class="form-control"
                                            id="totalVolumeSoldThroughAggregation" wire:model=''>
                                    </div>


                                    <hr>

                                    <!-- Date Recorded (YY/MM/DD) -->
                                    <div class="mb-3">
                                        <label for="dateRecorded" class="form-label">Date Recorded (YY/MM/DD)</label>
                                        <input type="date" class="form-control" id="dateRecorded" wire:model=''>
                                    </div>

                                    <!-- Name of Partner -->
                                    <div class="mb-3">
                                        <label for="partnerName" class="form-label">Name of Partner</label>
                                        <input type="text" class="form-control" id="partnerName" wire:model=''>
                                    </div>

                                    <!-- Country -->
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" wire:model=''>
                                    </div>

                                    <!-- Date of Maximum Sale (YY/MM/DD) -->
                                    <div class="mb-3">
                                        <label for="dateOfMaxSale" class="form-label">Date of Maximum Sale
                                            (YY/MM/DD)</label>
                                        <input type="date" class="form-control" id="dateOfMaxSale"
                                            wire:model=''>
                                    </div>

                                    <!-- Product (Seed, Ware, Value Added Products) -->
                                    <div class="mb-3">
                                        <label for="productType" class="form-label">Product (Seed, Ware, Value Added
                                            Products)</label>
                                        <input type="text" class="form-control" id="productType" wire:model=''>
                                    </div>

                                    <!-- Volume Sold in Previous Period (Metric Tonnes) -->
                                    <div class="mb-3">
                                        <label for="volumeSold" class="form-label">Volume Sold in Previous Period
                                            (Metric
                                            Tonnes)</label>
                                        <input type="number" class="form-control" id="volumeSold" wire:model=''>
                                    </div>

                                    <!-- Financial Value of Sales (Malawi Kwacha) -->
                                    <div class="mb-3">
                                        <label for="financialValue" class="form-label">Financial Value of Sales
                                            (Malawi
                                            Kwacha)</label>
                                        <input type="number" class="form-control" id="financialValue"
                                            wire:model=''>
                                    </div>

                                    <hr>

                                    <!-- Date Recorded (YY/MM/DD) -->
                                    <div class="mb-3">
                                        <label for="dateRecorded" class="form-label">Date Recorded (YY/MM/DD)</label>
                                        <input type="date" class="form-control" id="dateRecorded" wire:model=''>
                                    </div>

                                    <!-- Crop (Cassava, Potato, Sweet Potato) -->
                                    <div class="mb-3">
                                        <label for="crop" class="form-label">Crop (Cassava, Potato, Sweet
                                            Potato)</label>
                                        <input type="text" class="form-control" id="crop" wire:model=''>
                                    </div>

                                    <!-- Name of Market -->
                                    <div class="mb-3">
                                        <label for="marketName" class="form-label">Name of Market</label>
                                        <input type="text" class="form-control" id="marketName" wire:model=''>
                                    </div>

                                    <!-- District -->
                                    <div class="mb-3">
                                        <label for="district" class="form-label">District</label>
                                        <input type="text" class="form-control" id="district" wire:model=''>
                                    </div>

                                    <!-- Date of Maximum Sale (YY/MM/DD) -->
                                    <div class="mb-3">
                                        <label for="dateOfMaxSale" class="form-label">Date of Maximum Sale
                                            (YY/MM/DD)</label>
                                        <input type="date" class="form-control" id="dateOfMaxSale"
                                            wire:model=''>
                                    </div>

                                    <!-- Product (Seed, Ware, Value Added Products) -->
                                    <div class="mb-3">
                                        <label for="productType" class="form-label">Product (Seed, Ware, Value Added
                                            Products)</label>
                                        <input type="text" class="form-control" id="productType" wire:model=''>
                                    </div>

                                    <!-- Volume Sold in Previous Period -->
                                    <div class="mb-3">
                                        <label for="volumeSold" class="form-label">Volume Sold in Previous
                                            Period</label>
                                        <input type="number" class="form-control" id="volumeSold" wire:model=''>
                                    </div>

                                    <!-- Financial Value of Sales -->
                                    <div class="mb-3">
                                        <label for="financialValue" class="form-label">Financial Value of
                                            Sales</label>
                                        <input type="number" class="form-control" id="financialValue"
                                            wire:model=''>
                                    </div>


                                    <hr>

                                    <!-- Date Recorded (YY/MM/DD) -->
                                    <div class="mb-3">
                                        <label for="dateRecorded" class="form-label">Date Recorded (YY/MM/DD)</label>
                                        <input type="date" class="form-control" id="dateRecorded" wire:model=''>
                                    </div>

                                    <!-- Crop (Cassava, Potato, Sweet Potato) -->
                                    <div class="mb-3">
                                        <label for="crop" class="form-label">Crop (Cassava, Potato, Sweet
                                            Potato)</label>
                                        <input type="text" class="form-control" id="crop" wire:model=''>
                                    </div>

                                    <!-- Name of Market -->
                                    <div class="mb-3">
                                        <label for="marketName" class="form-label">Name of Market</label>
                                        <input type="text" class="form-control" id="marketName" wire:model=''>
                                    </div>

                                    <!-- Country -->
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" wire:model=''>
                                    </div>

                                    <!-- Date of Maximum Sale (YY/MM/DD) -->
                                    <div class="mb-3">
                                        <label for="dateOfMaxSale" class="form-label">Date of Maximum Sale
                                            (YY/MM/DD)</label>
                                        <input type="date" class="form-control" id="dateOfMaxSale"
                                            wire:model=''>
                                    </div>

                                    <!-- Product (Seed, Ware, Value Added Products) -->
                                    <div class="mb-3">
                                        <label for="productType" class="form-label">Product (Seed, Ware, Value Added
                                            Products)</label>
                                        <input type="text" class="form-control" id="productType" wire:model=''>
                                    </div>

                                    <!-- Volume Sold in Previous Period -->
                                    <div class="mb-3">
                                        <label for="volumeSold" class="form-label">Volume Sold in Previous
                                            Period</label>
                                        <input type="number" class="form-control" id="volumeSold" wire:model=''>
                                    </div>

                                    <!-- Financial Value of Sales -->
                                    <div class="mb-3">
                                        <label for="financialValue" class="form-label">Financial Value of
                                            Sales</label>
                                        <input type="number" class="form-control" id="financialValue"
                                            wire:model=''>
                                    </div>


                                </div>


                            </div>
                        </div>
                        <div class="d-grid col-8 justify-content-center">
                            <button class="btn btn-primary d-none" type="button" wire:click="addInput">Add More
                                +</button>
                            <button class="btn btn-success btn-lg" type="submit">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>



        {{--  <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


            <x-modal id="view-indicator-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div> --}}




    </div>

</div>
