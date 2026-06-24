@extends('layouts.app')

@section('title', 'Discover')

@section('bodyClass', 'overflow-hidden')
@section('mainClass', 'overflow-hidden')

@push('styles')
<style>
    :root {
        --nav-h: 4rem;
        --feed-h: calc(100dvh - var(--nav-h));
    }
    .feed-scroll {
        height: var(--feed-h);
        scroll-snap-type: y mandatory;
        overflow-y: auto;
        overscroll-behavior-y: contain;
        scrollbar-width: none;
        cursor: grab;
    }
    .feed-scroll.dragging {
        cursor: grabbing;
        scroll-snap-type: none;
        user-select: none;
    }
    .feed-scroll::-webkit-scrollbar {
        display: none;
    }
    .feed-panel {
        height: var(--feed-h);
        scroll-snap-align: start;
    }
</style>
@endpush

@section('content')
    @if($matches->isEmpty())
        <div class="flex items-center justify-center" style="height: var(--feed-h)">
            <div class="rounded-2xl bg-white p-12 text-center shadow">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">No matches yet</h3>
                <p class="mt-1 text-sm text-gray-500">Try broadening your preferences to discover more people.</p>
            </div>
        </div>
    @else
        <div
            class="relative"
            x-data="{
                current: 0,
                total: {{ $matches->count() }},
                nextCursor: '{{ $matches->nextCursor()?->encode() }}',
                loading: false,
                done: {{ $matches->hasMorePages() ? 'false' : 'true' }},
                dragging: false,
                dragStartY: 0,
                dragScrollTop: 0,
                panelHeight() {
                    return this.$refs.scroller?.firstElementChild?.offsetHeight || this.$refs.scroller?.offsetHeight || 1;
                },
                update(e) {
                    if (this.dragging) return;
                    const h = this.panelHeight();
                    this.current = Math.round(e.target.scrollTop / h);
                    if (!this.done && !this.loading && this.current >= this.total - 3) {
                        this.loadMore();
                    }
                },
                go(dir) {
                    const el = this.$refs.scroller;
                    el.scrollTo({ top: (this.current + dir) * this.panelHeight(), behavior: 'smooth' });
                },
                dragStart(e) {
                    if (e.target.closest('a, button')) return;
                    e.preventDefault();
                    this.dragging = true;
                    this.dragStartY = e.clientY;
                    this.dragScrollTop = this.$refs.scroller.scrollTop;
                    this.$refs.scroller.classList.add('dragging');
                },
                dragMove(e) {
                    if (!this.dragging) return;
                    const dy = this.dragStartY - e.clientY;
                    this.$refs.scroller.scrollTop = this.dragScrollTop + dy;
                },
                dragEnd(e) {
                    if (!this.dragging) return;
                    this.dragging = false;
                    this.$refs.scroller.classList.remove('dragging');
                    const dy = this.dragStartY - e.clientY;
                    const threshold = this.panelHeight() * 0.15;
                    if (Math.abs(dy) > threshold) {
                        this.go(dy > 0 ? 1 : -1);
                    } else {
                        this.go(0);
                    }
                    const h = this.panelHeight();
                    this.current = Math.round(this.$refs.scroller.scrollTop / h);
                    if (!this.done && !this.loading && this.current >= this.total - 3) {
                        this.loadMore();
                    }
                },
                async loadMore() {
                    this.loading = true;
                    try {
                        const url = '{{ route('feeds') }}' + '?cursor=' + this.nextCursor;
                        const res = await fetch(url, {
                            headers: {
                                'X-Feed-Page': '1',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });
                        const html = await res.text();
                        const tmp = document.createElement('div');
                        tmp.innerHTML = html;
                        const panels = tmp.querySelectorAll('.feed-panel');
                        panels.forEach(p => this.$refs.scroller.appendChild(p));
                        this.total += panels.length;
                        const meta = tmp.querySelector('meta[name=next-cursor]');
                        if (meta) {
                            this.nextCursor = meta.content;
                        } else {
                            this.done = true;
                        }
                    } finally {
                        this.loading = false;
                    }
                }
            }"
            x-on:keydown.arrow-down.window.prevent="go(1)"
            x-on:keydown.arrow-up.window.prevent="go(-1)"
        >
            {{-- Profile counter --}}
            <div class="pointer-events-none absolute right-4 top-4 z-10">
                <span class="rounded-full bg-black/40 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm" x-text="`${current + 1} / ${total}`"></span>
            </div>

            <div
                class="feed-scroll"
                x-ref="scroller"
                x-on:scroll.passive="update($event)"
                x-on:mousedown="dragStart($event)"
                x-on:mousemove.prevent="dragMove($event)"
                x-on:mouseup="dragEnd($event)"
                x-on:mouseleave="dragEnd($event)"
            >
                @include('pages.feeds-panel')
            </div>
        </div>
    @endif
@endsection
