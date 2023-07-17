<script lang="ts">
    import { createEventDispatcher } from "svelte";
    import { slide } from "svelte/transition";

    export let id;
    export let label;
    export let checked;
    let toggle_container;

    const dispatch = createEventDispatcher();

    const toggle = () => {
        dispatch("change", { id, checked: !checked });
    };

    $: id = id ?? Math.random().toString(36).substring(7);

    $: checked = checked ?? false;

    $: {
        dispatch("change", { id, checked });
    }

    const toggle_click = (e) => {
        // check if it is a keyboard event
        const is_keyboard_event = e.type === "keydown";
        if (is_keyboard_event) {
            // check if it is the spacebar
            const is_spacebar = e.key === " ";
            if (!is_spacebar) return;
            checked = !checked;
            return;
        }

        checked = !checked;
    };
</script>

<div class="flex flex-col gap-2" transition:slide>
    <label for={id} class="cursor-pointer">
        {label}
    </label>
    <div class="hidden">
        <input
            type="checkbox"
            {id}
            bind:checked
            on:change={toggle}
            class="hidden"
        />
    </div>

    <div
        class="toggle-container"
        class:checked
        on:click={toggle_click}
        on:keydown={toggle_click}
        tabindex="0"
        role="switch"
        aria-checked={checked}
        bind:this={toggle_container}
    >
        <div class="toggle-rail" />
        <div class="toggle-thumb" />
    </div>
</div>

<style lang="scss">
    .toggle-container {
        width: 50px;
        height: 25px;
        border-radius: 25px;
        position: relative;
        cursor: pointer;
        @apply bg-slate-950;

        .toggle-rail {
            position: absolute;
            top: 2px;
            left: 2px;
            right: 2px;
            bottom: 2px;
            border-radius: 500000px;
            @apply bg-slate-900;
        }

        .toggle-thumb {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 21px;
            height: 21px;
            border-radius: 500000px;
            transition: transform 0.2s ease-in-out,
                background-color 0.2s ease-in-out;
            transform: translateX(0);
            @apply bg-slate-500;
        }

        &.checked {
            .toggle-thumb {
                transform: translateX(25px);

                @apply bg-rose-500;
            }
        }
    }
</style>
