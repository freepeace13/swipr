@props([
    'options' => [],
    'name',
    'id' => null,
    'placeholder' => 'Select an option',
    'searchable' => false,
    'multiple' => false,
    'value' => null,
    'disabled' => false,
])

@php
    $id = $id ?? $name;
    $searchable = filter_var($searchable, FILTER_VALIDATE_BOOLEAN);
    $multiple = filter_var($multiple, FILTER_VALIDATE_BOOLEAN);
    $disabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
    $initialValue = old($name, $value);
    if ($multiple && is_string($initialValue)) {
        $initialValue = $initialValue ? explode(',', $initialValue) : [];
    }
@endphp

<div
    x-data="dropdown({
        options: {{ Js::from($options) }},
        initialValue: {{ Js::from($initialValue) }},
        multiple: {{ Js::from($multiple) }},
        searchable: {{ Js::from($searchable) }},
        placeholder: {{ Js::from($placeholder) }},
        name: {{ Js::from($name) }},
    })"
    x-on:keydown.escape.window="close()"
    class="relative"
    {{ $attributes->only('class') }}
>
    {{-- Trigger --}}
    <button
        type="button"
        x-ref="trigger"
        x-on:click="toggle()"
        x-on:keydown.arrow-down.prevent="open ? highlightNext() : open = true"
        x-on:keydown.arrow-up.prevent="highlightPrev()"
        x-on:keydown.enter.prevent="selectHighlighted()"
        @if($disabled) disabled @endif
        :aria-expanded="open"
        aria-haspopup="listbox"
        id="{{ $id }}"
        class="mt-1 flex w-full items-center justify-between rounded-input border border-gray-300 bg-white px-3 py-2 text-left text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500"
    >
        <span class="block truncate" :class="hasSelection ? 'text-gray-900' : 'text-gray-400'" x-text="displayValue"></span>
        <x-heroicon-m-chevron-down class="ml-2 h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200" x-bind:class="open && 'rotate-180'" />
    </button>

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-on:click.outside="close()"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-dropdown bg-white shadow-dropdown ring-1 ring-black/5"
    >
        {{-- Search input --}}
        @if($searchable)
            <div class="sticky top-0 z-10 bg-white p-2">
                <input
                    type="text"
                    x-ref="search"
                    x-model="search"
                    x-on:keydown.arrow-down.prevent="highlightNext()"
                    x-on:keydown.arrow-up.prevent="highlightPrev()"
                    x-on:keydown.enter.prevent="selectHighlighted()"
                    placeholder="Search..."
                    class="w-full rounded-input border border-gray-300 px-3 py-1.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
            </div>
        @endif

        {{-- Options list --}}
        <ul role="listbox" :aria-multiselectable="multiple" class="py-1">
            <template x-for="(option, index) in filtered" :key="option.value">
                <li
                    role="option"
                    :aria-selected="isSelected(option)"
                    :class="{
                        'bg-brand-600 text-white': highlightedIndex === index,
                        'text-gray-900': highlightedIndex !== index,
                    }"
                    x-on:click="select(option)"
                    x-on:mouseenter="highlightedIndex = index"
                    class="relative cursor-pointer select-none px-3 py-2 text-sm"
                >
                    <span class="block truncate" :class="isSelected(option) && 'font-medium'" x-text="option.label"></span>

                    <span
                        x-show="isSelected(option)"
                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                        :class="highlightedIndex === index ? 'text-white' : 'text-brand-600'"
                    >
                        <x-heroicon-m-check class="h-4 w-4" />
                    </span>
                </li>
            </template>

            <li x-show="filtered.length === 0" class="px-3 py-2 text-sm text-gray-500">
                No results found.
            </li>
        </ul>
    </div>

    {{-- Hidden inputs for form submission --}}
    <template x-if="multiple">
        <template x-for="val in selected" :key="val">
            <input type="hidden" :name="name + '[]'" :value="val">
        </template>
    </template>
    <template x-if="!multiple">
        <input type="hidden" :name="name" :value="selected ?? ''">
    </template>
</div>
