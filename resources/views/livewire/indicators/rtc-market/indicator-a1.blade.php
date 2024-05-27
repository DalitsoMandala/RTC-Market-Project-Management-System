<div>

    <div class="table-responsive" x-data="{
        cassavaCountFarmer: 0,
        potatoCountFarmer: 0,
        swPotatoCountFarmer: 0,
        cassavaCountProcessor: 0,
        potatoCountProcessor: 0,
        swPotatoCountProcessor: 0,
        cassavaCountTrader: 0,
        potatoCountTrader: 0,
        swPotatoCountTrader: 0,
        // cassavaCount: 0,
        // potatoCount: 0,
        // swPotatoCount: 0,
        init() {
            //farmers
            const cropCountFarmer = $store.a1Data.data.cropCountFarmer;
    
            this.cassavaCountFarmer = cropCountFarmer.cassava_count === null ? 0 : Number(cropCountFarmer.cassava_count);
            this.potatoCountFarmer = cropCountFarmer.potato_count === null ? 0 : Number(cropCountFarmer.potato_count);
            this.swPotatoCountFarmer = cropCountFarmer.sw_potato_count === null ? 0 : Number(cropCountFarmer.sw_potato_count);
    
            const cropCountTrader = $store.a1Data.data.cropCountTrader;
            this.cassavaCountTrader = cropCountTrader.cassava_count === null ? 0 : Number(cropCountTrader.cassava_count);
            this.potatoCountTrader = cropCountTrader.potato_count === null ? 0 : Number(cropCountTrader.potato_count);
            this.swPotatoCountTrader = cropCountTrader.sw_potato_count === null ? 0 : Number(cropCountTrader.sw_potato_count);
    
            const cropCountProcessor = $store.a1Data.data.cropCountProcessor;
            this.cassavaCountProcessor = cropCountProcessor.cassava_count === null ? 0 : Number(cropCountProcessor.cassava_count);
            this.potatoCountProcessor = cropCountProcessor.potato_count === null ? 0 : Number(cropCountProcessor.potato_count);
            this.swPotatoCountProcessor = cropCountProcessor.sw_potato_count === null ? 0 : Number(cropCountProcessor.sw_potato_count);
    
        },
    
    
    
    
        calculateCropPercentages(actorType, count) {
    
    
    
            total = this.calculateCropTotal(actorType);
    
            calculation = count / total * 100;
            if (total === 0) {
                return 0; // Return 0 if total is zero
            }
    
            return calculation;
    
    
        },
    
        percentage(count, total) {
            calculation = count / total * 100;
            if (total === 0) {
                return 0; // Return 0 if total is zero
            }
    
            return calculation;
        },
        calculateCropTotal(actorType) {
    
            if (actorType === 'FARMER') {
    
                total = this.cassavaCountFarmer + this.potatoCountFarmer + this.swPotatoCountFarmer;
    
                return parseFloat(total);
            }
            if (actorType === 'PROCESSOR') {
                total = this.cassavaCountProcessor + this.potatoCountProcessor + this.swPotatoCountProcessor;
                return parseFloat(total);
            }
            if (actorType === 'TRADER') {
                total = this.cassavaCountTrader + this.potatoCountTrader + this.swPotatoCountTrader;
                return parseFloat(total);
            }
    
    
    
        },
        grandTotalPercentage(count) {
    
            total = (this.cassavaCountFarmer + this.cassavaCountProcessor + this.cassavaCountTrader) +
                (this.potatoCountFarmer + this.potatoCountProcessor + this.potatoCountTrader) +
                (this.swPotatoCountFarmer + this.swPotatoCountProcessor + this.swPotatoCountTrader);
    
            calculation = count / total * 100;
            if (total === 0) {
                return 0; // Return 0 if total is zero
            }
    
            return calculation;
    
        }
    
    
    
    }">

        <table class="table text-uppercase table-bordered table-hover ">
            <thead>
                <tr>
                    <th style="text-align: center" colspan="9">RTC Actor By Crop </th>
                </tr>
            </thead>
            <thead class="table-light">
                <tr>
                    <th scope="col"></th>
                    <th scope="col" colspan="2">Respondent</th>

                    <th scope="col" colspan="2">Respondent</th>

                    <th scope="col" colspan="2">Respondent</th>

                    <th scope="col" colspan="2"></th>


                </tr>
            </thead>
            <thead class="table-light">
                <tr>
                    <th scope="col"></th>
                    <th scope="col" colspan="2">Farmer</th>

                    <th scope="col" colspan="2">Processor</th>

                    <th scope="col" colspan="2">Trader</th>

                    <th scope="col" colspan="2">Grand Total</th>


                </tr>
            </thead>
            <thead class="table-light">
                <tr>
                    <th scope="col">CROP</th>
                    <th scope="col">VALUE</th>
                    <th scope="col">%</th>
                    <th scope="col">VALUE</th>
                    <th scope="col">%</th>
                    <th scope="col">VALUE</th>
                    <th scope="col">%</th>
                    <th scope="col">VALUE</th>
                    <th scope="col">% (CROP)</th>

                </tr>
            </thead>
            <tbody>


                <tr class="">
                    {{-- Farmer --}}
                    <td scope="row">cassava</td>
                    <td x-text="cassavaCountFarmer"></td>
                    <td x-text="(calculateCropPercentages('FARMER', cassavaCountFarmer)).toFixed()">Item</td>
                    <td x-text="cassavaCountProcessor"></td>
                    <td x-text="(calculateCropPercentages('PROCESSOR', cassavaCountProcessor)).toFixed()"></td>
                    <td x-text="cassavaCountTrader"></td>
                    <td x-text="(calculateCropPercentages('TRADER', cassavaCountTrader)).toFixed()"></td>
                    <td x-text="(cassavaCountFarmer + cassavaCountProcessor + cassavaCountTrader)"></td>
                    <td
                        x-text="grandTotalPercentage((cassavaCountFarmer + cassavaCountProcessor + cassavaCountTrader)).toFixed()">
                    </td>
                </tr>
                <tr class="">
                    <td scope="row">potato</td>
                    <td x-text="potatoCountFarmer"></td>
                    <td x-text="(calculateCropPercentages('FARMER', potatoCountFarmer)).toFixed()"></td>
                    <td x-text="potatoCountProcessor"></td>
                    <td x-text="(calculateCropPercentages('PROCESSOR', potatoCountProcessor)).toFixed()"></td>
                    <td x-text="potatoCountTrader"></td>
                    <td x-text="(calculateCropPercentages('TRADER', potatoCountTrader)).toFixed()"></td>
                    <td x-text="potatoCountFarmer + potatoCountProcessor + potatoCountTrader "></td>
                    <td
                        x-text="grandTotalPercentage((potatoCountFarmer + potatoCountProcessor + potatoCountTrader)).toFixed()">
                    </td>
                </tr>
                <tr class="">
                    <td scope="row">sweet potato</td>
                    <td x-text="swPotatoCountFarmer"></td>
                    <td x-text="(calculateCropPercentages('FARMER', swPotatoCountFarmer)).toFixed()"></td>
                    <td x-text="swPotatoCountProcessor"></td>
                    <td x-text="(calculateCropPercentages('PROCESSOR', swPotatoCountProcessor)).toFixed()"></td>
                    <td x-text="swPotatoCountTrader"></td>
                    <td x-text="(calculateCropPercentages('TRADER', swPotatoCountTrader)).toFixed()"></td>
                    <td x-text="swPotatoCountFarmer + swPotatoCountProcessor + swPotatoCountTrader"></td>
                    <td
                        x-text="grandTotalPercentage((swPotatoCountFarmer + swPotatoCountProcessor + swPotatoCountTrader)).toFixed()">
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <th>Grand Total</th>
                <th x-text="calculateCropTotal('FARMER')"></th>
                <th
                    x-text="(calculateCropPercentages('FARMER', cassavaCountFarmer)) + (calculateCropPercentages('FARMER', potatoCountFarmer))+ (calculateCropPercentages('FARMER', swPotatoCountFarmer))">
                </th>
                <th x-text="calculateCropTotal('PROCESSOR')"></th>
                <th
                    x-text="(calculateCropPercentages('PROCESSOR', cassavaCountProcessor)) + (calculateCropPercentages('PROCESSOR', potatoCountProcessor))+ (calculateCropPercentages('PROCESSOR', swPotatoCountProcessor))">
                </th>
                <th x-text="calculateCropTotal('TRADER')"></th>
                <th
                    x-text="(calculateCropPercentages('TRADER', cassavaCountTrader)) + (calculateCropPercentages('TRADER', potatoCountTrader))+ (calculateCropPercentages('TRADER', swPotatoCountTrader))">
                </th>
                <th
                    x-text="(cassavaCountFarmer + cassavaCountProcessor + cassavaCountTrader)+
                    (potatoCountFarmer + potatoCountProcessor + potatoCountTrader) +
                     (swPotatoCountFarmer + swPotatoCountProcessor + swPotatoCountTrader)">
                </th>
                <th
                    x-text="(grandTotalPercentage((cassavaCountFarmer + cassavaCountProcessor + cassavaCountTrader))+
                grandTotalPercentage((potatoCountFarmer + potatoCountProcessor + potatoCountTrader))+
                grandTotalPercentage((swPotatoCountFarmer + swPotatoCountProcessor + swPotatoCountTrader))).toFixed()
                ">

                </th>

            </tfoot>
        </table>
    </div>

    @script
        <script>
            Alpine.store('a1Data', {
                data: @json($data)

            })
        </script>
    @endscript
</div>
