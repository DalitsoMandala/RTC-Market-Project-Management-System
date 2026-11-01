<x-detail-wrapper>
      <x-table-card :title="'Contractual Agreement Table'" :subtitle="'Contractual Agreement Data'">

                    <livewire:tables.rtc-market.rtc-production-processors-conc-agreement :rowId="$row->id" :wire:key="'conc-agreement-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'Domestic Markets Table'" :subtitle="'Domestic Markets Data'">
                    <livewire:tables.rtc-market.rtc-production-processor-dom-markets :rowId="$row->id" :wire:key="'dom-markets-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'International Markets Table'" :subtitle="'International Markets Data'">
                    <livewire:tables.rtc-market.rtc-production-processor-inter-markets :rowId="$row->id" :wire:key="'inter-markets-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'Market Information Systems Table'" :subtitle="'Market Information Systems Data'">
                    <livewire:tables.rtc-market.rpm-processor-m-i-s :rowId="$row->id" :wire:key="'mis-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'Aggregation Centers Table'" :subtitle="'Aggregation Centers Data'">
                    <livewire:tables.rtc-market.rpm-processor-agg-centers :rowId="$row->id" :wire:key="'agg-centers-' . $row->id" />
                </x-table-card>
</x-detail-wrapper>
