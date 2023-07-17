<script lang="ts">
    import Main from "./Main.svelte";
    import Settings from "./Settings.svelte";
    import WebpBanner from "./lib/WebpBanner.svelte";
    import MovingBackgroundShapes from "./lib/components/MovingBackgroundShapes.svelte";

    export let is_settings_page: Boolean = false;
</script>

<div class="webp-easy-wp-admin mt-4 mr-2">
    <WebpBanner />
    {#if !is_settings_page}
        <Main />
    {:else}
        <Settings />
    {/if}
</div>

<MovingBackgroundShapes />

<style lang="scss">
    .webp-easy-wp-admin {
        max-width: 100%;
    }
</style>
