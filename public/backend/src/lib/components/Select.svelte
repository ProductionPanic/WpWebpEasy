<script lang="ts">
    import { createEventDispatcher } from "svelte";
    import { fly, slide } from "svelte/transition";

    export let id;
    export let label;
    export let options;
    export let value;
    let active = false;

    let select_container;

    const dispatch = createEventDispatcher();

    const emit_change = (e) => {
        dispatch("change", value);
    };

    const toggle_list = () => {
        active = !active;
        if (active) {
            select_container.focus();
        }
    };
</script>

<div class="custom-select-component" transition:slide>
    <div class="hidden">
        <select bind:value on:change={emit_change}>
            {#each options as option}
                <option value={option.value}>{option.label}</option>
            {/each}
        </select>
    </div>

    <label for={id} class="cursor-pointer">
        {label}
    </label>
    <div
        class="select-container"
        on:click={toggle_list}
        bind:this={select_container}
        role="listbox"
        tabindex="0"
        on:keypress={(e) => {
            if (e.key === "Enter") {
                toggle_list();
            }
            const escape_keycode = 27;
            console.log(e);
            if (e.keyCode === escape_keycode) {
                active = false;
            }
        }}
    >
        <div class="select-label">
            {#each options as option}
                {#if option.value === value}
                    {option.label}
                {/if}
            {/each}
        </div>
        <div class="select-arrow">
            <div class="arrow-inner" />
        </div>

        {#if active}
            <div
                class="select-list"
                class:active
                in:fly={{ y: -20, duration: 300 }}
                out:fly={{ y: -20, duration: 300 }}
            >
                {#each options as option, i}
                    <div
                        class="select-list-item"
                        role="option"
                        aria-selected={option.value === value}
                        on:click={() => {
                            value = option.value;
                        }}
                        on:keypress={(e) => {
                            if (e.key === "Enter") {
                                value = option.value;
                            }
                        }}
                        tabindex="0"
                    >
                        {option.label}
                    </div>
                {/each}
            </div>
        {/if}
    </div>
</div>

<style lang="scss">
    .custom-select-component {
        position: relative;
        width: auto;
        max-width: 100%;
        height: 100%;
        max-height: 100%;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;

        .hidden {
            display: none;
        }

        .select-container {
            position: relative;
            width: fit-content;
            max-width: 100%;
            min-width: calc(min(100%, 20rem));
            height: 100%;
            max-height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;

            @apply p-2 rounded-md border border-slate-950 bg-slate-900;

            &:focus {
                outline: none;
            }

            .select-label {
                font-size: 1rem;
                font-weight: 500;
            }

            .select-arrow {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 1.2rem;
                height: 0.75rem;

                .arrow-inner {
                    width: 100%;
                    height: 100%;
                    clip-path: polygon(
                        0% 0%,
                        10% 0%,
                        50% 80%,
                        90% 0%,
                        100% 0%,
                        50% 100%
                    );
                    @apply bg-rose-500;
                }
            }
        }

        .select-list {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-width: 100%;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            z-index: 1;
            opacity: 1;
            transition: opacity 0.2s ease-in-out;

            @apply p-2 rounded-md border border-slate-800 bg-slate-950;

            .select-list-item {
                outline: none !important ;
                font-size: 1rem;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 0.2s ease-in-out;

                &:hover {
                    @apply bg-slate-950;
                }

                &:active,
                &:focus,
                &:target {
                    @apply bg-slate-900;
                }
            }
        }
    }
</style>
