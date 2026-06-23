export default ({ options, initialValue, multiple, searchable, placeholder, name }) => ({
    open: false,
    search: '',
    selected: multiple ? (Array.isArray(initialValue) ? initialValue : []) : (initialValue ?? null),
    options,
    multiple,
    searchable,
    placeholder,
    name,
    highlightedIndex: -1,

    get filtered() {
        if (!this.search) return this.options;
        const term = this.search.toLowerCase();
        return this.options.filter(o => o.label.toLowerCase().includes(term));
    },

    get hasSelection() {
        return this.multiple ? this.selected.length > 0 : this.selected !== null && this.selected !== '';
    },

    get displayValue() {
        if (!this.hasSelection) return this.placeholder;
        if (this.multiple) {
            return this.selected
                .map(v => this.options.find(o => o.value === v)?.label)
                .filter(Boolean)
                .join(', ');
        }
        return this.options.find(o => o.value === this.selected)?.label ?? this.placeholder;
    },

    toggle() {
        this.open ? this.close() : this.openDropdown();
    },

    openDropdown() {
        this.open = true;
        this.highlightedIndex = -1;
        this.search = '';
        this.$nextTick(() => {
            if (this.searchable && this.$refs.search) {
                this.$refs.search.focus();
            }
        });
    },

    close() {
        this.open = false;
        this.search = '';
        this.highlightedIndex = -1;
    },

    select(option) {
        if (this.multiple) {
            const idx = this.selected.indexOf(option.value);
            if (idx === -1) {
                this.selected.push(option.value);
            } else {
                this.selected.splice(idx, 1);
            }
        } else {
            this.selected = option.value;
            this.close();
            this.$refs.trigger.focus();
        }
    },

    isSelected(option) {
        return this.multiple
            ? this.selected.includes(option.value)
            : this.selected === option.value;
    },

    highlightNext() {
        this.highlightedIndex = (this.highlightedIndex + 1) % this.filtered.length;
    },

    highlightPrev() {
        this.highlightedIndex = this.highlightedIndex <= 0
            ? this.filtered.length - 1
            : this.highlightedIndex - 1;
    },

    selectHighlighted() {
        if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filtered.length) {
            this.select(this.filtered[this.highlightedIndex]);
        }
    },
});
