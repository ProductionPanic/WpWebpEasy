<script>
    import { fly } from "svelte/transition";
    export let stats;

    let container_el;

    let _stats = [];
    $: {
        _stats = [];
        // get object values
        for(let stat in stats) {
            stat = stats[stat]
            _stats.push(stat);
        }
    }
    
    $:if(container_el) {
        container_el.style.setProperty("--width", `${container_el.clientWidth}px`);
        container_el.style.setProperty("--count", `${_stats.length}`)        
    }
</script>

 <div class="stats-container" bind:this={container_el}>
   <div class="stats-container__inner">
    {#each _stats as [ stat, value ], i}
    <div
        class="bg-slate-900 rounded-lg shadow-lg p-4 text-white flex flex-col glass_bg"
        in:fly={{ y: 300, duration: 300, delay: 100 + i * 100 }}
    >
        <h2 class="text-md lg:text-xl font-bold text-rose-600">{stat}</h2>
        <p class="text-4xl font-bold">{value}</p>
    </div>
{/each}
   </div>
</div>

<style lang="scss">
    .stats-container {
        // sizing
        max-width: 100%;
        width: 100%;

        &__inner {        
            @apply grid grid-cols-3 lg:grid-cols-4 gap-4;
        }
    }

</style>
