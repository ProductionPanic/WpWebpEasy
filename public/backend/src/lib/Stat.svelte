<script>
    import { fly } from "svelte/transition";
    export let title;
    export let value;
    export let delay = 0;
</script>

<div
    class="bg-slate-900 rounded-lg shadow-lg p-4 text-white flex flex-col glass_bg"
    in:fly={{ y: 300, duration: 300, delay }}
>
    <h2 class="text-md lg:text-xl font-bold text-rose-600">{title}</h2>
    <p class="text-4xl font-bold">{value}</p> 
</div>
