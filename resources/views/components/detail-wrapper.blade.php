<div
    x-data="{
        w: null,
        init() {
            let el = this.$el.parentElement;
            while (el && !['auto', 'scroll'].includes(getComputedStyle(el).overflowX)) {
                el = el.parentElement;
            }
            if (!el) return;
            const update = () => this.w = el.clientWidth;
            update();
            new ResizeObserver(update).observe(el);
        }
    }"
    class="position-sticky start-0"
    :style="w ? `width:${w}px` : ''"
>
    <div class="bg-soft-secondary p-2">
        <div class="table-responsive">
            {{ $slot }}
        </div>
    </div>
</div>
