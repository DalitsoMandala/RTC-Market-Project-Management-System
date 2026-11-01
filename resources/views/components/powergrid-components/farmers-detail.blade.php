<x-detail-wrapper>
     <x-table-card :title="'Contractual Agreement Table'" :subtitle="'Contractual Agreement Data'">
                    <livewire:tables.rtc-market.rtc-production-farmers-conc-agreement :rowId="$row->id" :wire:key="'conc-agreement-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'Domestic Markets Table'" :subtitle="'Domestic Markets Data'">
                    <livewire:tables.rtc-market.rtc-production-farmers-dom-markets :rowId="$row->id" :wire:key="'dom-markets-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'International Markets Table'" :subtitle="'International Markets Data'">
                    <livewire:tables.rtc-market.rtc-production-farmers-inter-markets :rowId="$row->id" :wire:key="'inter-markets-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'Aggregation Centers Table'" :subtitle="'Aggregation Centers Data'">
                    <livewire:tables.rtc-market.rpm-farmer-agg-centers :rowId="$row->id" :wire:key="'agg-centers-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'MIS Table'" :subtitle="'MIS Data'">
                    <livewire:tables.rtc-market.rpm-farmer-m-i-s :rowId="$row->id" :wire:key="'mis-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'Basic Seed Table'" :subtitle="'Basic Seed Data'">
                    <livewire:tables.rtc-market.rpm-farmer-basic :rowId="$row->id" :wire:key="'basic-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'Certified Seed Table'" :subtitle="'Certified Seed Data'">
                    <livewire:tables.rtc-market.rpm-farmer-certified :rowId="$row->id" :wire:key="'certified-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'Cultivation Table'" :subtitle="'Cultivation Data'">
                    <livewire:tables.rtc-market.rpm-farmer-cultivation :rowId="$row->id" :wire:key="'cultivation-' . $row->id" />
                </x-table-card>
                <x-table-card :title="'Seed Registration Table'" :subtitle="'Seed Registration Data'">
                    <livewire:tables.farmer-seed-registration-table :rowId="$row->id" :wire:key="'seed-registration-' . $row->id" />
                </x-table-card>
</x-detail-wrapper>
