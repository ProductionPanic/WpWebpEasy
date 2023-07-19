<script>
    import { onMount } from "svelte";
    import Dashboard from "./lib/Dashboard.svelte";
    import PageAnimation from "./lib/components/PageAnimation.svelte";
    import AppScreen from './lib/AppScreen/AppScreen.svelte';
</script>

<PageAnimation>
    <Dashboard />
    <AppScreen />
</PageAnimation>
