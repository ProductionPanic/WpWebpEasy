<script>
    import { onMount } from "svelte";
    import { fly } from "svelte/transition";
    export let className = "";
    export let duration = 1000;
    export let x = window.innerWidth / 2;
    let x1 = 0;
    let x2 = 0;

    $: x2 = x * -1;
    $: x1 = x ?? 0;
</script>

<div
    class={className}
    in:fly={{ x: x2, duration: duration / 2, delay: duration / 2 }}
    out:fly={{ x: x1, duration: duration / 2, delay: 0 }}
>
    <slot />
</div>
