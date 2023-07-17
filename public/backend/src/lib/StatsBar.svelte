<script>
    import Stat from "./Stat.svelte";
    export let stats;

    let titles = {
        total_image_count: "Total image count",
        total_webp_count: "Total webp count",
        total_webp_compressed: "Total webp compressed",
    };
</script>

<div class="gap-4 grid grid-cols-3">
    {#each Object.keys(stats) as stat, i}
        <Stat title={titles[stat]} value={stats[stat]} delay={100 + i * 100} />
    {/each}
</div>
