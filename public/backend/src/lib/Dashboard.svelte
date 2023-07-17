<script>
    import { Stats } from "./ts/Stats";
    import StatsBar from "./StatsBar.svelte";
</script>

<main class="mt-4 mr-2">
    <StatsBar stats={$Stats} />
</main>
