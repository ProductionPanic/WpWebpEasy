<script>
    import PageAnimation from "./lib/components/PageAnimation.svelte";
    import Toggle from "./lib/components/Toggle.svelte";
    import Select from "./lib/components/Select.svelte";
    import { onMount } from "svelte";
    import WebpEasy from "./lib/ts/WebpEasy";
    import Toastify from "toastify-js";
    import { scale, slide } from "svelte/transition";

    let settings = {
        webp_enabled: true,
        compression_enabled: true,
        compression_quality: 80,
        autoconvert_new_images: true,
    };

    let og = null;
    let enable_save = false;

    $: if (settings && og !== null) {
        const _settings = JSON.stringify(settings);
        enable_save = _settings !== og;
    }

    const save_settings = async () => {
        const response = await WebpEasy.setSettings(settings);
        console.log(response);
        if (response.success) {
            og = JSON.stringify(settings);
            Toastify({
                text: "Settings saved!",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "#228B22",
                stopOnFocus: true,
            }).showToast();
        } else {
            Toastify({
                text: "Error saving settings!",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "#B22222",
                stopOnFocus: true,
            }).showToast();
        }
    };

    onMount(() => {
        WebpEasy.getSettings().then((res) => {
            settings = res;
            og = JSON.stringify(res);
        });
    });
</script>

<PageAnimation>
    <div
        class="bg-slate-900 rounded-lg shadow-lg p-4 mt-6 text-white flex flex-col glass_bg"
    >
        <h1 class="text-white text-2xl">Settings</h1>
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-4">
                <Toggle
                    id="webp_enabled"
                    label="Enable webp images"
                    bind:checked={settings.webp_enabled}
                />
                {#if settings.webp_enabled}
                    <Toggle
                        id="compression_enabled"
                        label="Enable compression"
                        bind:checked={settings.compression_enabled}
                    />
                    {#if settings.compression_enabled}
                        <Select
                            id="compression_quality"
                            label="Compression level"
                            bind:value={settings.compression_quality}
                            options={[
                                { value: 90, label: "Light" },
                                { value: 80, label: "Medium", selected: true },
                                { value: 70, label: "Heavy" },
                            ]}
                        />
                    {/if}
                    <Toggle
                        label="Auto convert new images"
                        id="autoconvert_new_images"
                        bind:checked={settings.autoconvert_new_images}
                    />
                {/if}

                {#if enable_save}
                    <button
                        class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded"
                        on:click={save_settings}
                        transition:slide
                    >
                        Save
                    </button>
                {/if}
            </div>
        </div>
    </div>
</PageAnimation>
