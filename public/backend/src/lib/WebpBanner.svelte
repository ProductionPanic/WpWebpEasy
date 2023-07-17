<!-- WebpBanner.svelte -->
<script lang="ts">
    import { onMount } from "svelte";
    import WebpEasy from "./ts/WebpEasy";

    const links = [
        {
            name: "Home",
            url: WebpEasy.links().home,
        },
        {
            name: "Settings",
            url: WebpEasy.links().settings,
        },
    ];

    let activeLine: HTMLSpanElement;
    let nav: HTMLElement;

    function is_active(page: string): boolean {
        const url = new URL(window.location.href);
        const page_name = url.searchParams.get("page");

        const given_url = new URL(page);
        const given_page_name = given_url.searchParams.get("page");
        return page_name === given_page_name;
    }

    async function check_active() {
        await new Promise((resolve) => setTimeout(resolve, 100));
        const url = new URL(window.location.href);
        const page_name = url.searchParams.get("page");
        const active_link = nav.querySelector("a[href*='" + page_name + "']");
        const box = active_link.getBoundingClientRect();
        const parent_box = nav.getBoundingClientRect();
        const width = box.width;
        const left = box.left - parent_box.left;

        activeLine.style.width = `${width}px`;
        activeLine.style.left = `${left}px`;
    }

    // on mount
    onMount(() => {
        check_active();
    });
</script>

<svelte:document on:hashchange={check_active} />

<header
    class="bg-slate-900 text-white p-4 rounded-[5000px] flex justify-between items-center glass_bg"
>
    <div class="flex items-center gap-4">
        <h1 class="text-2xl font-bold text-white">
            <span class="text-rose-600">WebP</span> Easy
        </h1>
    </div>
    <div class="flex items-center gap-4 relative mx-4">
        <nav bind:this={nav} class="relative flex gap-4">
            {#each links as link}
                <a
                    href={link.url}
                    class:active={is_active(link.url)}
                    class="text-white hover:text-gray-200 text-lg"
                >
                    {link.name}
                </a>
            {/each}
        </nav>

        <span class="active-line" bind:this={activeLine} />
    </div>
</header>

<style lang="scss">
    a.active {
        @apply text-gray-200 relative;
    }
    a:not(.active) {
        @apply text-white;
    }
    .active-line {
        @apply bg-rose-600 absolute bottom-0 transition-all duration-300 h-[2px];
    }
</style>
